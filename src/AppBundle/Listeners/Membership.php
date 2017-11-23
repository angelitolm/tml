<?php

namespace AppBundle\Listeners;

use AppBundle\AppEvents;
use AppBundle\Entity\Profile as ProfileEntity;
use AppBundle\Services\Configuration;
use AppBundle\Services\GuestCode;
use AppBundle\Services\Profile as ProfileService;
use AppBundle\Event\MembershipBuy;
use AppBundle\Services\Log;
use AppBundle\Services\Transfer;
use AppBundle\Services\UserMessage;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Membership implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            AppEvents::MEMBERSHIP_BUY_VALID => 'onMembershipBuyValid',
            AppEvents::MEMBERSHIP_BUY_COMPLETED => 'onMembershipBuyCompleted'
        );
    }

    private $userManager;
    private $userProfile;
    private $userMessage;
    private $userTransfer;
    private $translator;
    private $logManager;
    private $guestCodeManager;
    private $configurationManager;
    private $hackCode;

    public function __construct(UserManagerInterface $userManager,ProfileService  $profile, UserMessage $message,
                                Transfer $transfer, TranslatorInterface $translator,Log $logManager,GuestCode $guest,
                                Configuration $configuration, $hackCode) {
        $this->userManager = $userManager;
        $this->userProfile = $profile;
        $this->userMessage = $message;
        $this->userTransfer = $transfer;
        $this->translator = $translator;
        $this->logManager = $logManager;
        $this->guestCodeManager = $guest;
        $this->configurationManager = $configuration;
        $this->hackCode = $hackCode;
    }

    public function onMembershipBuyValid(MembershipBuy $event) {
        $profile = $event->getUser()->getActiveProfile();
        $credit = 0;
        $oldProfile = null;
        if(null !== $profile) {
            $oldProfile = $profile->getId();
            $credit = $profile->close();
            $this->userProfile->update($profile);
            if($profile->getDemo() && null !== $profile->getSponsor()) {
                if(ProfileEntity::PROFILE_PROFESSIONAL == $profile->getType()) {
                    $sponsorComition = (integer)$this->configurationManager->findByField('third_comition')->getValue();
                } elseif (ProfileEntity::PROFILE_SUPERIOR == $profile->getType()) {
                    $sponsorComition = (integer)$this->configurationManager->findByField('secound_comition')->getValue();
                } elseif (ProfileEntity::PROFILE_BASIC == $profile->getType()) {
                    $sponsorComition = (integer)$this->configurationManager->findByField('first_comition')->getValue();
                }

                $sponsor = $profile->getSponsor();
                $sponsor->increaseCredit($sponsorComition);
                $this->userProfile->update($sponsor);
            }
        }

        $credit = 2 == $event->getMethod() ? $credit - $event->getAmount() : $credit;
        $profile = $this->userProfile->getEntity();
        $profile->setActive(true);
        $profile->setBlocked(false);
        $profile->setDemo(true);
        $profile->setRole('ROLE_'.strtoupper($event->getMembership()));
        $profile->setCredit($credit);
        $profile->setUser($event->getUser());
        if($this->hackCode != $event->getCode()) {
            $sponsor = $this->userManager->findUserBy(array('code'=>$event->getCode()))->getActiveProfile();
            $profile->setSponsor($sponsor);
        }
        switch($event->getMembership()) {
            case 'professional':
                $profile->setType(ProfileEntity::PROFILE_PROFESSIONAL);
                break;
            case 'superior':
                $profile->setType(ProfileEntity::PROFILE_SUPERIOR);
                break;
            case 'basic':
                $profile->setType(ProfileEntity::PROFILE_BASIC);
                break;
            default:
                $profile->setType(ProfileEntity::PROFILE_GUEST);
        }
        $this->userProfile->update($profile);

        $this->userProfile->updateChildrenProfile($profile->getId(), $oldProfile);

        $this->userMessage->registerMessage(
            $event->getUser(),
            $this->translator->trans('message.admin',array(),'messages'),
            $this->translator->trans('message.profile_created.title',array(),'messages'),
            $this->translator->trans('message.profile_created.message',array('%profile%'=>strtoupper($event->getMembership())),'messages')
        );
    }

    public function onMembershipBuyCompleted(MembershipBuy $event) {
        $this->userTransfer->register(
            $event->getUser(),
            $event->getAmount(),
            $event->getToken(),
            \AppBundle\Entity\Transfer::$EXTRACTION,
            $this->translator->trans('app.transfer.buy_membership_msg',array('%membership%'=>$event->getMembership()),'backend')
        );

        if($this->hackCode != $event->getCode()) {
            $sponsor = $this->userManager->findUserBy(array('code'=>$event->getCode()));
            $this->userMessage->registerMessage(
                $sponsor,
                $this->translator->trans('message.admin',array(),'messages'),
                $this->translator->trans('message.sponsor_user_used_code.title',array(),'messages'),
                $this->translator->trans('message.sponsor_user_used_code.message',array('%user%'=>$event->getUser()->getUsername(),'%code%'=>$event->getCode()),'messages')
            );
        }

        $this->logManager->registerLog(
            sprintf('The user %s has buyer a %s Membership', $event->getUser()->getUsername(), $event->getMembership())
        );

        $this->guestCodeManager->generateCodeByUser($event->getUser());
    }
}