<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\ProfileMessageRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class ProfileMessage extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\ProfileMessage';
    protected $entityShortcutName = 'AppBundle:ProfileMessage';

    /**
     * @return ProfileMessageRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\ProfileMessage
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }


    /**
     * @param \AppBundle\Entity\ProfileMessage $entity
     */
    public function update(\AppBundle\Entity\ProfileMessage $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\ProfileMessage $entity
     */
    public function remove(\AppBundle\Entity\ProfileMessage $entity)
    {
        $this->getRepository()->remove($entity);
    }

}