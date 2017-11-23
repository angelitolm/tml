<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LanguageController extends Controller
{
    /**
     * @Route("/switch-language",name="app_switch_language")
     */
    public function switchAction()
    {
        $request = $this->get('request');
        $uri = $request->query->get('uri');
        $locale = $request->query->get('lng',$request->getDefaultLocale());
        $params = $request->query->get('params');
        $params['_locale'] = $locale;
        /*if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $user->setLocale($locale);
            $this->get('fos_user.user_manager')->updateUser($user);
        }*/

        $route = $this->get('router')->match($uri);
        return $this->redirect($this->generateUrl($route['_route'],$params));
    }

}
