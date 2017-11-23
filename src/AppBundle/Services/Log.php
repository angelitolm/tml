<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\LogRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Log extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Log';
    protected $entityShortcutName = 'AppBundle:Log';

    /**
     * @return LogRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Log
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\Log $entity
     */
    public function update(\AppBundle\Entity\Log $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Log $entity
     */
    public function remove(\AppBundle\Entity\Log $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $message
     * @param int $type
     */
    public function registerLog($message, $type = \AppBundle\Entity\Log::STATUS_INFO) {
        $entity = $this->getEntity();
        $entity->setMessage($message);
        $entity->setType($type);
        $this->update($entity);
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getLatestLog($limit = 10) {
        return $this->getRepository()->getLatestLog($limit);
    }

}