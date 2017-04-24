<?php

namespace Dusk\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Dusk\UserBundle\Entity\CountryRepository;
use Dusk\UserBundle\Entity\StateRepository;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;


class RegistrationFormType extends BaseType  {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('firstname',null, array('required' => true));
        $builder->add('lastname',null, array('required' => true));
        $builder->add('email',null, array('required' => true));
        $builder->add('company_name',null, array('required' => true));
        $builder->add('country','entity',array(
            'class'=>'DuskUserBundle:Country',
            'empty_value' => 'Please Select',
            'property'      => 'name',
            'query_builder' => function(CountryRepository $er) {
                return $er->createQueryBuilder('c')
                    ->add('orderBy','c.name ASC');
            },  
            'required'=>true));
//        $builder->add('agree', 'checkbox', array('mapped' => false));
        $builder->add('address1',null, array('required' => true));
        $builder->add('address2');
        $builder->add('state','entity',array(
            'class'=>'DuskUserBundle:State',
            'empty_value' => 'Please Select',
            'property'      => 'state_name',
            'query_builder' => function(StateRepository $er) {
                return $er->createQueryBuilder('c')
                    ->add('orderBy','c.state_name ASC');
            },  
            'required'=>true));
        $builder->add('zipcode',null, array('required' => true));
        $builder->add('phone1',null, array('required' => true));
        $builder->add('phone2',null, array('required' => true));
       
       // $builder->remove('username');
    }

    public function getName()
    {
        return 'dusk_user_registration';
    }
}

?>
