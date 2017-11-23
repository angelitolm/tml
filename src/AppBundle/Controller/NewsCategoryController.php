<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NewsCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class NewsCategoryController
 * @package AppBundle\Controller
 *
 * @Route("/admin/news-category")
 */
class NewsCategoryController extends Controller
{
    /**
     * @Route("/", name="app_admin_news_category_index")
     */
    public function indexAction()
    {
        return $this->render(':NewsCategory:index.html.twig');
    }

    /**
     * @Route("/datatable", name="app_admin_news_category_datatable")
     */
    public function datatableAction()
    {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $elements = $this->get('tml.news_category')->getDatableElement($start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            $data[] = array(
                'category' => $element->getCategory(),
                'created' => $element->getCreated()->format('Y-m-d H:i'),
                'actions'  => $this->renderView(':NewsCategory:actions.html.twig', array('id'=>$element->getId()))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('tml.news_category')->getTotalElement(),
            "recordsFiltered" => $this->get('tml.news_category')->getTotalFilter($filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/new", name="app_admin_news_category_new")
     */
    public function newAction()
    {
        $entity = $this->get('tml.news_category')->getEntity();
        $form = $this->createForm('app_news_category', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $this->get('tml.news_category')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('new_edit'))
                return $this->redirect($this->generateUrl('app_admin_news_category_edit',array('id'=>$entity->getId())));

            if($this->get('request')->request->has('new_new'))
                return $this->redirect($this->generateUrl('app_admin_news_category_new'));

            return $this->redirect($this->generateUrl('app_admin_news_category_index'));
        }

        return $this->render(':NewsCategory:new.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/edit/{id}", name="app_admin_news_category_edit")
     * @ParamConverter("entity")
     */
    public function editAction(NewsCategory $entity)
    {
        $form = $this->createForm('app_news_category', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $this->get('tml.news_category')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('update'))
                return $this->redirect($this->generateUrl('app_admin_news_category_edit',array('id'=>$entity->getId())));

            return $this->redirect($this->generateUrl('app_admin_news_category_index'));
        }

        return $this->render(':NewsCategory:edit.html.twig', array('form'=>$form->createView(),'entity'=>$entity));
    }

    /**
     * @Route("/delete/{id}", name="app_admin_news_category_delete")
     * @ParamConverter("entity")
     */
    public function deleteAction(NewsCategory $entity)
    {
        $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('delete.success',array(),'resources'));
        $this->get('tml.news_category')->remove($entity);

        return $this->redirect($this->generateUrl('app_admin_news_category_index'));
    }

}
