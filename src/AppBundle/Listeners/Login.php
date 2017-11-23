<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 08/31/2015
 * Time: 4:41 PM
 */

namespace AppBundle\Listeners;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class Login
{
    private $router = null;
    private $locale = 'en';
    private $url = false;

    public function __construct(Router $router, $defaultLocale) {
        $this->router = $router;
        $this->locale = $defaultLocale;
    }

    public function onAuthenticationSuccess(AuthenticationEvent $event) {
        if($event->getAuthenticationToken()->getUser() instanceof UserInterface) {
            $locale = $event->getAuthenticationToken()->getUser()->getLocale();
            if(null !== $locale) {
                $this->locale = $locale;
                $this->url = 'app_admin_dashboard';
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event) {
        if (null != $this->url) {
            $url = $this->router->generate($this->url, array('_locale' => $this->locale));
            $event->setResponse(new RedirectResponse($url));
            $event->stopPropagation();
        }
    }
}