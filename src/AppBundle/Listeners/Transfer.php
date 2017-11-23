<?php

namespace AppBundle\Listeners;


use AppBundle\AppEvents;
use AppBundle\Services\Configuration;
use AppBundle\Services\UserMessage;
use AppBundle\Services\Transfer as TransferService;
use AppBundle\Entity\Transfer as TransferEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Twig\UtilesExtension;

class Transfer implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            AppEvents::TRANSFER_INITIALIZE => 'onTransferInitialize',
            AppEvents::TRANSFER_COMPLETED => 'onTransferCompleted'
        );
    }

    private $userMessage;
    private $translator;
    private $configuration;
    private $userProfile;
    private $userTransfer;

    public function __construct(UserMessage $userMessage, Configuration $configuration, \AppBundle\Services\Profile $userProfile,  TranslatorInterface $translator, TransferService $transfer){
        $this->userMessage = $userMessage;
        $this->configuration = $configuration;
        $this->translator = $translator;
        $this->userProfile = $userProfile;
        $this->userTransfer = $transfer;
    }

    public function onTransferInitialize(\AppBundle\Event\Transfer $event) {
        $this->userMessage->registerMessage(
            $event->getUser(),
            $this->translator->trans('message.admin',array(),'messages'),
            $this->translator->trans('message.transfer.title',array(),'messages'),
            $this->translator->trans('message.transfer.message',array('%amount%'=>$event->getAmount()),'messages')
        );
    }

    public function onTransferCompleted(\AppBundle\Event\Transfer $event) {
        $user = $event->getUser();
        if(null !== $user->getActiveProfile() && $user->getActiveProfile()->getDemo()) {
            $transfers = $user->getActiveTransfers();
            $amountTotal = 0;
            foreach ($transfers as $transfer) {
                if(TransferEntity::$DEPOSIT ==  $transfer->getTransferType()) {
                    $amountTotal += $transfer->getAmount();
                } /*else {
                    $amountTotal -= $transfer->getAmount();
                }*/
            }

            if(0 < $amountTotal) {
                if($user->hasRole('ROLE_PROFESSIONAL')) {
                    $membershipAmount = (integer)$this->configuration->findByField('professional_price')->getValue();
                    $sponsorComition = (integer)$this->configuration->findByField('third_comition')->getValue();
                } elseif ($user->hasRole('ROLE_SUPERIOR')) {
                    $membershipAmount = (integer)$this->configuration->findByField('superior_price')->getValue();
                    $sponsorComition = (integer)$this->configuration->findByField('secound_comition')->getValue();
                } else {
                    $membershipAmount = (integer)$this->configuration->findByField('basic_price')->getValue();
                    $sponsorComition = (integer)$this->configuration->findByField('first_comition')->getValue();
                }

                if($membershipAmount <= $amountTotal) {
                    $profile = $user->getActiveProfile();
                    $profile->setDemo(false);
                    $this->userProfile->update($profile);

                    if(null !== $profile->getSponsor()) {
                        $sponsor = $profile->getSponsor();
                        $sponsor->increaseCredit($sponsorComition);
                        $this->userProfile->update($sponsor);

                        $this->userTransfer->register(
                          $sponsor->getUser(),$sponsorComition,date('YmdHis'), TransferEntity::$DEPOSIT
                        );
                    }
                }
            }
        }
    }
}