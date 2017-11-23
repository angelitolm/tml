<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Profile
 *
 * @ORM\Table(name="tml_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\ProfileRepository")
 */
class Profile
{
    const PROFILE_GUEST = 0;
    const PROFILE_BASIC = 1;
    const PROFILE_SUPERIOR = 2;
    const PROFILE_PROFESSIONAL = 3;

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
     * @ORM\Column(name="credit", type="float")
     */
    private $credit;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var boolean
     *
     * @ORM\Column(name="blocked", type="boolean")
     */
    private $blocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="demo", type="boolean")
     */
    private $demo;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Profile", mappedBy="sponsor")
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProfileMessage", mappedBy="profile")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProfileMessage", mappedBy="profileSend")
     */
    private $messagesSend;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="profiles")
     * @ORM\JoinColumn(fieldName="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Profile", inversedBy="children")
     * @ORM\JoinColumn(fieldName="sponsor_id", referencedColumnName="id")
     */
    private $sponsor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="discount", type="boolean")
     */
    private $discount;

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
        $this->children = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->messagesSend = new ArrayCollection();
        $this->credit = 0;
        $this->active = true;
        $this->blocked = true;
        $this->demo = true;
        $this->type = Profile::PROFILE_BASIC;
        $this->discount = true;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getRole();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrenMembers() {
        return $this->getChildren()->filter(function($entity) {
            return Profile::PROFILE_GUEST != $entity->getType() && $entity->isActive() && !$entity->isBlocked();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDemoChildren() {
        return $this->getChildren()->filter(function($entity) {
            return true == $entity->getDemo() && Profile::PROFILE_GUEST != $entity->getType() && $entity->isActive() && !$entity->isBlocked();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrenMembersMonth() {
        $now = new \DateTime();
        $now->setDate($now->format('Y'),$now->format('m'),1);
        $now->setTime(0,0,0);
        return $this->getChildren()->filter(function($entity) use ($now){
            return Profile::PROFILE_GUEST != $entity->getType() && $now <= $entity->getCreated() && $entity->isActive() && !$entity->isBlocked();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuestChildrenMembers() {
        return $this->getChildren()->filter(function($entity) {
            return Profile::PROFILE_GUEST == $entity->getType() && $entity->isActive() && !$entity->isBlocked();
        });
    }

    /**
     * @return float
     */
    public function close() {
        $credit = $this->getCredit();
        $this->setActive(false);
        $this->setBlocked(true);
        $this->setCredit(0);

        return $credit;
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
     * Set credit
     *
     * @param float $credit
     *
     * @return Profile
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param int $credit
     * @return bool
     */
    public function increaseCredit($credit = 0) {
        $this->credit += $credit;
        return true;
    }

    /**
     * @param int $credit
     * @return bool
     */
    public function decreaseCredit($credit = 0) {
        if($this->credit >= $credit) {
            $this->credit -= $credit;
            return true;
        }
        return false;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Profile
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Profile
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

    /**
     * @return bool
     */
    public function isActive() {
        return $this->getActive();
    }

    /**
     * Set blocked
     *
     * @param boolean $blocked
     *
     * @return Profile
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * Get blocked
     *
     * @return boolean
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * @return bool
     */
    public function isBlocked() {
        return $this->getBlocked();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Profile
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
     * @return Profile
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
     * @return Profile
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
     * Add child
     *
     * @param \AppBundle\Entity\Profile $child
     *
     * @return Profile
     */
    public function addChild(\AppBundle\Entity\Profile $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Profile $child
     */
    public function removeChild(\AppBundle\Entity\Profile $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set sponsor
     *
     * @param \AppBundle\Entity\Profile $sponsor
     *
     * @return Profile
     */
    public function setSponsor(\AppBundle\Entity\Profile $sponsor = null)
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * Get sponsor
     *
     * @return \AppBundle\Entity\Profile
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

    /**
     * Set demo
     *
     * @param boolean $demo
     *
     * @return Profile
     */
    public function setDemo($demo)
    {
        $this->demo = $demo;

        return $this;
    }

    /**
     * Get demo
     *
     * @return boolean
     */
    public function getDemo()
    {
        return $this->demo;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


    /**
     * Set discount
     *
     * @param boolean $discount
     *
     * @return Profile
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return boolean
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Add message
     *
     * @param \AppBundle\Entity\ProfileMessage $message
     *
     * @return Profile
     */
    public function addMessage(\AppBundle\Entity\ProfileMessage $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \AppBundle\Entity\ProfileMessage $message
     */
    public function removeMessage(\AppBundle\Entity\ProfileMessage $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add messagesSend
     *
     * @param \AppBundle\Entity\ProfileMessage $messagesSend
     *
     * @return Profile
     */
    public function addMessagesSend(\AppBundle\Entity\ProfileMessage $messagesSend)
    {
        $this->messagesSend[] = $messagesSend;

        return $this;
    }

    /**
     * Remove messagesSend
     *
     * @param \AppBundle\Entity\ProfileMessage $messagesSend
     */
    public function removeMessagesSend(\AppBundle\Entity\ProfileMessage $messagesSend)
    {
        $this->messagesSend->removeElement($messagesSend);
    }

    /**
     * Get messagesSend
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessagesSend()
    {
        return $this->messagesSend;
    }

}
