<?php

namespace AppBundle\Controller;

use AppBundle\AppEvents;
use AppBundle\Entity\Profile;
use AppBundle\Entity\Transfer;
use AppBundle\Entity\User;
use AppBundle\Event\MembershipBuy;
use Doctrine\Common\Util\Debug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Omnipay\Omnipay;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MembershipController
 * @package AppBundle\Controller
 * @Route("/admin/membership")
 */
class MembershipController extends Controller
{
    /**
     * @Route("/", name="app_admin_membership_index")
     */
    public function indexAction()
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
        return $this->render('membership/index.html.twig', array('code'=>$code,'prices'=>$prices,'guests'=>$guests));
    }

    /**
     * @Route("/buy-membership", name="app_admin_membership_buy")
     */
    public function buyMembershipAction() {
        $membership = $this->get('request')->get('membership');
        $code = $this->get('request')->get('code', null);
        $user = $this->get('fos_user.user_manager')->findUserBy(array('code'=>$code));

        $this->get('session')->set('code', $code);
        $this->get('session')->set('membership', $membership);

        $method = $this->get('request')->request->get('method', 1);
        $price = $this->get('tml.configuration')->findByField($membership.'_price')->getValue();

        if(null === $code || null === $user || !$user->isAccountNonLocked()) {
            if($code != $this->get('service_container')->getParameter('app.hack_code')) {
                $this->get('session')->getFlashBag()->add('danger',$this->get('translator')->trans('code.provide.not_valid',array(),'backend'));
                return $this->redirect($this->generateUrl('app_admin_membership_index'));
            }
        }

        if( null !== $user) {
            if(!$user->validBuyMembership($membership)) {
                $this->get('session')->getFlashBag()->add('danger',$this->get('translator')->trans('code.provide.user_not_have_membership_valid_for_validate',array(),'backend'));
                return $this->redirect($this->generateUrl('app_admin_membership_index'));
            }

            $limit = (integer)$this->get('tml.configuration')->findByField($membership.'_limit_sponsor')->getValue();
            $counter = $this->get('tml.profile')->countRegisterBySponsor($user->getActiveProfile()->getId());
            if($limit <= $counter) {
                $price = $this->get('tml.configuration')->findByField($membership.'_exceded_price')->getValue();
            }
        }

        if(2 == $method) {
            if(null === $this->getUser()->getActiveProfile() || $this->getUser()->getActiveProfile()->getCredit() < $price) {
                $this->get('session')->getFlashBag()->add('danger',$this->get('translator')->trans('tml.method_selected.not_valid.not_credit',array(),'backend'));
                return $this->redirect($this->generateUrl('app_admin_membership_index'));
            }
            return $this->redirect($this->generateUrl('app_admin_membership_complete', array('method'=>2)));
        }

        $gateway = Omnipay::getFactory()->create('PayPal_Express');
        $gateway->setUsername($this->get('service_container')->getParameter('app.omnipay_username'));
        $gateway->setPassword($this->get('service_container')->getParameter('app.omnipay_password'));
        $gateway->setSignature($this->get('service_container')->getParameter('app.omnipay_signature'));
        if($this->get('service_container')->getParameter('app.omnipay_test')) {
            $gateway->setTestMode(true);
        }
        // TODO: comment the next line
        return $this->redirect($this->generateUrl('app_admin_membership_complete')); //TODO; remove test restriction

        $response = $gateway->purchase(
            array(
                'cancelUrl' => $this->generateUrl('app_admin_membership_cancel', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'returnUrl' => $this->generateUrl('app_admin_membership_complete', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'description' => $this->get('translator')->trans('buy a %member% plan in %site%', array(
                    '%member%' => $membership, '%site%'=>$this->get('service_container')->getParameter('site.name')
                )),
                'amount' => $price,
                'currency' => $this->get('tml.configuration')->findByField('currency')->getValue()
            )
        )->send();

        return $this->redirect($response->redirect());
    }

    /**
     * @Route("/buy-complete", name="app_admin_membership_complete")
     */
    public function completeBuyAction() {
        $method = $this->get('request')->get('method',1);
        $token = $this->get('request')->query->get('token','666');
        $payerID = $this->get('request')->query->get('PayerID');

        $membership = $this->get('session')->get('membership');
        $code = $this->get('session')->get('code');
        $user = $this->get('fos_user.user_manager')->findUserBy(array('code'=>$code));
        $amount = $this->get('tml.configuration')->findByField($membership.'_price')->getValue();

        if(null !== $user) {
            $limit = (integer)$this->get('tml.configuration')->findByField($membership.'_limit_sponsor')->getValue();
            $counter = $this->get('tml.profile')->countRegisterBySponsor($user->getActiveProfile()->getId());
            if($limit <= $counter) {
                $amount = $this->get('tml.configuration')->findByField($membership.'_exceded_price')->getValue();
            }
        }

        if($method == 2) {

        } else {
            $gateway = Omnipay::getFactory()->create('PayPal_Express');
            $gateway->setUsername($this->get('service_container')->getParameter('app.omnipay_username'));
            $gateway->setPassword($this->get('service_container')->getParameter('app.omnipay_password'));
            $gateway->setSignature($this->get('service_container')->getParameter('app.omnipay_signature'));
            if($this->get('service_container')->getParameter('app.omnipay_test')) {
                $gateway->setTestMode(true);
            }
            $params = array(
                'cancelUrl' => $this->generateUrl('app_admin_membership_cancel', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'returnUrl' => $this->generateUrl('app_admin_membership_complete', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                'description' => $this->get('translator')->trans('buy a %member% plan in %site%', array(
                    '%member%' => $membership, '%site%'=>$this->get('service_container')->getParameter('site.name')
                )),
                'amount' => $amount,
                'currency' => $this->get('tml.configuration')->findByField('currency')->getValue()
            );
            // TODO: uncomment the next 2 lines
//            // TODO: remove test restriction
//            $response = $gateway->completePurchase($params)->send();
//            $paypalResponse = $response->getData();
        }

        if( true || 2 == $method || (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success' && $paypalResponse['TOKEN'] == $token)) {
            $user = $this->getUser();

            $this->get('tml.user_message')->registerMessage(
                $user,
                $this->get('translator')->trans('message.admin',array(),'messages'),
                $this->get('translator')->trans('message.membership_buy.title',array(),'messages'),
                $this->get('translator')->trans('message.membership_buy.message',array('%amount%'=>$amount),'messages')
            );

            $event = new MembershipBuy($user, $membership, $code, $amount, $method, $token);
            $this->get('event_dispatcher')->dispatch(AppEvents::MEMBERSHIP_BUY_VALID, $event);

            $memberPrefix = $this->get('tml.configuration')->findByField($membership.'_prefix')->getValue();
            $memberNumber = $this->get('tml.configuration')->findByField($membership.'_code');
            $memberNumberValue = (integer)$memberNumber->getValue();
            $user->setCode($memberPrefix.$memberNumberValue);
            $memberNumberValue++;
            $memberNumber->setValue((string)$memberNumberValue);
            $this->get('tml.configuration')->update($memberNumber);
            $this->get('fos_user.user_manager')->updateUser($user);

            /*if(('basic' == $membership || 'superior' == $membership || 'professional' == $membership) && null === $user->getBlog()) {
                $blog = $this->get('tml.blog')->getEntity();
                $blog->setTitle($this->get('service_container')->getParameter('app.blog.title'));
                $blog->setSubtitle($this->get('service_container')->getParameter('app.blog.subtitle'));
                $blog->setSlogan($this->get('service_container')->getParameter('app.blog.slogan'));
                $blog->setUser($user);
                $this->container->get('tml.blog')->update($blog);
            }*/
            // TODO: uncomment the next 2 lines
            $this->get('session')->remove('membership');
            $this->get('session')->remove('code');

            $event = new MembershipBuy($user, $membership, $code, $amount, $method, $token);
            $this->get('event_dispatcher')->dispatch(AppEvents::MEMBERSHIP_BUY_COMPLETED, $event);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('transaction.completed',array(),'backend'));
            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('plan.buy.successfully',array(),'backend'));
            return $this->redirect($this->generateUrl('app_admin_membership_index'));
        } else {
            $this->get('session')->getFlashBag()->add('danger',$this->get('translator')->trans('transaction.can.not.verified',array(),'backend'));
            return $this->redirect($this->generateUrl('app_admin_membership_index'));
        }
    }

    /**
     * @Route("/buy-cancel", name="app_admin_membership_cancel")
     */
    public function cancelBuyAction() {
        $membership = $this->get('session')->get('membership');
        $this->get('tml.user_message')->registerMessage(
            $this->getUser(),
            $this->get('translator')->trans('message.admin',array(),'messages'),
            $this->get('translator')->trans('message.membership_buy_cancel.title',array(),'messages'),
            $this->get('translator')->trans('message.membership_buy_cancel.message',array('%membership%'=>$membership),'messages')
        );

        $this->get('session')->remove('membership');
        $this->get('session')->remove('code');

        $this->get('session')->getFlashBag()->add('danger',$this->get('translator')->trans('transaction.canceled',array(),'backend'));
        return $this->redirect($this->generateUrl('app_admin_membership_index'));
    }

    /**
     * @Route("/cancel-membership", name="app_admin_membership_cancel_membership")
     * @return JsonResponse
     */
    public function requestMembershipDevolutionAction() {
        $account = $this->get('request')->request->get('account', null);
        $responseData = array('success'=>false);
        if(null === $account) {
            $responseData['message'] = $this->get('translator')->trans('paypal_account_required_for_devolution', array(), 'backend');
        } else {
            if ($this->getUser()->hasRole('ROLE_PROFESSIONAL')) {
                $price = $this->get('tml.configuration')->findByField('professional_price')->getValue();
            } elseif ($this->getUser()->hasRole('ROLE_SUPERIOR')) {
                $price = $this->get('tml.configuration')->findByField('superior_price')->getValue();
            } else {
                $price = $this->get('tml.configuration')->findByField('basic_price')->getValue();
            }

            $profile = $this->getUser()->getActiveProfile();
            if($price <= $profile->getCredit()) {
                $responseData['message'] = $this->get('translator')->trans('membership_can_not_cancel_because_your_comition_is_over_membership_cost', array(), 'backend');
            } else {
                $start = $this->getUser()->getActiveProfile()->getCreated()->format('Y-m-d H:i:s');
                $end = date('Y-m-d H:i:s');
                $comition = $this->get('tml.transfer')->getTotalForRange($this->getUser()->getId(), $start, $end);
                if($price <= $comition) {
                    $responseData['message'] = $this->get('translator')->trans('membership_can_not_cancel_because_your_comition_is_over_membership_cost', array(), 'backend');
                } else {
                    $user = $this->getUser();
                    $body = $this->renderView(':membership:email_comition_devolution.html.twig', array(
                        'name' => $user->getName(),'username'=>$user->getUsername(),'email'=>$user->getEmail(),
                        'paypal_email' => $account, 'price' => $price
                    ));
                    $message = \Swift_Message::newInstance();
                    $message
                        ->setFrom($this->get('service_container')->getParameter('mailer_user'))
                        ->setTo($this->get('service_container')->getParameter('devolution.notification_email'))
                        ->setSubject('Solicitud de Devolucion de Membresia')
                        ->setBody($body, 'text/html')
                    ;
                    $this->get('mailer')->send($message);
                    $user->setEnabled(false);
                    $user->setLocked(true);
                    $this->get('fos_user.user_manager')->updateUser($user);

                    $this->get('tml.log')->registerLog(
                        sprintf('The user %s has request a Membership devolution', $user->getUsername())
                    );

                    $responseData = array(
                        'success' => true,
                        'message' => $this->get('translator')->trans('membership_devolution_request_has_recived', array(), 'backend')
                    );
                }
            }
        }

        $response = new JsonResponse($responseData);
        return $response;
    }
}
