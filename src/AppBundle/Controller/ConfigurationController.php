<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Configuration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ConfiguationController
 * @package AppBundle\Controller
 *
 * @Route("/admin/configuration")
 */
class ConfigurationController extends Controller
{
    /**
     * @Route("/", name="app_admin_configuration_index")
     */
    public function indexAction()
    {
        return $this->render(':Configuration:index.html.twig');
    }

    /**
     * @Route("/datatable", name="app_admin_configuration_datatable")
     */
    public function datatableAction()
    {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $elements = $this->get('tml.configuration')->getDatableElement($start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            $data[] = array(
                'field' => $element->getField(),
                'description' => $element->getDescription(),
                'value' => $element->getValue(),
                'actions'  => $this->renderView(':Configuration:actions.html.twig', array('id'=>$element->getId()))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('tml.configuration')->getTotalElement(),
            "recordsFiltered" => $this->get('tml.configuration')->getTotalFilter($filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/edit/{id}", name="app_admin_configuration_edit")
     * @ParamConverter("entity")
     */
    public function editAction(Configuration $entity)
    {
        $form = $this->createForm('app_configuration', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $this->get('tml.configuration')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('update'))
                return $this->redirect($this->generateUrl('app_admin_configuration_edit',array('id'=>$entity->getId())));

            return $this->redirect($this->generateUrl('app_admin_configuration_index'));
        }

        return $this->render(':Configuration:edit.html.twig', array('form'=>$form->createView(),'entity'=>$entity));
    }

}
