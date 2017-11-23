<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FrontendController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }

        $testimonials = $this->get('tml.testimonials')->getRepository()->findAll();
        $latestNews = $this->get('tml.news')->getLatestNews(3);

        return $this->render('AppBundle:Frontend:index.html.twig', array(
            'testimonials' => $testimonials,
            'news' => $latestNews,
            'csrf_token' => $csrfToken
        ));
    }

    /**
     * @Route("/about_us", name="abou_us")
     */
    public function aboutAction()
    {
        $testimonials = $this->get('tml.testimonials')->getRepository()->findAll();

        return $this->render('AppBundle:Frontend:about_us.html.twig', array(
            'testimonials' => $testimonials
        ));
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacyAction()
    {
        return $this->render('AppBundle:Frontend:privacy.html.twig');
    }

    /**
     * @Route("/terms", name="terms")
     */
    public function termsAction()
    {
        return $this->render('AppBundle:Frontend:terms.html.twig');
    }

    /**
     * @Route("/legal", name="legal")
     */
    public function legalAction()
    {
        return $this->render('AppBundle:Frontend:legal.html.twig');
    }

    /**
     * @Route("/servicios", name="servicios")
     */
    public function serviciosAction()
    {
        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
        return $this->render('AppBundle:Frontend:servicios.html.twig', array('csrf_token' => $csrfToken));
    }

    /**
     * @Route("/solicitud_credit", name="solicitud_credit")
     */
    public function solicitud_creditAction()
    {

        return $this->render('AppBundle:Frontend:solicitud_credit.html.twig');
    }

    /**
     * @Route("/enviar_solicitud_creditcaptura", name="enviar enviar_solicitud_creditcaptura")
     */
    public function enviar_solicitud_enviar_solicitud_creditcapturaAction()
    {
        $name = $_POST['name_to_credit'];
        $mail = $_POST['mail_to_credit'];
        $mail_string = "'.$mail.'";
        $name_sponsor = $_POST['sponsor_to_credit'];
        $code = $_POST['code_to_credit'];
        $comment = $_POST['comment_to_credit'];
    }

    /**
     * @Route("/enviar_solicitud_credit", name="enviar solicitud_credit")
     */
    public function enviar_solicitud_creditAction()
    {
        $name = $_POST['name_to_credit'];
        $mail = $_POST['mail_to_credit'];
        $mail_string = "'.$mail.'";
        $name_sponsor = $_POST['sponsor_to_credit'];
        $code = $_POST['code_to_credit'];
        $comment = $_POST['comment_to_credit'];

        $body = '<!DOCTYPE html>
<html>
<body>
<p>El usuario "' . $name . '", ha solicitado un pr&eacute;stamo a TML-Creidt</p>
<table>
    <tr>
        <th>Correo</th>
        <td>"' . $mail . '"</td>
    </tr>
    <tr>
        <th>Patrocinador</th>
        <td>"' . $name_sponsor . '"</td>
    </tr>
    <tr>
        <th>C&oacute;digo</th>
        <td>"' . $code . '"</td>
    </tr>
    <tr>
        <th>Comentario</th>
        <td>"' . $comment . '"</td>
    </tr>
</table>
</body>
</html>';

        $message = \Swift_Message::newInstance();
        $message
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($this->get('service_container')->getParameter('credit.notification_email'))
            ->setSubject('Solicitud de Membresia Credito')
            ->setBody($body, 'text/html');
        $this->get('mailer')->send($message);

        if ($message != null) {
            return new RedirectResponse(
                $this->generateUrl('servicios')
            );
        } else {
            return false;
        }
    }

    /**
     * @Route("/club", name="club")
     */
    public function clubAction()
    {
        $latestNews = $this->get('tml.news')->getLatestNews(3);

        return $this->render('AppBundle:Frontend:club.html.twig', array(
            'news' => $latestNews
        ));
    }

    /**
     * @Route("/member_pts", name="member_pts")
     */
    public function member_ptsAction()
    {
        $member = $this->get('fos_user.user_manager')->findUsers();

//        $userid = $member->getId();

//        $testCourses = $this->get('tml.test_courses')->getUserCourses($userid);
//        $test = array();
//        foreach ($testCourses as $course) {
//            $test[$course->getLevel()] = $course;
//        }

        return $this->render('AppBundle:Frontend:member_pts.html.twig', array(
            'members' => $member,
//            'testCourses' => $test
        ));
    }

    /**
     * @Route("/negocio", name="negocio")
     */
    public function negocioAction()
    {
        return $this->render('AppBundle:Frontend:negocio.html.twig');
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faqAction()
    {
        return $this->render('AppBundle:Frontend:faq.html.twig');
    }

    /**
     * @Route("/registro", name="registro")
     */
    public function registroAction()
    {
        $code = $this->get('session')->get('code', null);
        $prices = array(
            'basic' => $this->get('tml.configuration')->findByField('basic_price')->getValue(),
            'superior' => $this->get('tml.configuration')->findByField('superior_price')->getValue(),
            'professional' => $this->get('tml.configuration')->findByField('professional_price')->getValue()
        );
        $guests = array(
            'basic' => $this->get('tml.configuration')->findByField('guest_basic_code')->getValue(),
            'superior' => $this->get('tml.configuration')->findByField('guest_superior_code')->getValue(),
            'professional' => $this->get('tml.configuration')->findByField('guest_professional_code')->getValue()
        );

        return $this->render('AppBundle:Frontend:registro.html.twig', array('code' => $code, 'prices' => $prices, 'guests' => $guests));
    }

    /**
     * @Route("/datatable-clients", name="app_front_clients_datatable")
     */
    public function clientDatatableAction() {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $filter = $this->get('request')->query->get('search');
        $elements = $this->get('fos_user.user_manager')->getClientDatableElement($start, $limit, $filter['value']);
        $data = array();
        foreach($elements as $element) {
            $data[] = array(
                'row' => $this->renderView('AppBundle:Frontend:client.html.twig', array('user'=>$element))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('fos_user.user_manager')->getClientTotalElement(),
            "recordsFiltered" => $this->get('fos_user.user_manager')->getClientFilterTotal($filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

}
