<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MauticPlugin\LodgeSubscriptionBundle\Entity\Settings;

class SubscriptionSettingsType extends AbstractType
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
                'year',
                ChoiceType::class,
                [
                    'label' => 'Year',
                    'required' => true,
                    'choices' => array_combine(
                        range((int) $currentYear, (int) $currentYear + 3),
                        range((int) $currentYear, (int) $currentYear + 3)
                    ),
                    'data' => (int) $currentYear,
                ]
            )
            ->add(
                'amountFull',
                NumberType::class,
                [
                    'label' => 'Full Subscription Amount',
                    'required' => true,
                    'attr' => [
                        'step' => 0.01,
                        'min' => 0,
                    ],
                ]
            )
            ->add(
                'amountReduced',
                NumberType::class,
                [
                    'label' => 'Reduced Subscription Amount',
                    'required' => true,
                    'attr' => [
                        'step' => 0.01,
                        'min' => 0,
                    ],
                ]
            )
            ->add(
                'amountHonorary',
                NumberType::class,
                [
                    'label' => 'Honorary Subscription Amount',
                    'required' => true,
                    'attr' => [
                        'step' => 0.01,
                        'min' => 0,
                    ],
                    'data' => 0,
                ]
            )
            ->add(
                'stripePublishableKey',
                TextType::class,
                [
                    'label' => 'Stripe Publishable Key',
                    'required' => false,
                ]
            )
            ->add(
                'stripeSecretKey',
                TextType::class,
                [
                    'label' => 'Stripe Secret Key',
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'stripeWebhookSecret',
                TextType::class,
                [
                    'label' => 'Stripe Webhook Secret',
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'lodgesubscription_settings';
    }
} 