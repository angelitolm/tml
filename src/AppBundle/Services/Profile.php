<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\ProfileRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Profile extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Profile';
    protected $entityShortcutName = 'AppBundle:Profile';

    /**
     * @return ProfileRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Profile
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }


    /**
     * @param \AppBundle\Entity\Profile $entity
     */
    public function update(\AppBundle\Entity\Profile $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Profile $entity
     */
    public function remove(\AppBundle\Entity\Profile $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $userId
     * @return null|\AppBundle\Entity\Profile
     */
    public function getActiveByUser($userId) {
        return $this->getRepository()->findOneBy(array('user'=>$userId,'active'=>true,'blocked'=>false));
    }

    /**
     * @param $sponsor
     * @param null $start
     * @param null $end
     * @return int
     */
    public function countRegisterBySponsor($sponsor, $start = null, $end = null) {
        if(null == $start) {
            $start = new \DateTime();
            $start->setDate($start->format('Y'),$start->format('m'),1);
            $start->setTime(0,0,0);
        }
        if(null == $end) {
            $end = new \DateTime();
        }
        if($start instanceof \DateTime) {
            $start = $start->format('Y-m-d H:i:s');
        }
        if($end instanceof \DateTime) {
            $end = $end->format('Y-m-d H:i:s');
        }
        $counter = $this->getRepository()->countRegisterByRange($sponsor, $start, $end);
        if(empty($counter)) return 0;
        $counter = (integer)$counter[0][1];

        return $counter;
    }

    /**
     * @param $newProfile
     * @param null $oldProfile
     * @return bool
     */
    public function updateChildrenProfile($newProfile, $oldProfile= null) {
        if(null !== $oldProfile) {
            return $this->getRepository()->updateChildrenSponsor($newProfile, $oldProfile);
        }
        return false;
    }
}