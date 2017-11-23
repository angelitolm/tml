<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;

class NewsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label'=>'news.title','translation_domain'=>'backend', 'attr'=>array(
                'icon'=>'fa fa-bookmark'
            )))
            ->add('categories',null,array('label'=>'news.categories','translation_domain'=>'backend','attr'=>array(
                'icon'=>'fa fa-tags'
            )))
            ->add('image','file', array('label'=>'news.picture','translation_domain'=>'backend','mapped'=>false,
                'required'=>false,'attr'=>array('icon'=>'fa fa-photo'),'constraints'=>array(
                    new Image(array(
                        'maxSize' => "5M"
                    ))
                )
            ))
            ->add('body', null, array('label'=>'news.body', 'translation_domain'=>'backend','attr'=>array(
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
            'data_class' => 'AppBundle\Entity\News'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_news';
    }
}
