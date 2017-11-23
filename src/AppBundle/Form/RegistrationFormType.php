<?php


namespace AppBundle\Form;

use AppBundle\Constraints\Code;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code',null, array('label'=>'form.code', 'translation_domain' => 'FOSUserBundle','attr'=>array(
                /*'icon'=>'fa fa-barcode',*/'help'=>'Debe introducir un código válido'),'mapped'=>false,'required'=>true,'constraints'=> array(
                    new NotBlank(), new Code()
                )
            ))
            ->add('name',null,array('label'=>'form.name', 'translation_domain' => 'FOSUserBundle', 'attr'=>array(
                /*'icon'=>'fa fa-user',*/
            )))
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle','attr'=>array(
                /*'icon'=>'fa fa-user'*/
            )))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle','attr'=>array(
                /*'icon'=>'fa fa-envelope'*/
            )))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password','attr'=>array(/*'icon'=>'fa fa-lock'*/)),
                'second_options' => array('label' => 'form.password_confirmation','attr'=>array(/*'icon'=>'fa fa-lock'*/)),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('terms_condition','checkbox', array('label'=>'form.register_terms_condition','translation_domain'=>'FOSMessageBundle',
                'required'=>true,'mapped'=>false))
        ;
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}
