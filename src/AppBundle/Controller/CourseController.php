<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CourseController
 * @package AppBundle\Controller
 *
 * @Route("/admin/courses")
 */
class CourseController extends Controller
{
    /**
     * @Route("/", name="app_admin_courses_index")
     */
    public function indexAction()
    {
        return $this->render(':Course:index.html.twig');
    }

    /**
     * @Route("/datatable", name="app_admin_courses_datatable")
     */
    public function datatableAction()
    {
        $start = $this->get('request')->query->get('start',0);
        $limit = $this->get('request')->query->get('length',10);
        $order = $this->get('request')->query->get('order');
        $filter = $this->get('request')->query->get('search');
        $elements = $this->get('tml.course')->getDatableElement($start, $limit, $order[0], $filter['value']);
        $data = array();
        foreach($elements as $element)
        {
            $data[] = array(
                'name' => $element->getName(),
                'field' => $element->getFile(),
                'membership' => $element->getHumanType(),
                'description' => $element->getDescription(),
                'actions'  => $this->renderView(':Course:actions.html.twig', array('id'=>$element->getId()))
            );
        }

        $data_response = array(
            "recordsTotal"    => $this->get('tml.course')->getTotalElement(),
            "recordsFiltered" => $this->get('tml.course')->getTotalFilter($filter['value']),
            "data"            => $data,
            "draw"            => $this->get('request')->query->get('draw',1)
        );

        $response = new JsonResponse($data_response);
        return $response;
    }

    /**
     * @Route("/new", name="app_admin_courses_new")
     */
    public function newAction()
    {
        $entity = $this->get('tml.course')->getEntity();
        $form = $this->createForm('app_course', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $file = $this->get('request')->files->get('app_course');
            $fileResource = $file['resource'];
            if(!empty($fileResource)) {
                $uploadDir = Course::FILE_UPLOAD_DIR;
                $name = md5($fileResource->getClientOriginalName()) . '.' . $fileResource->getClientOriginalExtension();
                $fileResource->move($uploadDir, $name);
                $entity->setFile($name);
            }
            $fileMedia = $file['media'];
            if(!empty($fileMedia)) {
                $uploadDir = Course::FILE_UPLOAD_DIR;
                $name = md5($fileMedia->getClientOriginalName()) . '.' . $fileMedia->getClientOriginalExtension();
                $fileMedia->move($uploadDir, $name);
                $entity->setImage($name);
            }
            $this->get('tml.course')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('new_edit'))
                return $this->redirect($this->generateUrl('app_admin_courses_edit',array('id'=>$entity->getId())));

            if($this->get('request')->request->has('new_new'))
                return $this->redirect($this->generateUrl('app_admin_courses_new'));

            return $this->redirect($this->generateUrl('app_admin_courses_index'));
        }

        return $this->render(':Course:new.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/edit/{id}", name="app_admin_courses_edit")
     * @ParamConverter("entity")
     */
    public function editAction(Course $entity)
    {
        $form = $this->createForm('app_course', $entity);
        $form->handleRequest($this->get('request'));
        if($form->isSubmitted() && $form->isValid()) {
            $file = $this->get('request')->files->get('app_course');
            $fileResource = $file['resource'];
            if(!empty($fileResource)) {
                $uploadDir = Course::FILE_UPLOAD_DIR;
                $name = md5($fileResource->getClientOriginalName()) . '.' . $fileResource->getClientOriginalExtension();
                $fileResource->move($uploadDir, $name);
                if(null !== $entity->getFile()) {
                    $filename = $uploadDir.$entity->getFile();
                    if(is_file($filename)) {
                        unlink($filename);
                    }
                }
                $entity->setFile($name);
            }
            $fileMedia = $file['media'];
            if(!empty($fileMedia)) {
                $uploadDir = Course::FILE_UPLOAD_DIR;
                $name = md5($fileMedia->getClientOriginalName()) . '.' . $fileMedia->getClientOriginalExtension();
                $fileMedia->move($uploadDir, $name);
                if(null !== $entity->getImage()) {
                    $filename = $uploadDir.$entity->getImage();
                    if(is_file($filename)) {
                        unlink($filename);
                    }
                }
                $entity->setImage($name);
            }
            $this->get('tml.course')->update($entity);

            $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('save_success',array(),'backend'));
            if($this->get('request')->request->has('update'))
                return $this->redirect($this->generateUrl('app_admin_courses_edit',array('id'=>$entity->getId())));

            return $this->redirect($this->generateUrl('app_admin_courses_index'));
        }

        return $this->render(':Course:edit.html.twig', array('form'=>$form->createView(),'entity'=>$entity));
    }

    /**
     * @Route("/delete/{id}", name="app_admin_courses_delete")
     * @ParamConverter("entity")
     */
    public function deleteAction(Course $entity)
    {
        $this->get('session')->getFlashBag()->add('success',$this->get('translator')->trans('delete.success',array(),'resources'));
        $uploadFile = Course::FILE_UPLOAD_DIR.$entity->getFile();
        if(is_file($uploadFile)) {
            unlink($uploadFile);
        }
        $this->get('tml.course')->remove($entity);

        return $this->redirect($this->generateUrl('app_admin_news_index'));
    }

    /**
     * @Route("/courses", name="app_admin_courses_list")
     */
    public function coursesAction() {
        $type = Course::$BASIC;
        if($this->getUser()->hasRole('ROLE_SUPERIOR'))
            $type = Course::$SUPERIOR;
        if($this->getUser()->hasRole('ROLE_PROFESSIONAL'))
            $type = Course::$PROFESSIONAL;

        $courses = $this->get('tml.course')->getCourseByType($type);

        return $this->render(':Course:list.html.twig', array('courses'=>$courses));
    }

}
