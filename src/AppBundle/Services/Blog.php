<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\BlogRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Blog extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Blog';
    protected $entityShortcutName = 'AppBundle:Blog';

    /**
     * @return BlogRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Blog
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\Blog $entity
     */
    public function update(\AppBundle\Entity\Blog $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Blog $entity
     */
    public function remove(\AppBundle\Entity\Blog $entity)
    {
        $this->getRepository()->remove($entity);
    }
}