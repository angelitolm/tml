<?php


namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Image;

class ProfileFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle','attr'=>array('icon'=>'fa fa-user')))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle','attr'=>array('icon'=>'fa fa-envelope')))
            ->add('title', null, array('label' => 'form.title', 'translation_domain' => 'FOSUserBundle','attr'=>array('icon'=>'fa fa-tag'),'required'=>false))
            ->add('dni', null, array('label' => 'form.dni', 'translation_domain' => 'FOSUserBundle','attr'=>array('icon'=>'icon-credit-card'),'required'=>false))
            ->add('phone', null, array('label' => 'form.phone', 'translation_domain' => 'FOSUserBundle','attr'=>array('icon'=>'fa fa-phone'),'required'=>false))
            ->add('address', null, array('label' => 'form.address', 'translation_domain' => 'FOSUserBundle','attr'=>array('icon'=>'fa fa-road'),'required'=>false))
            ->add('image','file', array('label'=>'form.picture','translation_domain'=>'FOSUserBundle','mapped'=>false,
                'required'=>false,'attr'=>array('icon'=>'fa fa-photo'),'constraints'=>array(
                    new Image(array(
                        'maxSize' => "5M"
                    ))
                )
            ))
            ->add('description', null, array('label'=>'form.description', 'translation_domain'=>'FOSUserBundle','attr'=>array(
                'icon' => 'fa fa-comment','rows'=>10
            )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'profile',
        ));
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getName()
    {
        return 'app_user_profile';
    }

}
