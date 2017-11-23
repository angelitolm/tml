<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\NewsRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class News extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\News';
    protected $entityShortcutName = 'AppBundle:News';

    /**
     * @return NewsRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\News
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\News $entity
     */
    public function update(\AppBundle\Entity\News $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\News $entity
     */
    public function remove(\AppBundle\Entity\News $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param int $start
     * @param int $limit
     * @param array $order
     * @param string $filter
     * @return array
     */
    public function getDatableElement($start = 0, $limit = 10, $order = array(), $filter = '')
    {
        return $this->getRepository()->datableElement($start, $limit, $order, $filter);
    }

    /**
     *
     * @return mixed
     */
    public function getTotalElement()
    {
        return $this->getRepository()->getCountTotal();
    }

    /**
     *
     * @param string $filter
     * @return mixed
     */
    public function getTotalFilter($filter = '')
    {
        return $this->getRepository()->getFilterTotal($filter);
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getLatestNews($limit = 10) {
        return $this->getRepository()->getLatestNews($limit);
    }
}