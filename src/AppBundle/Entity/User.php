<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * User
 *
 * @ORM\Table(name="tml_user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserRepository")
 */
class User extends BaseUser
{
    const IMAGES_UPLOAD_DIR = "upload/users/";
    static $ROLE_CLIENT = 'ROLE_CLIENT';
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=255, nullable=true)
     */
    protected $dni;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=255, nullable=true)
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    protected $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Profile", mappedBy="user")
     */
    protected $profiles;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Blog", mappedBy="user")
     */
    protected $blog;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transfer", mappedBy="user")
     */
    protected $transfers;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user")
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GuestCode", mappedBy="user")
     */
    protected $guestCode;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserMessage", mappedBy="user")
     */
    protected $messages;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SocialMessage", mappedBy="user")
     */
    protected $socialMessage;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SocialMessage", inversedBy="userLikes", cascade={"persist"})
     * @ORM\JoinColumn(name="user_social_message")
     */
    protected $socialMessageLikes;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="follows")
     **/
    protected $followers;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="followers")
     * @ORM\JoinTable(name="tml_user_followers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_follow_id", referencedColumnName="id")}
     *   )
     **/
    protected $follows;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TestCourses", mappedBy="user")
     */
    protected $testCourses;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Comember", mappedBy="user")
     */
    protected $comember;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(type="datetime", name="code_generated", nullable=true)
     */
    protected $codeGenerated;

    public function __construct() {
        parent::__construct();
        $this->profiles = new ArrayCollection();
        $this->transfers = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->guestCode = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->socialMessage = new ArrayCollection();
        $this->socialMessageLikes = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->testCourses = new ArrayCollection();
        $this->locale = 'es';
    }

    /**
     * @return string
     */
    public function __toString() {
        $name = $this->getName();
        if(empty($name)) {
            $name = $this->getUsername();
        }
        return $name;
    }

    /**
     * @return array
     */
    public function getRoles() {
        $roles = parent::getRoles();
        $roles[] = static::$ROLE_CLIENT;

        foreach($this->getProfiles() as $profile) {
            if($profile->isActive() && !$profile->isBlocked()) {
                $roles[] = $profile->getRole();
            }
        }

        return $roles;
    }

    /**
     * @return bool
     */
    public function isGuest() {
        return Profile::PROFILE_GUEST === $this->getActiveProfile()->getType();
    }

    /**
     * @return \AppBundle\Entity\Profile|null
     */
    public function getActiveProfile() {
        $profileActive = null;
        foreach($this->getProfiles() as $profile) {
            if($profile->isActive() && !$profile->isBlocked()) {
                $profileActive = $profile;
                break;
            }
        }
        return $profileActive;
    }

    /**
     * @param $role
     * @return bool
     */
    public function isRoleActiveProfile($role) {
        $profile = $this->getRoleProfile($role);
        if(null == $profile) return false;

        return $profile->isActive() && !$profile->isBlocked();
    }

    /**
     * @param $role
     * @return \AppBundle\Entity\Profile|null
     */
    public function getRoleProfile($role) {
        $profileActive = null;
        foreach($this->getProfiles() as $profile) {
            if($role == $profile->getRole()) {
                $profileActive = $profile;
                break;
            }
        }
        return $profileActive;
    }

    /**
     * @param $role
     * @return int
     */
    public function getProfileCredit($role) {
        $credit = 0;
        foreach($this->getProfiles() as $profile) {
            if($role == $profile->getRole()) {
                $credit = $profile->getCredit();
                break;
            }
        }
        return $credit;
    }

    /**
     * @param $membership
     * @return bool
     */
    public function validBuyMembership($membership) {
        if($this->hasRole('ROLE_PROFESSIONAL'))
            return true;

        if($this->hasRole('ROLE_SUPERIOR') && $membership != 'professional')
            return true;

        if($this->hasRole('ROLE_BASIC') && $membership == 'basic')
            return true;

        return false;
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
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return User
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
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return User
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
     * @return User
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
     * Add profile
     *
     * @param \AppBundle\Entity\Profile $profile
     *
     * @return User
     */
    public function addProfile(\AppBundle\Entity\Profile $profile)
    {
        $this->profiles[] = $profile;

        return $this;
    }

    /**
     * Remove profile
     *
     * @param \AppBundle\Entity\Profile $profile
     */
    public function removeProfile(\AppBundle\Entity\Profile $profile)
    {
        $this->profiles->removeElement($profile);
    }

    /**
     * Get profiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * Set blog
     *
     * @param \AppBundle\Entity\Blog $blog
     *
     * @return User
     */
    public function setBlog(\AppBundle\Entity\Blog $blog = null)
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * Get blog
     *
     * @return \AppBundle\Entity\Blog
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Add transfer
     *
     * @param \AppBundle\Entity\Transfer $transfer
     *
     * @return User
     */
    public function addTransfer(\AppBundle\Entity\Transfer $transfer)
    {
        $this->transfers[] = $transfer;

        return $this;
    }

    /**
     * Remove transfer
     *
     * @param \AppBundle\Entity\Transfer $transfer
     */
    public function removeTransfer(\AppBundle\Entity\Transfer $transfer)
    {
        $this->transfers->removeElement($transfer);
    }

    /**
     * Get transfers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransfers()
    {
        return $this->transfers;
    }

    public function getActiveTransfers() {
        return $this->getTransfers()->filter(function($entity) {
            return true == $entity->getActive();
        });
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add guestCode
     *
     * @param \AppBundle\Entity\GuestCode $guestCode
     *
     * @return User
     */
    public function addGuestCode(\AppBundle\Entity\GuestCode $guestCode)
    {
        $this->guestCode[] = $guestCode;

        return $this;
    }

    /**
     * Remove guestCode
     *
     * @param \AppBundle\Entity\GuestCode $guestCode
     */
    public function removeGuestCode(\AppBundle\Entity\GuestCode $guestCode)
    {
        $this->guestCode->removeElement($guestCode);
    }

    /**
     * Get guestCode
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuestCode()
    {
        return $this->guestCode;
    }

    /**
     * Add message
     *
     * @param \AppBundle\Entity\UserMessage $message
     *
     * @return User
     */
    public function addMessage(\AppBundle\Entity\UserMessage $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \AppBundle\Entity\UserMessage $message
     */
    public function removeMessage(\AppBundle\Entity\UserMessage $message)
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

    public function getUnreadMessage() {
        $today = new \DateTime();
        $today->setTime(0,0,0);
        return $this->getMessages()->filter(function($entity) use ($today) {
            return UserMessage::$NEW_STATUS == $entity->getStatus() && $entity->getUpdated()->diff($today)->days < 1;
        });
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return User
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
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
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param string $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }



    /**
     * Add socialMessage
     *
     * @param \AppBundle\Entity\SocialMessage $socialMessage
     *
     * @return User
     */
    public function addSocialMessage(\AppBundle\Entity\SocialMessage $socialMessage)
    {
        $this->socialMessage[] = $socialMessage;

        return $this;
    }

    /**
     * Remove socialMessage
     *
     * @param \AppBundle\Entity\SocialMessage $socialMessage
     */
    public function removeSocialMessage(\AppBundle\Entity\SocialMessage $socialMessage)
    {
        $this->socialMessage->removeElement($socialMessage);
    }

    /**
     * Get socialMessage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSocialMessage()
    {
        return $this->socialMessage;
    }

    /**
     * Add socialMessageLike
     *
     * @param \AppBundle\Entity\SocialMessage $socialMessageLike
     *
     * @return User
     */
    public function addSocialMessageLike(\AppBundle\Entity\SocialMessage $socialMessageLike)
    {
        $this->socialMessageLikes[] = $socialMessageLike;

        return $this;
    }

    /**
     * Remove socialMessageLike
     *
     * @param \AppBundle\Entity\SocialMessage $socialMessageLike
     */
    public function removeSocialMessageLike(\AppBundle\Entity\SocialMessage $socialMessageLike)
    {
        $this->socialMessageLikes->removeElement($socialMessageLike);
    }

    /**
     * Get socialMessageLikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSocialMessageLikes()
    {
        return $this->socialMessageLikes;
    }

    /**
     * Add follower
     *
     * @param \AppBundle\Entity\User $follower
     *
     * @return User
     */
    public function addFollower(\AppBundle\Entity\User $follower)
    {
        $this->followers[] = $follower;

        return $this;
    }

    /**
     * Remove follower
     *
     * @param \AppBundle\Entity\User $follower
     */
    public function removeFollower(\AppBundle\Entity\User $follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add follow
     *
     * @param \AppBundle\Entity\User $follow
     *
     * @return User
     */
    public function addFollow(\AppBundle\Entity\User $follow)
    {
        $this->follows[] = $follow;

        return $this;
    }

    /**
     * Remove follow
     *
     * @param \AppBundle\Entity\User $follow
     */
    public function removeFollow(\AppBundle\Entity\User $follow)
    {
        $this->follows->removeElement($follow);
    }

    /**
     * Get follows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollows()
    {
        return $this->follows;
    }

    public function userHasFollow($userId) {
        return $this->getFollows()->exists(function($key, $entity) use ($userId) {
            return $userId == $entity->getId();
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserChatList() {
        $follows = $this->getFollows();
        $followers = $this->getFollowers();
        foreach ($followers as $follower) {
            if(!$this->userHasFollow($follower->getId())) {
                $follows->add($follower);
            }
        }
        return $follows;
    }

    /**
     * Set codeGenerated
     *
     * @param \DateTime $codeGenerated
     *
     * @return User
     */
    public function setCodeGenerated($codeGenerated)
    {
        $this->codeGenerated = $codeGenerated;

        return $this;
    }

    /**
     * Get codeGenerated
     *
     * @return \DateTime
     */
    public function getCodeGenerated()
    {
        return $this->codeGenerated;
    }

    /**
     * Add testCourse
     *
     * @param \AppBundle\Entity\TestCourses $testCourse
     *
     * @return User
     */
    public function addTestCourse(\AppBundle\Entity\TestCourses $testCourse)
    {
        $this->testCourses[] = $testCourse;

        return $this;
    }

    /**
     * Remove testCourse
     *
     * @param \AppBundle\Entity\TestCourses $testCourse
     */
    public function removeTestCourse(\AppBundle\Entity\TestCourses $testCourse)
    {
        $this->testCourses->removeElement($testCourse);
    }

    /**
     * Get testCourses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTestCourses()
    {
        return $this->testCourses;
    }

    /**
     * Set comember
     *
     * @param \AppBundle\Entity\Comember $comember
     *
     * @return User
     */
    public function setComember(\AppBundle\Entity\Comember $comember = null)
    {
        $this->comember = $comember;

        return $this;
    }

    /**
     * Get comember
     *
     * @return \AppBundle\Entity\Comember
     */
    public function getComember()
    {
        return $this->comember;
    }

    /**
     * @param $level
     * @return bool
     */
    public function courseHasCompleted($level) {
        return $this->getTestCourses()->exists(function($key, $entity) use ($level) {
            return $level == $entity->getLevel();
        });
    }
}
