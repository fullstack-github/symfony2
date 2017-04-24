<?php

namespace Dusk\UserBundle\Form\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class InviteFriendType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        // add your custom field
        $builder->add('email','email', array('required' => true));
    }

    public function getName() {
        return 'dusk_invite_friend';
    }

}

?>
