<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ComemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label'=>'comember.name','translation_domain'=>'backend'))
            ->add('mail', 'email', array('label'=>'comember.mail','translation_domain'=>'backend'))
            ->add('dni', null, array('label'=>'comember.dni','translation_domain'=>'backend'))
            ->add('address', null, array('label'=>'comember.address','translation_domain'=>'backend'))
            ->add('phone', null, array('label'=>'comember.phone','translation_domain'=>'backend'))
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comember',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_comember';
    }
}
