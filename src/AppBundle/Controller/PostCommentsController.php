<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PostComment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostCommentsController extends Controller
{
    /**
     * @Route("/admin/post-comments/{post}", name="app_admin_post_comments_list")
     */
    public function indexAction($post) {
        return $this->render(':post_comments:index.html.twig', array('post'=>$post));
    }

    /**
     * @Route("/admin/post-comments/datatable/{post}", name="app_admin_post_comments_datatable")
     */
    public function datatableAction($post) {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $elements = $this->get('tml.post_comment')->getDatableElement($post,$start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            $data[] = array(
                'title' => $element->getTitle(),
                'author' => $element->getName(),
                'created' => $element->getCreated()->format('Y-m-d H:i'),
                'actions'  => $this->renderView(':post_comments:actions.html.twig', array('id'=>$element->getId()))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('tml.post_comment')->getTotalElement($post),
            "recordsFiltered" => $this->get('tml.post_comment')->getTotalFilter($post, $filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/post-comments/new/{post}", name="app_front_post_comments_new")
     */
    public function newAction()
    {
        return array(
                // ...
            );
    }

    /**
     * @Route("/admin/post-comments/details/{id}", name="app_admin_post_comments_details")
     * @ParamConverter("entity")
     */
    public function detailsAction(PostComment $entity)
    {
        return $this->render(':post_comments:details.html.twig', array('postComment'=>$entity));
    }

    /**
     * @Route("/admin/post-comments/delete/{id}", name="app_admin_post_comments_delete")
     * @ParamConverter("entity")
     */
    public function deleteAction(PostComment $entity)
    {
        $post = $entity->getPost()->getId();
        $this->get('tml.post_comment')->remove($entity);
        $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('delete.success',array(),'resources'));

        return $this->redirect($this->generateUrl('app_admin_post_comments_list',array('post'=>$post)));
    }

}
