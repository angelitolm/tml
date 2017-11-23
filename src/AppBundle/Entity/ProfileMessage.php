<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProfileMessage
 *
 * @ORM\Table(name="tml_profile_message")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\ProfileMessageRepository")
 */
class ProfileMessage
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Profile", inversedBy="messages")
     * @ORM\JoinColumn(fieldName="profile_id", referencedColumnName="id")
     */
    private $profile;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Profile", inversedBy="messagesSend")
     * @ORM\JoinColumn(fieldName="profile_send_id", referencedColumnName="id")
     */
    private $profileSend;

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

    public function __toString()
    {
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
     * @return ProfileMessage
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
     * Set profile
     *
     * @param \AppBundle\Entity\Profile $profile
     *
     * @return ProfileMessage
     */
    public function setProfile(\AppBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \AppBundle\Entity\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set profileSend
     *
     * @param \AppBundle\Entity\Profile $profileSend
     *
     * @return ProfileMessage
     */
    public function setProfileSend(\AppBundle\Entity\Profile $profileSend = null)
    {
        $this->profileSend = $profileSend;

        return $this;
    }

    /**
     * Get profileSend
     *
     * @return \AppBundle\Entity\Profile
     */
    public function getProfileSend()
    {
        return $this->profileSend;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return ProfileMessage
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
     * @return ProfileMessage
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
}
