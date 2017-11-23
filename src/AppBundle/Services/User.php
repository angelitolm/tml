<?php

namespace AppBundle\Services;

use AppBundle\Entity\Profile as EntityProfile;
use FOS\UserBundle\Doctrine\UserManager;

class User extends UserManager
{
    /**
     * @param int $start
     * @param int $limit
     * @param array $order
     * @param string $filter
     * @return array
     */
    public function getDatableElement($start = 0, $limit = 10, $order = array(), $filter = '')
    {
        return $this->repository->datableElement($start, $limit, $order, $filter);
    }

    /**
     *
     * @return mixed
     */
    public function getTotalElement()
    {
        return $this->repository->getCountTotal();
    }

    /**
     *
     * @param string $filter
     * @return mixed
     */
    public function getTotalFilter($filter = '')
    {
        return $this->repository->getFilterTotal($filter);
    }

    /**
     * @param int $role
     * @return mixed
     */
    public function getAllByRole($role = EntityProfile::PROFILE_BASIC) {
        return $this->repository->getAllByRole($role);
    }

    /**
     * @param int $interval
     * @return mixed
     */
    public function getAllToGeneratedCode($interval = 15) {
        return $this->repository->getAllToGenerateCode($interval);
    }

    /**
     * @param bool $onlyGuest
     * @return mixed
     */
    public function getRegisterToday($onlyGuest = false) {
        $time = new \DateTime();
        $end = $time->format('Y-m-d H:i:s');
        $time->setTime(0,0,0);
        $start = $time->format('Y-m-d H:i:s');

        return $this->repository->getUserRegisterInRange($start, $end, $onlyGuest);
    }

    /**
     * @param bool $onlyGuest
     * @return mixed
     */
    public function getRegisterMonth($onlyGuest = false) {
        $time = new \DateTime();
        $end = $time->format('Y-m-d H:i:s');
        $time->setTime(0,0,0);
        $time->setDate($time->format('Y'), $time->format('m'), 1);
        $start = $time->format('Y-m-d H:i:s');

        return $this->repository->getUserRegisterInRange($start, $end, $onlyGuest);
    }

    /**
     * @param bool|false $onlyGuest
     * @return mixed
     */
    public function getRegisterWeek($onlyGuest = false) {
        $time = new \DateTime();
        $end = $time->format('Y-m-d H:i:s');
        $time->setTime(0,0,0);
        $dayWeek = $time->format('w');
        $time->sub(new \DateInterval('P'.$dayWeek.'D'));
        $start = $time->format('Y-m-d H:i:s');

        return $this->repository->getUserRegisterInRange($start, $end, $onlyGuest);
    }

    /**
     * @param int $start
     * @param int $limit
     * @param string $filter
     * @return mixed
     */
    public function getClientDatableElement($start = 0, $limit = 10, $filter = '')
    {
        return $this->repository->clientDatableElement($start, $limit, $filter);
    }

    /**
     * @return mixed
     */
    public function getClientTotalElement()
    {
        return $this->repository->getClientCountTotal();
    }

    /**
     * @param string $filter
     * @return mixed
     */
    public function getClientFilterTotal($filter = '')
    {
        return $this->repository->getClientFilterTotal($filter);
    }
}