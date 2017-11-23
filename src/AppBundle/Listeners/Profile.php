<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 08/31/2015
 * Time: 4:41 PM
 */

namespace AppBundle\Listeners;

use AppBundle\Entity\User;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Profile implements EventSubscriberInterface
{
    private $container = null;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'onProfileEditCompleted'
        );
    }

    public function onProfileEditCompleted(FilterUserResponseEvent $event) {
        $file = $event->getRequest()->files->get('fos_user_profile_form');
        $file = $file['image'];
        if(!empty($file)) {
            $user = $event->getUser();
            $uploadDir = User::IMAGES_UPLOAD_DIR;
            $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $name);
            if(null !== $user->getPicture()) {
                $filename = $uploadDir.$user->getPicture();
                if(is_file($filename)) {
                    unlink($filename);
                }
            }
            $user->setPicture($name);
            $this->container->get('fos_user.user_manager')->updateUser($user);
        }
    }
}