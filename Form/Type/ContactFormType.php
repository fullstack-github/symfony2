<?php

namespace Dusk\UserBundle\Form\Type;
use Dusk\UserBundle\Entity\EnquiryCategory;
use Dusk\UserBundle\Entity\EnquiryCategoryRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ContactFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        // add your custom field
        $builder
                ->add('enquiry_category','entity',array(
            'class'=>'DuskUserBundle:EnquiryCategory',
            'empty_value' => 'Select Enquiry Type',
            'property'      => 'name',
            'query_builder' => function(EnquiryCategoryRepository $er) {
                return $er->createQueryBuilder('e')
                    ->add('orderBy','e.name ASC');
            }, 'label' => 'Enquiry Type',
            'required'=>false))
                ->add('name','text', array('label' => 'Your Name*', 'required' => true))
                ->add('email','email', array('label' => 'Your Email*', 'required' => true))
                ->add('phone','text', array('label' => 'Your Phone', 'required' => false))
                ->add('postcode','text', array('label' => 'Your Postcode', 'required' => false))
                ->add('message', 'textarea', array('label' => 'Your Message*', 'required' => true));
    }

    public function getName() {
        return 'dusk_contact';
    }

}

?>
