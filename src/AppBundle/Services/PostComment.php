<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\PostCommentRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class PostComment extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\PostComment';
    protected $entityShortcutName = 'AppBundle:PostComment';

    /**
     * @return PostCommentRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\PostComment
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\PostComment $entity
     */
    public function update(\AppBundle\Entity\PostComment $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\PostComment $entity
     */
    public function remove(\AppBundle\Entity\PostComment $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $post
     * @param int $start
     * @param int $limit
     * @param array $order
     * @param string $filter
     * @return array
     */
    public function getDatableElement($post, $start = 0, $limit = 10, $order = array(), $filter = '')
    {
        return $this->getRepository()->datableElement($post, $start, $limit, $order, $filter);
    }

    /**
     * @param $post
     * @return mixed
     */
    public function getTotalElement($post)
    {
        return $this->getRepository()->getCountTotal($post);
    }

    /**
     * @param $post
     * @param string $filter
     * @return mixed
     */
    public function getTotalFilter($post, $filter = '')
    {
        return $this->getRepository()->getFilterTotal($post, $filter);
    }
}