<?php
/**
 * Created by PhpStorm.
 * User: Angelito
 * Date: 03/12/2015
 * Time: 12:24 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TestCourses
 *
 * @ORM\Table(name="tml_test_courses")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\TestCoursesRepository")
 */
class TestCourses
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="testCourses")
     * @ORM\JoinColumn(fieldName="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="course", type="string", length=255, nullable=true)
     */
    private $course;

    /**
     * @var integer
     *
     * @ORM\Column(name="punctuation", type="integer")
     */
    private $punctuation;


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
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param string $course
     */
    public function setCourse($course)
    {
        $this->course = $course;
    }



    /**
     * Set punctuation
     *
     * @param integer $punctuation
     *
     * @return TestCourses
     */
    public function setPunctuation($punctuation)
    {
        $this->punctuation = $punctuation;

        return $this;
    }

    /**
     * Get punctuation
     *
     * @return integer
     */
    public function getPunctuation()
    {
        return $this->punctuation;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return TestCourses
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
}
