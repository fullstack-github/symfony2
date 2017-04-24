<?php

namespace Dusk\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Dusk\UserBundle\Entity\CountryRepository;
use Dusk\UserBundle\Entity\StateRepository;
use Symfony\Component\Form\AbstractType;


class UserFormType extends AbstractType  {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {$builder->add('firstname');
        $builder->add('lastname');
        $builder->add('email', 'email');
        $builder->add('company_name');
        $builder->add('country','entity',array(
            'class'=>'DuskUserBundle:Country',
            'empty_value' => 'Please Select',
            'property'      => 'name',
            'query_builder' => function(CountryRepository $er) {
                return $er->createQueryBuilder('c')
                    ->add('orderBy','c.name ASC');
            },  
            'required'=>true));
        $builder->add('address1');
        $builder->add('address2', null, array('required' => false));
        $builder->add('state','entity',array(
            'class'=>'DuskUserBundle:State',
            'empty_value' => 'Please Select',
            'property'      => 'state_name',
            'query_builder' => function(StateRepository $er) {
                return $er->createQueryBuilder('c')
                    ->add('orderBy','c.state_name ASC');
            },  
            'required'=>true));
        $builder->add('zipcode');
        $builder->add('phone1');
        $builder->add('phone2');
        $builder->add('username', null);
        $builder->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ));
    }

    public function getName()
    {
        return 'dusk_user_edit';
    }
}

?>
