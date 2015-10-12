<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\TransferRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Transfer extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Transfer';
    protected $entityShortcutName = 'AppBundle:Transfer';

    /**
     * @return TransferRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Transfer
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\Transfer $entity
     */
    public function update(\AppBundle\Entity\Transfer $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Transfer $entity
     */
    public function remove(\AppBundle\Entity\Transfer $entity)
    {
        $this->getRepository()->remove($entity);
    }
}