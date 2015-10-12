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
}