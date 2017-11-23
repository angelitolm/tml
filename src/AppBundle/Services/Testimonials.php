<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\CommentRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Testimonials extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Comment';
    protected $entityShortcutName = 'AppBundle:Comment';

    /**
     * @return CommentRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Comment
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\Comment $entity
     */
    public function update(\AppBundle\Entity\Comment $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Comment $entity
     */
    public function remove(\AppBundle\Entity\Comment $entity)
    {
        $this->getRepository()->remove($entity);
    }
}