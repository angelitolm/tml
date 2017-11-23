<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\TestCoursesRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class TestCourses extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\TestCourses';
    protected $entityShortcutName = 'AppBundle:TestCourses';

    /**
     * @return TestCoursesRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\TestCourses
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param $userId
     * @param $level
     * @return \AppBundle\Entity\TestCourses|null
     */
    public function getByUserLevel($userId, $level) {
        return  $this->getRepository()->findOneBy(array('user'=>$userId,'level'=>$level));
    }

    /**
     * @param \AppBundle\Entity\TestCourses $entity
     */
    public function update(\AppBundle\Entity\TestCourses $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\TestCourses $entity
     */
    public function remove(\AppBundle\Entity\TestCourses $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $userId
     * @return array
     */
    public function getUserCourses($userId) {
        return $this->getRepository()->getUserCourses($userId);
    }

}