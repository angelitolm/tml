<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 05/26/2015
 * Time: 2:32 PM
 */

namespace AppBundle\Listeners;

use AppBundle\Entity\Profile;
use Doctrine\Common\Util\Debug;
use Elao\WebProfilerExtraBundle\TwigProfilerEngine;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Registration implements EventSubscriberInterface{

    private $container;
    private $blogDefault;

    public function __construct(ContainerInterface $container, $blogDefault) {
        $this->container = $container;
        $this->blogDefault = $blogDefault;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted'
        );
    }

    public function onRegistrationInitialize(GetResponseUserEvent $event) {
        $event->getUser()->setLocale($event->getRequest()->getLocale());
        if($event->getRequest()->get('membership', false)) {
            $this->container->get('session')->set('membership', $event->getRequest()->get('membership'));
        }
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $code = $event->getRequest()->request->get('fos_user_registration_form')['code'];
        $this->container->get('session')->set('code', $code);
        if($this->container->get('session')->has('membership')) {
            $guest = $this->container->get('tml.guest_code')->findByCode($code);
            if(null === $guest) {
                $url = $this->container->get('router')->generate('app_admin_membership_buy', array(
                    '_locale'=>$event->getRequest()->getLocale(),
                    'code' => $code,
                    'membership' => $this->container->get('session')->get('membership')
                ));
            } else {
                $url = $this->container->get('router')->generate('app_admin_membership_index', array('_locale'=>$event->getRequest()->getLocale()));
            }
        } else {
            $url = $this->container->get('router')->generate('app_admin_membership_index', array('_locale'=>$event->getRequest()->getLocale()));
        }
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event) {
        $this->container->get('tml.user_message')->registerMessage(
            $event->getUser(),
            $this->container->get('translator')->trans('message.admin',array(),'messages'),
            $this->container->get('translator')->trans('message.user_created.title',array(),'messages'),
            $this->container->get('translator')->trans('message.user_created.message',array(),'messages')
        );

        $code = $this->container->get('session')->get('code');
        $guest = $this->container->get('tml.guest_code')->findByCode($code);
        if(null !== $guest) {
            $profile = $this->container->get('tml.profile')->getEntity();
            $profile->setActive(true);
            $profile->setBlocked(false);
            $profile->setDemo(true);
            $profile->setRole('ROLE_GUEST');
            $profile->setType(Profile::PROFILE_GUEST);
            $profile->setUser($event->getUser());
            $profile->setSponsor($guest->getUser()->getActiveProfile());
            $this->container->get('tml.profile')->update($profile);

            $this->container->get('session')->remove('code');
            $this->container->get('session')->remove('membership');

            $this->container->get('tml.user_message')->registerMessage(
                $event->getUser(),
                $this->container->get('translator')->trans('message.admin',array(),'messages'),
                $this->container->get('translator')->trans('message.profile_created.title',array(),'messages'),
                $this->container->get('translator')->trans('message.profile_created.message',array('%profile%'=>'Guest'),'messages')
            );

            $this->container->get('tml.user_message')->registerMessage(
                $guest->getUser(),
                $this->container->get('translator')->trans('message.admin',array(),'messages'),
                $this->container->get('translator')->trans('message.sponsor_user_used_code.title',array(),'messages'),
                $this->container->get('translator')->trans('message.sponsor_user_used_code.message',array('%user%'=>$event->getUser()->getUsername(),'%code%'=>$code),'messages')
            );

            $user = $event->getUser();
            $memberPrefix = $this->container->get('tml.configuration')->findByField('guest_prefix')->getValue();
            $memberNumber = $this->container->get('tml.configuration')->findByField('guest_code');
            $memberNumberValue = (integer)$memberNumber->getValue();
            $user->setCode($memberPrefix.$memberNumberValue);
            $memberNumberValue++;
            $memberNumber->setValue((string)$memberNumberValue);
            $this->container->get('tml.configuration')->update($memberNumber);
            $this->container->get('fos_user.user_manager')->updateUser($user);
            $this->container->get('tml.guest_code')->removeByCode($code);
        }
        $blog = $this->container->get('tml.blog')->getEntity();
        $blog->setTitle($this->blogDefault['blog_title']);
        $blog->setSubtitle($this->blogDefault['blog_subtitle']);
        $blog->setSlogan($this->blogDefault['blog_slogan']);
        $blog->setUser($event->getUser());
        $this->container->get('tml.blog')->update($blog);

        $mail = \Swift_Message::newInstance()

            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($event->getUser()->getEmail())
            ->setSubject('Registro Completado')
            ->setBody(
                $this->container->get('templating')->render('helpers/email_register.html.twig',array('user'=>$event->getUser())),
                'text/html'
            );

        $this->container->get('mailer')->send($mail);

        $this->container->get('tml.log')->registerLog(
            sprintf('The user %s has been register in the system.', $event->getUser()->getUsername())
        );
    }
}