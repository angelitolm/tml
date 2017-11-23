<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class UserController
 * @package AppBundle\Controller
 *
 * @Route("/admin/users")
 */
class TestCoursesController extends Controller
{
    /**
     * @Route("/admin/dashboard/test_courses", name="app_admin_test_courses")
     */
    public function testCoursesAction()
    {
        $level = $this->get('request')->request->get('level',1);
        $punctuation = $this->get('request')->request->get('resultptos',0);

        /*if ($this->getUser()->hasRole('ROLE_PROFESSIONAL')) {
            $level = 3;
            $courses = 'Professional Course';
        } elseif ($this->getUser()->hasRole('ROLE_SUPERIOR')) {
            $level = 2;
            $courses = 'Superior Course';
        } else {
            $level = 1;
            $courses = 'Basic Course';
        }*/

        if (!empty($punctuation)){
            $courseTest = $this->get('tml.test_courses')->getByUserLevel($this->getUser()->getId(), $level);
            if(null === $courseTest) {
                $courseTest = $this->get('tml.test_courses')->getEntity();
                $courseTest->setUser($this->getUser());
                $courseTest->setLevel($level);
            }
            //$profilePuntuation->setCourse($courses);
            $courseTest->setPunctuation($punctuation);

            $this->get('tml.test_courses')->update($courseTest);
        }

        return $this->redirect($this->generateUrl('certified'));
    }
}
