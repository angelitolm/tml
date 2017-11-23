<?php

namespace AppBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

class Transfer extends Event
{
    private $user;
    private $amount;
    private $type;

    public function __construct(UserInterface $user, $amount, $type){
        $this->user = $user;
        $this->amount = $amount;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }


}