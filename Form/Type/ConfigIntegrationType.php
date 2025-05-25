<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigIntegrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'standard_subscription_amount',
            MoneyType::class,
            [
                'label'       => 'lodge_subscription.config.standard_subscription_amount',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.standard_subscription_amount.tooltip',
                ],
                'required'    => true,
                'currency'    => 'GBP',
                'scale'       => 2,
            ]
        );

        $builder->add(
            'senior_subscription_amount',
            MoneyType::class,
            [
                'label'       => 'lodge_subscription.config.senior_subscription_amount',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.senior_subscription_amount.tooltip',
                ],
                'required'    => true,
                'currency'    => 'GBP',
                'scale'       => 2,
            ]
        );

        $builder->add(
            'stripe_webhook_secret',
            TextType::class,
            [
                'label'       => 'lodge_subscription.config.stripe_webhook_secret',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.stripe_webhook_secret.tooltip',
                ],
                'required'    => false,
            ]
        );

        $builder->add(
            'payment_success_url',
            UrlType::class,
            [
                'label'       => 'lodge_subscription.config.payment_success_url',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.payment_success_url.tooltip',
                ],
                'required'    => false,
            ]
        );

        $builder->add(
            'payment_cancel_url',
            UrlType::class,
            [
                'label'       => 'lodge_subscription.config.payment_cancel_url',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.payment_cancel_url.tooltip',
                ],
                'required'    => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'integration' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'integration_config';
    }
} 

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigIntegrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'standard_subscription_amount',
            MoneyType::class,
            [
                'label'       => 'lodge_subscription.config.standard_subscription_amount',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.standard_subscription_amount.tooltip',
                ],
                'required'    => true,
                'currency'    => 'GBP',
                'scale'       => 2,
            ]
        );

        $builder->add(
            'senior_subscription_amount',
            MoneyType::class,
            [
                'label'       => 'lodge_subscription.config.senior_subscription_amount',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.senior_subscription_amount.tooltip',
                ],
                'required'    => true,
                'currency'    => 'GBP',
                'scale'       => 2,
            ]
        );

        $builder->add(
            'stripe_webhook_secret',
            TextType::class,
            [
                'label'       => 'lodge_subscription.config.stripe_webhook_secret',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.stripe_webhook_secret.tooltip',
                ],
                'required'    => false,
            ]
        );

        $builder->add(
            'payment_success_url',
            UrlType::class,
            [
                'label'       => 'lodge_subscription.config.payment_success_url',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.payment_success_url.tooltip',
                ],
                'required'    => false,
            ]
        );

        $builder->add(
            'payment_cancel_url',
            UrlType::class,
            [
                'label'       => 'lodge_subscription.config.payment_cancel_url',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'   => 'form-control',
                    'tooltip' => 'lodge_subscription.config.payment_cancel_url.tooltip',
                ],
                'required'    => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'integration' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'integration_config';
    }
} 