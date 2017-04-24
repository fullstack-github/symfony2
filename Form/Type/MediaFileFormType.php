<?php

namespace Dusk\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaFileFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('file', 'file', array('label' => 'File', 'required' =>  false))
                ->add('link', 'text', array('required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Dusk\UserBundle\Entity\MediaFile',
        ));
    }

    public function getName() {
        return 'dusk_media_file';
    }

}

?>
