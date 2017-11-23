<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostCommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label'=>'comment.name', 'translation_domain'=>'backend'))
            ->add('title',null, array('label'=>'comment.title', 'translation_domain'=>'backend'))
            ->add('message', null, array('label'=>'comment.description', 'translation_domain'=>'backend'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PostComment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_post_comment';
    }
}
