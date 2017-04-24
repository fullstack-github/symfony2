<?php

namespace Dusk\UserBundle\Form\Type;
use Dusk\UserBundle\Entity\EnquiryCategory;
use Dusk\UserBundle\Entity\EnquiryCategoryRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class NewsletterType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        // add your custom field
        $builder
                ->add('name','text', array('required' => true))
                ->add('email','email', array('required' => true))
                ;
    }

    public function getName() {
        return 'dusk_newsletter';
    }

}

?>
