<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Recipient;
use App\Entity\Transaction;
use App\Service\ClientUtils;
use LogicException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class TransactionType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var ClientUtils
     */
    private $clientUtils;

    public function __construct(Security $security, ClientUtils $clientUtils)
    {
        $this->security = $security;
        $this->clientUtils = $clientUtils;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, ['required' => false])
            ->add('save', SubmitType::class, ['label' => 'Valider le virement']);

        /** @var Client $client */
        $client = $this->security->getUser();
        if (!$client) {
            throw new LogicException('The TransactionType cannot be used without an authenticated client!');
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($client) {
            $form = $event->getForm();

            $formOptions = [
                'class' => Recipient::class,
                'mapped' => false,
                'choice_label' => 'fullName',
                'invalid_message' => 'Ce bénéficiaire est invalide.',
                'choices' => $this->clientUtils->getActivatedRecipients($client)
            ];

            $form->add('amount', NumberType::class, ['invalid_message' => 'Le montant doit être un nombre positif.',
                'constraints' => [new LessThanOrEqual($client->getAccount()->getBalance(), null, 'Le montant ne doit pas être supérieur au solde de votre compte.')]]);

            $form->add('recipient', EntityType::class, $formOptions);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
