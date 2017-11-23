<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;

class BlogType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label'=>'blog.title', 'translation_domain'=>'backend', 'attr'=>array(
                'icon'=>'fa fa-bookmark'
            )))
            ->add('subtitle', null, array('label'=>'blog.subtitle', 'translation_domain'=>'backend', 'attr'=>array(
                'icon'=>'fa fa-bookmark-o'
            )))
            ->add('slogan', null, array('label'=>'blog.slogan', 'translation_domain'=>'backend', 'attr'=>array(
                'icon'=>'fa fa-bullhorn'
            )))
            ->add('image','file', array('label'=>'blog.picture','translation_domain'=>'backend','mapped'=>false,
                'required'=>false,'attr'=>array('icon'=>'fa fa-photo'),'constraints'=>array(
                    new Image(array(
                        'maxSize' => "5M"
                    ))
                )
            ))
            ->add('about', null, array('label'=>'blog.about', 'translation_domain'=>'backend','attr'=>array(
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
            'data_class' => 'AppBundle\Entity\Blog'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_blog';
    }
}
