<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\CourseRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Course extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Course';
    protected $entityShortcutName = 'AppBundle:Course';

    /**
     * @return CourseRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Course
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\Course $entity
     */
    public function update(\AppBundle\Entity\Course $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Course $entity
     */
    public function remove(\AppBundle\Entity\Course $entity)
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
     * @param $type
     * @return array
     */
    public function getCourseByType($type) {
        return $this->getRepository()->findBy(array('type'=>$type));
    }
}