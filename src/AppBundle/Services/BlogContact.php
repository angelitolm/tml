<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\BlogContactRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class BlogContact extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\BlogContact';
    protected $entityShortcutName = 'AppBundle:BlogContact';

    /**
     * @return BlogContactRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\BlogContact
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\BlogContact $entity
     */
    public function update(\AppBundle\Entity\BlogContact $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\BlogContact $entity
     */
    public function remove(\AppBundle\Entity\BlogContact $entity)
    {
        $this->getRepository()->remove($entity);
    }
}