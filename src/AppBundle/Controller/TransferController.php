<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class TransferController
 * @package AppBundle\Controller
 *
 * @Route("/admin/transfer")
 */
class TransferController extends Controller
{
    /**
     * @Route("/", name="app_admin_transfer_index")
     */
    public function indexAction()
    {
        $lastTransfer = $this->get('tml.transfer')->getLatestTransfer($this->getUser()->getId(), 10);

        return $this->render(':transfer:index.html.twig',array('lastTransfer'=>$lastTransfer));
    }

    /**
     * @Route("/cobrar-comision", name="app_admin_transfer_cobrar_comision")
     */
    public function cobrarComisionAction() {
        $account = $this->get('request')->request->get('account');
        $amount = $this->get('request')->request->get('amount', 0);
        $responseData = array('success'=>false);
        if(empty($account) || empty($amount)) {
            $responseData['message'] = $this->get('translator')->trans('the_account_and_amount_is_required',array(),'backend');
        } elseif($amount > $this->getUser()->getActiveProfile()->getCredit()) {
            $responseData['message'] = $this->get('translator')->trans('the_amount_defined_is_not_valid_greather_than_the_user_credit',array(),'backend');
        } else {
            $user = $this->getUser();
            $profile = $user->getActiveProfile();
            $profile->decreaseCredit($amount);
            $this->get('tml.profile')->update($profile);
            $body = $this->renderView(':transfer:email_comition.html.twig', array(
                'name' => $user->getName(),'username'=>$user->getUsername(),'email'=>$user->getEmail(),
                'paypal_email' => $account, 'amount' => $amount, 'credit' => $profile->getCredit()
            ));
            $message = \Swift_Message::newInstance();
            $message
                ->setFrom($this->get('service_container')->getParameter('mailer_user'))
                ->setTo($this->get('service_container')->getParameter('comition.notification_email'))
                ->setSubject('Solicitud de Pago de Comision')
                ->setBody($body, 'text/html')
            ;
            $this->get('mailer')->send($message);

            $transfer = $this->get('tml.transfer')->getEntity();
            $transfer->setAmount($amount);
            $transfer->setCode(date('YmdHis'));
            $transfer->setDescription('Pago de Comision');
            $transfer->setTransferType(Transfer::$PAY_COMISION);
            $transfer->setUser($user);
            $this->get('tml.transfer')->update($transfer);

            $this->get('tml.log')->registerLog(
                sprintf('The user %s has request a cash a commission to %d', $user->getUsername(), $amount)
            );

            $responseData = array(
                'success' => true,
                'message' => $this->get('translator')->trans('pay_comision_request_has_received',array(),'backend')
            );
        }


        $response = new JsonResponse($responseData);
        return $response;
    }
}
