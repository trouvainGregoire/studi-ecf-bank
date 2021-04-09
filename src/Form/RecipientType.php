<?php

namespace App\Form;

use App\Entity\Recipient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('firstname', TextType::class)
            ->add('accountIdentifier', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Demander l\'ajout de ce bénéficiaire']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Recipient::class
        ));
    }
}