<?php


namespace App\Form;


use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('firstname', TextType::class)
            ->add('birthdate', BirthdayType::class, ['years' => range(date('Y') - 70, date('Y'))])
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('address', TextType::class)
            ->add('zipcode', NumberType::class, ['html5' => true])
            ->add('city', TextType::class)
            ->add('idCarFile', VichFileType::class, ['attr' => ['accept' => 'application/pdf']])
            ->add('save', SubmitType::class, ['label' => 'Créer mon compte']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Client::class
        ));
    }
}