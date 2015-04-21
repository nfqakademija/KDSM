<?php
/**
 * Created by PhpStorm.
 * User: Dainius
 * Date: 4/19/2015
 * Time: 08:00
 */

namespace KDSM\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder
            ->add('cardId', 'integer', array(
                'label' => 'Kortelės ID',
                'data' => '0'
            ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'kdsm_content_registration';
    }
}