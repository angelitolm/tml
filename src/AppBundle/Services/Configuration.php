<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\ConfigurationRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Configuration extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Configuration';
    protected $entityShortcutName = 'AppBundle:Configuration';

    /**
     * @return ConfigurationRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param integer $id
     *
     * @return \AppBundle\Entity\Configuration
     */
    public function getEntity($id = null)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param $field
     * @return null|\AppBundle\Entity\Configuration
     */
    public function findByField($field) {
        return $this->getRepository()->findOneBy(array('field'=>$field));
    }

    /**
     * @param \AppBundle\Entity\Configuration $entity
     */
    public function update(\AppBundle\Entity\Configuration $entity)
    {
        $this->getRepository()->update($entity);
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