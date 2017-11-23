<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SocialMessageController
 * @package AppBundle\Controller
 *
 * @Route("/social-message")
 */
class SocialMessageController extends Controller
{
    /**
     * @Route("/latest", name="app_admin_social_message_latest")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction() {
        $socialLimit = (integer)$this->get('tml.configuration')->findByField('social_message_limit')->getValue();
        $latestMessage = $this->get('tml.social_message')->getLatest($socialLimit);

        return $this->render(':SocialMessage:latest.html.twig', array('latestMessage'=>$latestMessage,'userId'=>$this->getUser()->getId()));
    }

    /**
     * @Route("/likes", name="app_admin_social_message_increment_like")
     * @return JsonResponse
     */
    public function likeAction() {
        $id = (integer)$this->get('request')->request->get('message',0);
        $action = (integer)$this->get('request')->request->get('action',0);
        $socialMessage = $this->get('tml.social_message')->getEntity($id);
        if(null !== $socialMessage) {
            $user = $this->getUser();
            if(1 == $action) {
                $socialMessage->incrementLike();
                //$socialMessage->addUserLike($user);
                $user->addSocialMessageLike($socialMessage);
            } else {
                $socialMessage->decrementLike();
                //$socialMessage->removeUserLike($user);
                $user->removeSocialMessageLike($socialMessage);
            }
            $this->get('fos_user.user_manager')->updateUser($user);
            $this->get('tml.social_message')->update($socialMessage);
        }

        return new JsonResponse(array('success'=>true));
    }

    /**
     * @Route("/publish", name="app_admin_social_message_publish")
     * @return JsonResponse
     */
    public function publishAction() {
        $responseData = array('success'=>false);
        $message = $this->get('request')->request->get('message',null);
        $message = trim($message);
        if(!empty($message)) {
            $this->get('tml.social_message')->registerMessage($this->getUser(), $message);
            $responseData['success'] = true;
        }

        return new JsonResponse($responseData);
    }
}
