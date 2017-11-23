<?php

namespace AppBundle\Services;

use AppBundle\Entity\Repositories\UserMessageRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;
use FOS\UserBundle\Model\UserInterface;

class UserMessage extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\UserMessage';
    protected $entityShortcutName = 'AppBundle:UserMessage';

    /**
     * @return UserMessageRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\UserMessage
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\UserMessage $entity
     */
    public function update(\AppBundle\Entity\UserMessage $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\UserMessage $entity
     */
    public function remove(\AppBundle\Entity\UserMessage $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $user
     * @param int $start
     * @param int $limit
     * @param array $order
     * @param string $filter
     * @return array
     */
    public function getDatableElement($user, $start = 0, $limit = 10, $order = array(), $filter = '')
    {
        return $this->getRepository()->datableElement($user, $start, $limit, $order, $filter);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getTotalElement($user)
    {
        return $this->getRepository()->getCountTotal($user);
    }

    /**
     * @param $user
     * @param string $filter
     * @return mixed
     */
    public function getTotalFilter($user, $filter = '')
    {
        return $this->getRepository()->getFilterTotal($user, $filter);
    }

    /**
     * @param UserInterface $user
     * @param $from
     * @param $subject
     * @param $message
     */
    public function registerMessage(UserInterface $user, $from, $subject, $message) {
        $entity = $this->getEntity();
        $entity->setUser($user);
        $entity->setSend($from);
        $entity->setSubject($subject);
        $entity->setMessage($message);
        $this->update($entity);
    }
}