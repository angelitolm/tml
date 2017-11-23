<?php

namespace AppBundle\Services;

use AppBundle\Entity\Profile as EntityProfile;
use AppBundle\Entity\Repositories\GuestCodeRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class GuestCode extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\GuestCode';
    protected $entityShortcutName = 'AppBundle:GuestCode';
    private $userManager;
    private $configurationManager;
    private $profileManager;

    /**
     * @return Profile
     */
    public function getProfileManager()
    {
        return $this->profileManager;
    }

    /**
     * @param Profile $profileManager
     */
    public function setProfileManager($profileManager)
    {
        $this->profileManager = $profileManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return Configuration
     */
    public function getConfigurationManager()
    {
        return $this->configurationManager;
    }

    /**
     * @param Configuration $configurationManager
     */
    public function setConfigurationManager(Configuration $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @return GuestCodeRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\GuestCode
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param $code
     * @return null|\AppBundle\Entity\GuestCode
     */
    public function findByCode($code) {
        return $this->getRepository()->findOneBy(array('code'=>$code));
    }

    /**
     * @param \AppBundle\Entity\GuestCode $entity
     */
    public function update(\AppBundle\Entity\GuestCode $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\GuestCode $entity
     */
    public function remove(\AppBundle\Entity\GuestCode $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $code
     * @return bool
     */
    public function removeByCode($code) {
        $guestCode = $this->findByCode($code);
        if(null !== $guestCode) {
            $this->remove($guestCode);
            return true;
        }
        return false;
    }

    /**
     * @param $code
     * @return \AppBundle\Entity\GuestCode|null
     */
    public function findValidCode($code) {
        return $this->getRepository()->findValidCode($code);
    }

    /**
     * @param int $userId
     */
    public function clearAllByUser($userId) {
        $this->getRepository()->clearAllByUser($userId);
    }

    public function generateCodeByUser(UserInterface $user) {
        if(!$user->hasRole('ROLE_ADMIN') && !$user->hasRole('ROLE_GUEST')) {
            $limit = 0;
            $profile = $this->getProfileManager()->getActiveByUser($user->getId());
            if(null == $profile) return;
            switch($profile->getType()) {
                case EntityProfile::PROFILE_BASIC:
                    $limit = (integer)$this->getConfigurationManager()->findByField('guest_basic_code')->getValue();
                    break;
                case EntityProfile::PROFILE_SUPERIOR:
                    $limit = (integer)$this->getConfigurationManager()->findByField('guest_superior_code')->getValue();
                    break;
                case EntityProfile::PROFILE_PROFESSIONAL:
                    $limit = (integer)$this->getConfigurationManager()->findByField('guest_professional_code')->getValue();
                    break;
                default:
                    $limit = 0;
            }
            $this->clearAllByUser($user->getId());
            $code = $user->getCode();
            for($i = 0; $i < $limit; $i++) {
                $codeNew = $code.'-'.mt_rand(0,1000);
                $guestCode = $this->getEntity();
                $guestCode->setCode($codeNew);
                $guestCode->setUser($user);
                $guestCode->setActive(true);
                $this->update($guestCode);
            }
            $user->setCodeGenerated(new \DateTime());
            $this->getUserManager()->updateUser($user);
        }
    }
}