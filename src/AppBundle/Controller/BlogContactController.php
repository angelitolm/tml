<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogContactController extends Controller
{
    /**
     * @Route("/blog-contact/new/{username}", name="app_front_blog_contact_new")
     */
    public function newAction($username)
    {
        $user = $this->get('fos_user.user_manager')->findUserByUsername($username);
        $responseData = array('success'=>false);
        if(null !== $user) {
            $name = $this->get('request')->request->get('contact-form-name');
            $email = $this->get('request')->request->get('contact-form-email');
            $messageBody = $this->get('request')->request->get('contact-form-message');

            $message = $this->get('tml.user_message')->getEntity();
            $message->setSend($name.'<'.$email.'>');
            $message->setSubject($this->get('translator')->trans('message.blog_contact.title',array(),'messages'));
            $message->setMessage($messageBody);
            $message->setUser($user);
            $this->get('tml.user_message')->update($message);

            /*$blogContact = $this->get('tml.blog_contact')->getEntity();
            $blogContact->setName($name);
            $blogContact->setEmail($email);
            $blogContact->setMessage($message);
            $blogContact->setBlog($user->getBlog());
            $this->get('tml.blog_contact')->update($blogContact);*/

            $responseData['success'] = true;
        } else {

        }

        $response = new JsonResponse($responseData);
        return $response;
    }

    /**
     * @Route("/admin/messages", name="app_admin_messages_index")
     */
    public function indexAction() {
        return $this->render(':blog_contact:index.html.twig');
    }

    /**
     * @Route("/admin/message-datatable", name="app_admin_messages_datatable")
     */
    public function datatableAction() {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $user = $this->getUser()->getId();
        $elements = $this->get('tml.user_message')->getDatableElement($user,$start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            $data[] = array(
                'from' => $element->getSend(),
                'body' => $this->renderView(':blog_contact:body.html.twig', array('subject'=>$element->getSubject(),'message'=>$element->getMessage())),
                'actions'  => $this->renderView(':blog_contact:actions.html.twig', array('id'=>$element->getId()))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('tml.post')->getTotalElement($user),
            "recordsFiltered" => $this->get('tml.post')->getTotalFilter($user, $filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/admin/message-details/{id}", name="app_admin_message_details")
     * @ParamConverter("entity")
     */
    public function detailsAction(UserMessage $entity) {
        if(UserMessage::$NEW_STATUS ==  $entity->getStatus()) {
            $entity->setStatus(UserMessage::$READ_STATUS);
            $this->get('tml.user_message')->update($entity);
        }
        return $this->render(':blog_contact:details.html.twig', array('message'=>$entity));
    }

}
