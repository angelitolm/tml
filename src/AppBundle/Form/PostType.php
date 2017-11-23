<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;

class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label'=>'posts.title', 'translation_domain'=>'backend','attr'=>array(
                'icon'=>'fa fa-tag'
            )))
            ->add('image','file', array('label'=>'posts.picture','translation_domain'=>'backend','mapped'=>false,
                'required'=>false,'attr'=>array('icon'=>'fa fa-photo'),'constraints'=>array(
                    new Image(array(
                        'maxSize' => "5M"
                    ))
                )
            ))
            ->add('description', null, array('label'=>'posts.description', 'translation_domain'=>'backend','attr'=>array(
                'icon' => 'fa fa-comment','rows'=>10
            )))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_post';
    }
}
