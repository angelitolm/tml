<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Blog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\VarDumper\VarDumper;

class BlogController extends Controller
{
    /**
     * @Route("/blog/{username}", name="app_front_blog")
     */
    public function indexAction($username)
    {
        //Noticias TML
        $latestNews = $this->get('tml.news')->getLatestNews(3);

        $user = $this->get('fos_user.user_manager')->findUserByUsername($username);
        if(null === $user) {
            throw new NotFoundHttpException();
        }

//        $pp  = dump($user);
//        exit;

        return $this->render(':Blog:index.html.twig', array(
            'blog'=>$user->getBlog(),
            'latestNews' => $latestNews,
            'user' => $user
        ));
    }

    /**
     * @Route("/admin/blog/details", name="app_admin_blog_details")
     */
    public function detailsAction()
    {
        return $this->render(':Blog:details.html.twig');
    }

    /**
     * @Route("/admin/blog/new", name="app_admin_blog_new")
     */
    public function newAction()
    {
        $entity = $this->get('tml.blog')->getEntity();
        $form = $this->createForm('app_blog', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $file = $this->get('request')->files->get('app_blog');
            $file = $file['image'];
            if(!empty($file)) {
                $uploadDir = Blog::IMAGES_UPLOAD_DIR;
                $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $name);
                $entity->setPicture($name);
            }
            $entity->setUser($this->getUser());
            $this->get('tml.blog')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('new_edit'))
                return $this->redirect($this->generateUrl('app_admin_blog_edit'));

            return $this->redirect($this->generateUrl('app_admin_blog_details'));
        }

        return $this->render(':Blog:new.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/admin/blog/edit", name="app_admin_blog_edit")
     */
    public function editAction()
    {
        $entity = $this->getUser()->getBlog();
        $form = $this->createForm('app_blog', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $file = $this->get('request')->files->get('app_blog');
            $file = $file['image'];
            if(!empty($file)) {
                $uploadDir = Blog::IMAGES_UPLOAD_DIR;
                $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $name);
                $entity->setPicture($name);
            }
            $this->get('tml.blog')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('new_edit'))
                return $this->redirect($this->generateUrl('app_admin_blog_edit'));

            return $this->redirect($this->generateUrl('app_admin_blog_details'));
        }

        return $this->render(':Blog:edit.html.twig', array('form'=>$form->createView()));
    }

}
