<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class NewsController
 * @package AppBundle\Controller
 *
 * @Route("/admin/news")
 */
class NewsController extends Controller
{
    /**
     * @Route("/", name="app_admin_news_index")
     */
    public function indexAction()
    {
        return  $this->render(':News:index.html.twig');
    }

    /**
     * @Route("/datatable", name="app_admin_news_datatable")
     */
    public function datatableAction()
    {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $elements = $this->get('tml.news')->getDatableElement($start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            $data[] = array(
                'title' => $element->getTitle(),
                'categories' => '',
                'created' => $element->getCreated()->format('Y-m-d H:i'),
                'actions'  => $this->renderView(':News:actions.html.twig', array('id'=>$element->getId()))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('tml.news')->getTotalElement(),
            "recordsFiltered" => $this->get('tml.news')->getTotalFilter($filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/new", name="app_admin_news_new")
     */
    public function newAction()
    {
        $entity = $this->get('tml.news')->getEntity();
        $form = $this->createForm('app_news', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $file = $this->get('request')->files->get('app_news');
            $file = $file['image'];
            if(!empty($file)) {
                $uploadDir = News::IMAGES_UPLOAD_DIR;
                $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $name);
                $entity->setImage($name);
            }
            $this->get('tml.news')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('new_edit'))
                return $this->redirect($this->generateUrl('app_admin_news_edit',array('id'=>$entity->getId())));

            if($this->get('request')->request->has('new_new'))
                return $this->redirect($this->generateUrl('app_admin_news_new'));

            return $this->redirect($this->generateUrl('app_admin_news_index'));
        }

        return $this->render(':News:new.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/edit/{id}", name="app_admin_news_edit")
     * @ParamConverter("entity")
     */
    public function editAction(News $entity)
    {
        $form = $this->createForm('app_news', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $file = $this->get('request')->files->get('app_news');
            $file = $file['image'];
            if(!empty($file)) {
                $uploadDir = News::IMAGES_UPLOAD_DIR;
                $name = md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $name);
                if(null !== $entity->getPicture()) {
                    $filename = $uploadDir.$entity->getPicture();
                    if(is_file($filename)) {
                        unlink($filename);
                    }
                }
                $entity->setImage($name);
            }
            $this->get('tml.news')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('update'))
                return $this->redirect($this->generateUrl('app_admin_news_edit',array('id'=>$entity->getId())));

            return $this->redirect($this->generateUrl('app_admin_news_index'));
        }

        return $this->render(':News:edit.html.twig', array('form'=>$form->createView(),'entity'=>$entity));
    }

    /**
     * @Route("/delete/{id}", name="app_admin_news_delete")
     * @ParamConverter("entity")
     */
    public function deleteAction(News $entity)
    {
        $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('delete.success',array(),'resources'));
        $uploadFile = News::IMAGES_UPLOAD_DIR.$entity->getImage();
        if(is_file($uploadFile)) {
            unlink($uploadFile);
        }
        $this->get('tml.news')->remove($entity);

        return $this->redirect($this->generateUrl('app_admin_news_index'));
    }

}
