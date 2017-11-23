<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class UserController
 * @package AppBundle\Controller
 *
 * @Route("/admin/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="app_admin_user_index")
     */
    public function indexAction()
    {
        return $this->render(':User:index.html.twig');
    }

    /**
     * @Route("/datatable", name="app_admin_user_datatable")
     */
    public function datatableAction()
    {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $elements = $this->get('fos_user.user_manager')->getDatableElement($start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            if($element->hasRole('ROLE_ADMIN')) {
                $role = 'Administrator';
            } elseif ($element->hasRole('ROLE_PROFESSIONAL')) {
                $role = 'Professional';
            } elseif ($element->hasRole('ROLE_SUPERIOR')) {
                $role = 'Superior';
            } else {
                $role = 'Basic';
            }
            $data[] = array(
                'name' => $element->getName(),
                'username' => $element->getUsername(),
                'email' => $element->getEmail(),
                'role' => $role,
                'status' => $this->renderView(':helpers:column-boolean.html.twig',array('flag'=>$element->isEnabled())),
                'actions'  => $this->renderView('User/datatable.html.twig', array('username'=>$element->getUsername(),'enabled'=>$element->isEnabled()))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('fos_user.user_manager')->getTotalElement(),
            "recordsFiltered" => $this->get('fos_user.user_manager')->getTotalFilter($filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/switch-status/{username}", name="app_admin_user_switch_status")
     */
    public function switchStatusAction($username) {
        $user = $this->get('fos_user.user_manager')->findUserByUsername($username);
        if(null !== $user) {
            $user->setEnabled(!$user->isEnabled());
            $this->get('fos_user.user_manager')->updateUser($user);
        }

        return $this->redirect($this->generateUrl('app_admin_user_index'));
    }

    /**
     * @Route("/follow", name="app_admin_user_follow")
     */
    public function followAction() {
        $username = $this->get('request')->request->get('username');
        $action = $this->get('request')->request->get('action', 1);

        $userFollow = $this->get('fos_user.user_manager')->findUserByUsername($username);
        if(null !== $userFollow) {
            $user = $this->getUser();
            if(1 == $action) {
                $user->addFollow($userFollow);
            } else {
                $user->removeFollow($userFollow);
            }
            $this->get('fos_user.user_manager')->updateUser($user);
        }

        return new JsonResponse(array('success'=>true));
    }

    /**
     * @Route("/sponsor-message", name="app_admin_user_sponsor_message")
     */
    public function sponsorMessage() {
        $message = $this->get('request')->request->get('message', null);
        $message = trim($message);
        $user = $this->getUser()->getActiveProfile();

        $responseData = array(
            'success' => false,
            'message' => $this->get('translator')->trans('sponsor.message.not_empty',array(),'backend')
        );
        if(!empty($message)) {
            $sponsor = $this->getUser()->getActiveProfile()->getSponsor();
            if(null !== $sponsor) {
                $profileMessage = $this->get('tml.profile_message')->getEntity();
                $profileMessage->setMessage($message);
                $profileMessage->setProfile($sponsor);
                $profileMessage->setProfileSend($user);
                $responseData = array(
                    'success' => true,
                    'message' => $this->get('translator')->trans('sponsor.message.send',array(),'backend')
                );
                $this->get('tml.profile_message')->update($profileMessage);
            } else {
                $responseData['message'] = $this->get('translator')->trans('sponsor.message.sponsor_not_found',array(),'backend');
            }
        }

        return new JsonResponse($responseData);
    }

    /**
     * @Route("/edit_comember", name="app_admin_user_edit_comember")
     */
    public function editComemberAction() {
        $entity = $this->getUser()->getComember();
        if(null === $entity) {
            $entity = $this->get('tml.comember')->getEntity();
            $entity->setUser($this->getUser());
        }
        $form = $this->createForm('app_comember', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $this->get('tml.comember')->update($entity);
        }

        return $this->redirect($this->generateUrl('fos_user_profile_show'));
    }

}
