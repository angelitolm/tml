<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Transfer
 *
 * @ORM\Table(name="tml_transfer")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\TransferRepository")
 */
class Transfer
{
    public static $PAYPAL = 1;
    public static $DEPOSIT = 1;
    public static $EXTRACTION = 2;
    public static $PAY_COMISION = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="transfer", type="integer")
     */
    private $transfer;

    /**
     * @var integer
     *
     * @ORM\Column(name="transfer_type", type="integer")
     */
    private $transferType;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="transfers")
     * @ORM\JoinColumn(fieldName="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function __construct() {
        $this->amount = 0;
        $this->code = uniqid(mt_rand(), true);
        $this->transfer = Transfer::$PAYPAL;
        $this->transferType = Transfer::$DEPOSIT;
        $this->active = true;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getCode();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Transfer
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Transfer
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set transfer
     *
     * @param integer $transfer
     *
     * @return Transfer
     */
    public function setTransfer($transfer)
    {
        $this->transfer = $transfer;

        return $this;
    }

    /**
     * Get transfer
     *
     * @return integer
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Transfer
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Transfer
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Transfer
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Transfer
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set transferType
     *
     * @param integer $transferType
     *
     * @return Transfer
     */
    public function setTransferType($transferType)
    {
        $this->transferType = $transferType;

        return $this;
    }

    /**
     * Get transferType
     *
     * @return integer
     */
    public function getTransferType()
    {
        return $this->transferType;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Transfer
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
