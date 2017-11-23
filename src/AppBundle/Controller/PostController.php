<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends Controller
{
    /**
     * @Route("/post/{id}", name="app_post_details")
     * @ParamConverter("entity")
     *
     */
    public function indexAction(Post $entity)
    {
        $comment = $this->get('tml.post_comment')->getEntity();
        $form = $this->createForm('app_post_comment', $comment);
        return $this->render('Post/index.html.twig', array(
            'post'=>$entity,
            'blog'=>$entity->getBlog(),
            'user'=>$entity->getBlog()->getUser(),
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/post/publish-comment/{id}", name="app_post_publish_comment")
     * @ParamConverter("entity")
     *
     */
    public function publishCommentAction(Post $entity) {
        $comment = $this->get('tml.post_comment')->getEntity();
        $comment->setPost($entity);
        $form = $this->createForm('app_post_comment', $comment);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $this->get('tml.post_comment')->update($comment);
        }

        return $this->redirect($this->generateUrl('app_post_details', array('id'=>$entity->getId())));
    }

    /**
     * @Route("/admin/post/list", name="app_admin_post_list")
     */
    public function listAction()
    {
        if(null == $this->getUser()->getBlog()) {
            $this->get('session')->getFlashBag()->add('danger',$this->get('translator')->trans('Is required you create first a Blog',array(),'backend'));
            return $this->redirect($this->generateUrl('app_admin_blog_new'));
        }
        return $this->render(':Post:list.html.twig');
    }

    /**
     * @Route("/admin/post/datatable", name="app_admin_post_datatable")
     */
    public function datatableAction() {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $blog = $this->getUser()->getBlog();
        $elements = $this->get('tml.post')->getDatableElement($blog->getId(),$start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            $data[] = array(
                'title' => $element->getTitle(),
                'comments' => $element->getComments()->count(),
                'created' => $element->getCreated()->format('Y-m-d H:i'),
                'actions'  => $this->renderView(':post:actions.html.twig', array('id'=>$element->getId(),'hasComment'=>$element->getComments()->count() > 0))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('tml.post')->getTotalElement($blog->getId()),
            "recordsFiltered" => $this->get('tml.post')->getTotalFilter($blog->getId(), $filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/admin/post/new", name="app_admin_post_new")
     */
    public function newAction()
    {
        $blog = $this->getUser()->getBlog();
        if(null == $blog) {
            $this->get('session')->getFlashBag()->add('danger',$this->get('translator')->trans('Is required you create first a Blog',array(),'backend'));
            return $this->redirect($this->generateUrl('app_admin_blog_new'));
        }
        $entity = $this->get('tml.post')->getEntity();
        $form = $this->createForm('app_post', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $entity->setBlog($blog);
            $file = $this->get('request')->files->get('app_post');
            $file = $file['image'];
            if(!empty($file)) {
                $uploadDir = Post::IMAGES_UPLOAD_DIR;
                $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $name);
                $entity->setPicture($name);
            }
            $this->get('tml.post')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('new_edit'))
                return $this->redirect($this->generateUrl('app_admin_post_edit',array('id'=>$entity->getId())));

            if($this->get('request')->request->has('new_new'))
                return $this->redirect($this->generateUrl('app_admin_post_new'));

            return $this->redirect($this->generateUrl('app_admin_post_list'));
        }

        return $this->render(':Post:new.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/admin/post/edit/{id}", name="app_admin_post_edit")
     * @ParamConverter("entity")
     */
    public function editAction(Post $entity)
    {
        $form = $this->createForm('app_post', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $file = $this->get('request')->files->get('app_post');
            $file = $file['image'];
            if(!empty($file)) {
                $uploadDir = Post::IMAGES_UPLOAD_DIR;
                $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $name);
                if(null !== $entity->getPicture()) {
                    $filename = $uploadDir.$entity->getPicture();
                    if(is_file($filename)) {
                        unlink($filename);
                    }
                }
                $entity->setPicture($name);
            }
            $this->get('tml.post')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('update'))
                return $this->redirect($this->generateUrl('app_admin_post_edit',array('id'=>$entity->getId())));

            return $this->redirect($this->generateUrl('app_admin_post_list'));
        }

        return $this->render(':Post:edit.html.twig', array('form'=>$form->createView(),'entity'=>$entity));
    }

    /**
     * @Route("/admin/post/delete/{id}", name="app_admin_post_delete")
     * @ParamConverter("entity")
     */
    public function deleteAction(Post $entity)
    {
        $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('delete.success',array(),'resources'));
        $uploadFile = Post::IMAGES_UPLOAD_DIR.$entity->getPicture();
        if(is_file($uploadFile)) {
            unlink($uploadFile);
        }
        $this->get('tml.post')->remove($entity);

        return $this->redirect($this->generateUrl('app_admin_post_list'));
    }

}
