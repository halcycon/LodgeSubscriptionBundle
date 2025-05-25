<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentYear = date('Y');

        $builder
            ->add(
                'contactId',
                TextType::class,
                [
                    'label' => 'Contact ID',
                    'attr' => [
                        'readonly' => true,
                    ],
                ]
            )
            ->add(
                'contactName',
                TextType::class,
                [
                    'label' => 'Contact Name',
                    'attr' => [
                        'readonly' => true,
                    ],
                    'mapped' => false,
                ]
            )
            ->add(
                'amount',
                NumberType::class,
                [
                    'label' => 'Amount',
                    'required' => true,
                    'attr' => [
                        'step' => 0.01,
                        'min' => 0,
                    ],
                ]
            )
            ->add(
                'paymentDate',
                DateType::class,
                [
                    'label' => 'Payment Date',
                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'data' => new \DateTime(),
                ]
            )
            ->add(
                'paymentMethod',
                ChoiceType::class,
                [
                    'label' => 'Payment Method',
                    'required' => true,
                    'choices' => [
                        'Cash' => 'Cash',
                        'Card' => 'Card',
                        'Bank Transfer' => 'Bank Transfer',
                        'Cheque' => 'Cheque',
                    ],
                ]
            )
            ->add(
                'year',
                ChoiceType::class,
                [
                    'label' => 'Year',
                    'required' => true,
                    'choices' => array_combine(
                        range((int) $currentYear - 3, (int) $currentYear + 1),
                        range((int) $currentYear - 3, (int) $currentYear + 1)
                    ),
                    'data' => (int) $currentYear,
                ]
            )
            ->add(
                'isArrears',
                CheckboxType::class,
                [
                    'label' => 'Is Arrears Payment',
                    'required' => false,
                ]
            )
            ->add(
                'notes',
                TextareaType::class,
                [
                    'label' => 'Notes',
                    'required' => false,
                ]
            )
            ->add(
                'transactionId',
                TextType::class,
                [
                    'label' => 'Transaction ID',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'lodgesubscription_payment';
    }
} 