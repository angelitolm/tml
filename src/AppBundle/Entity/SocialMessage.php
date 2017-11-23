<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SocialMessage
 *
 * @ORM\Table(name="tml_social_message")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\SocialMessageRepository")
 */
class SocialMessage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes", type="integer")
     */
    private $likes;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="socialMessage")
     * @ORM\JoinColumn(fieldName="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="socialMessageLikes", cascade={"persist"})
     */
    private $userLikes;

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
        $this->userLikes = new ArrayCollection();
        $this->likes = 0;
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->getMessage();
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
     * Set message
     *
     * @param string $message
     *
     * @return SocialMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     *
     * @return SocialMessage
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     *
     */
    public function incrementLike() {
        $like = $this->getLikes();
        $like++;
        $this->setLikes($like);
    }

    /**
     *
     */
    public function decrementLike() {
        $like = $this->getLikes();
        $like--;
        $this->setLikes($like);
    }

    public function userHasLike($userId) {
        return $this->getUserLikes()->exists(function($key, $entity) use ($userId) {
           return $userId == $entity->getId();
        });
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return SocialMessage
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return SocialMessage
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
     * @return SocialMessage
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
     * Add userLike
     *
     * @param \AppBundle\Entity\User $userLike
     *
     * @return SocialMessage
     */
    public function addUserLike(\AppBundle\Entity\User $userLike)
    {
        $this->userLikes[] = $userLike;

        return $this;
    }

    /**
     * Remove userLike
     *
     * @param \AppBundle\Entity\User $userLike
     */
    public function removeUserLike(\AppBundle\Entity\User $userLike)
    {
        $this->userLikes->removeElement($userLike);
    }

    /**
     * Get userLikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserLikes()
    {
        return $this->userLikes;
    }
}
