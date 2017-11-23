<?php

namespace AppBundle\Form;

use AppBundle\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label'=>'course.name', 'translation_domain'=>'backend', 'attr'=>array(
                'icon'=>'fa fa-bookmark'
            )))
            ->add('type','choice', array('label'=>'course.type','translation_domain'=>'backend', 'attr'=>array(
                'icon' => 'fa fa-list'),'choices'=>Course::getTypeList()))
            ->add('resource','file', array('label'=>'course.file','translation_domain'=>'backend','mapped'=>false,
                'required'=>false,'attr'=>array('icon'=>'fa fa-file')))
            ->add('media','file', array('label'=>'course.image','translation_domain'=>'backend','mapped'=>false,
                'required'=>false,'attr'=>array('icon'=>'fa fa-file')))
            ->add('description', null, array('label'=>'course.description', 'translation_domain'=>'backend','attr'=>array(
                'icon' => 'fa fa-comment','rows'=>5
            )))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Course'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_course';
    }
}
