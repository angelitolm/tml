<?php

namespace AppBundle\Services;

use AppBundle\AppEvents;
use AppBundle\Entity\Repositories\TransferRepository;
use Epic\ServicesInjectionBundle\Services\AbstractServices;

class Transfer extends AbstractServices
{
    protected $entityName = '\AppBundle\Entity\Transfer';
    protected $entityShortcutName = 'AppBundle:Transfer';

    /**
     * @return TransferRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param null|integer id
     *
     * @return \AppBundle\Entity\Transfer
     */
    public function getEntity($id = null)
    {
        if(is_integer($id)) {
            return $this->getRepository()->find($id);
        }
        return new $this->entityName();
    }

    /**
     * @param \AppBundle\Entity\Transfer $entity
     */
    public function update(\AppBundle\Entity\Transfer $entity)
    {
        $this->getRepository()->update($entity);
    }

    /**
     * @param \AppBundle\Entity\Transfer $entity
     */
    public function remove(\AppBundle\Entity\Transfer $entity)
    {
        $this->getRepository()->remove($entity);
    }

    /**
     * @param $userId
     * @param int $limit
     * @return array
     */
    public function getLatestTransfer($userId, $limit = 10) {
        return $this->getRepository()->getLatestTransfer($userId, $limit);
    }

    /**
     * @param $user
     * @param $start
     * @param $end
     * @param bool|true $active
     * @return int
     */
    public function getTotalForRange($user, $start, $end, $active = true) {
        return $this->getRepository()->getTotalForRange($user, $start, $end, $active);
    }

    /**
     * @param $userId
     */
    public function inactiveAllTransfer($userId) {
        $this->getRepository()->inactiveAllTransfer($userId);
    }

    public function register($user, $amount, $code, $type, $description = "") {
        $event = new \AppBundle\Event\Transfer($user, $amount, $type);
        $this->getEventDispatcher()->dispatch(AppEvents::TRANSFER_INITIALIZE, $event);

        $transfer = $this->getEntity();
        $transfer->setUser($user);
        $transfer->setAmount($amount);
        $transfer->setCode($code);
        $transfer->setTransferType($type);
        $transfer->setDescription($description);
        $this->update($transfer);

        $event = new \AppBundle\Event\Transfer($user, $amount, $type);
        $this->getEventDispatcher()->dispatch(AppEvents::TRANSFER_COMPLETED, $event);
    }
}