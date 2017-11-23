<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\ComemberRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Comember extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Comember';
    protected $entityShortcutName = 'AppBundle:Comember';

    /**
     * @return ComemberRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Comember
     */
    public function getEntity($id = null)
    {
        if (is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param $userId
    //     * @param $level
     * @return \AppBundle\Entity\Comember|null
     */
    public function getByUserId($userId)
    {
        return $this->getRepository()->findOneBy(array('user' => $userId));
    }

    /**
     * @param \AppBundle\Entity\Comember $entity
     */
    public function update(\AppBundle\Entity\Comember $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Comember $entity
     */
    public function remove(\AppBundle\Entity\Comember $entity)
    {
        $this->getRepository()->remove($entity);
    }

}