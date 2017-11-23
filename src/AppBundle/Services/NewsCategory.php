<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\NewsCategoryRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class NewsCategory extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\NewsCategory';
    protected $entityShortcutName = 'AppBundle:NewsCategory';

    /**
     * @return NewsCategoryRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\NewsCategory
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\NewsCategory $entity
     */
    public function update(\AppBundle\Entity\NewsCategory $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\NewsCategory $entity
     */
    public function remove(\AppBundle\Entity\NewsCategory $entity)
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
}