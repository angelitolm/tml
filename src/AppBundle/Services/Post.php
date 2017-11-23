<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\PostRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Post extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Post';
    protected $entityShortcutName = 'AppBundle:Post';

    /**
     * @return PostRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Post
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\Post $entity
     */
    public function update(\AppBundle\Entity\Post $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Post $entity
     */
    public function remove(\AppBundle\Entity\Post $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $blog
     * @param int $start
     * @param int $limit
     * @param array $order
     * @param string $filter
     * @return array
     */
    public function getDatableElement($blog, $start = 0, $limit = 10, $order = array(), $filter = '')
    {
        return $this->getRepository()->datableElement($blog, $start, $limit, $order, $filter);
    }

    /**
     * @param $blog
     * @return mixed
     */
    public function getTotalElement($blog)
    {
        return $this->getRepository()->getCountTotal($blog);
    }

    /**
     * @param $blog
     * @param string $filter
     * @return mixed
     */
    public function getTotalFilter($blog, $filter = '')
    {
        return $this->getRepository()->getFilterTotal($blog, $filter);
    }
}