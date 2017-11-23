<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Profile;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DashboardController
 * @package AppBundle\Controller
 * @Route("/admin/dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="app_admin_dashboard")
     */
    public function indexAction()
    {
        if ($this->getUser()->hasRole('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('app_admin_dashboard_admin'));
        }
        $latestNews = $this->get('tml.news')->getLatestNews(3);
        $latestTransfer = $this->get('tml.transfer')->getLatestTransfer(10);

        switch ($this->getUser()->getActiveProfile()->getType()) {
            case Profile::PROFILE_BASIC:
                $membership = "Basica";
                $limitAffiliates = $this->get('tml.configuration')->findByField('basic_limit_sponsor')->getValue();
                $limitGuest = $this->get('tml.configuration')->findByField('guest_basic_code')->getValue();
                $price = $this->get('tml.configuration')->findByField('basic_price')->getValue();
                break;
            case Profile::PROFILE_SUPERIOR:
                $membership = "Superior";
                $limitAffiliates = $this->get('tml.configuration')->findByField('superior_limit_sponsor')->getValue();
                $limitGuest = $this->get('tml.configuration')->findByField('guest_superior_code')->getValue();
                $price = $this->get('tml.configuration')->findByField('superior_price')->getValue();
                break;
            case Profile::PROFILE_PROFESSIONAL:
                $membership = "Professional";
                $limitAffiliates = $this->get('tml.configuration')->findByField('professional_limit_sponsor')->getValue();
                $limitGuest = $this->get('tml.configuration')->findByField('guest_professional_code')->getValue();
                $price = $this->get('tml.configuration')->findByField('professional_price')->getValue();
                break;
            default:
                $limitAffiliates = 0;
                $limitGuest = 0;
                $price = 0;
                $membership = '';
        }

        $created = clone $this->getUser()->getCreated();
        $endMembership = $created->add(new \DateInterval('P1Y'));

        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $start = $now->format('Y-m-d H:i:s');
        $now->setTime(23, 59, 59);
        $end = $now->format('Y-m-d H:i:s');
        $comitionToday = $this->get('tml.transfer')->getTotalForRange($this->getUser()->getId(), $start, $end);

        return $this->render(':dashboard:index.html.twig', array('latestNews' => $latestNews, 'latestTransfers' => $latestTransfer,
            'price' => $price, 'endMembership' => $endMembership, 'membership' => $membership, 'comitionToday' => $comitionToday,
            'limitAffiliates' => $limitAffiliates, 'limitGuest' => $limitGuest
        ));
    }

    /**
     * @Route("/admon", name="app_admin_dashboard_admin")
     */
    public function indexAdminAction()
    {
        $latestLog = $this->get('tml.log')->getLatestLog();
        $clients = array();
        $clients['today'] = array(
            'clients' => $this->get('fos_user.user_manager')->getRegisterToday(),
            'guests' => $this->get('fos_user.user_manager')->getRegisterToday(true),
        );
        $clients['week'] = array(
            'clients' => $this->get('fos_user.user_manager')->getRegisterWeek(),
            'guests' => $this->get('fos_user.user_manager')->getRegisterWeek(true),
        );
        $clients['month'] = array(
            'clients' => $this->get('fos_user.user_manager')->getRegisterMonth(),
            'guests' => $this->get('fos_user.user_manager')->getRegisterMonth(true),
        );
        return $this->render(':dashboard:admin.html.twig', array('latestLog' => $latestLog,'clients'=>$clients));
    }

    /**
     * @Route("/inbox", name="app_admin_dashboard_inbox")
     */
    public function inboxAtion()
    {
        return $this->render(':dashboard:inbox.html.twig');
    }

    /**
     * @Route("/unread-message-notification", name="app_admin_unread_message_notification")
     */
    public function notificationMessageAction()
    {
        $messages = $this->getUser()->getUnreadMessage();
        return $this->render(':dashboard:message_unread.html.twig', array('messages' => $messages));
    }

    /**
     * @Route("/testimonial", name="app_admin_dashboard_testimonial")
     */
    public function testimonialAction()
    {
        $testimonies = $this->get('request')->request->get('testimonies', null);
        $responseData = array('success' => false);
        if (!empty($testimonies)) {
            $comment = $this->get('tml.testimonials')->getEntity();
            $comment->setMessage($testimonies);
            $comment->setUser($this->getUser());
            $this->get('tml.testimonials')->update($comment);
            $responseData = array('success' => true);
        }
        $response = new JsonResponse($responseData);
        return $response;
    }

    /**
     * @Route("/user_profile_other", name="user_profile_other")
     */
    public function profile_otherAction()
    {
        return $this->render('FOSUserBundle:Profile:other.html.twig');
    }

    /**
     * @Route("/courses_basics", name="courses_basics")
     */
    public function courses_basicsAction()
    {
        $course = $this->get('tml.test_courses')->getByUserLevel($this->getUser()->getId(),1);
        $ptos = 0;
        if(null !== $course) {
            $ptos = $course->getPunctuation();
        }

        return $this->render(':dashboard:course_basic.html.twig', array(
            'userptos' => $ptos
        ));
    }

    /**
     * @Route("/courses_superior", name="courses_superior")
     */
    public function courses_superiorAction()
    {
        $course = $this->get('tml.test_courses')->getByUserLevel($this->getUser()->getId(),2);
        $ptos = 0;
        if(null !== $course) {
            $ptos = $course->getPunctuation();
        }

        return $this->render(':dashboard:course_superior.html.twig', array(
            'userptos' => $ptos
        ));
    }

    /**
     * @Route("/courses_professionals", name="courses_professionals")
     */
    public function courses_professionalsAction()
    {
        $course = $this->get('tml.test_courses')->getByUserLevel($this->getUser()->getId(),3);
        $ptos = 0;
        if(null !== $course) {
            $ptos = $course->getPunctuation();
        }

        return $this->render(':dashboard:course_professional.html.twig', array(
            'userptos' => $ptos
        ));
    }


    /**
     * @Route("/courses_extras/{name}", name="courses_extras")
     */
    public function courses_extrasAction($name)
    {

        return $this->render(':courses_extras:'.$name.'.html.twig');
    }

    /**
     * @Route("/guaranties", name="guaranties")
     */
    public function guarantiesAction()
    {
        return $this->render(':dashboard:guaranties.html.twig');
    }

    /**
     * @Route("/blog/undefined", name="blog_undefined")
     */
    public function blog_undefinedAction()
    {
        return $this->redirect(':Blog:blog_undefined.html.twig');
    }

    /**
     * @Route("/certified", name="certified")
     */
    public function certifiedAction()
    {

        $testCourses = $this->get('tml.test_courses')->getUserCourses($this->getUser()->getId());
        $test = array();
        foreach($testCourses as $course) {
            $test[$course->getLevel()] = $course;
        }

        return $this->render(':dashboard:certified.html.twig', array(
            'testCourses' => $test
        ));
    }

    /**
     * @Route("/portafolio", name="portafolio")
     */
    public function portafolioAction()
    {
        return $this->render(':dashboard:portafolio.html.twig');
    }

}
