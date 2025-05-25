<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\IntegrationInterface;
use MauticPlugin\LodgeSubscriptionBundle\Form\Type\ConfigIntegrationType;

class LodgeSubscriptionIntegration extends BasicIntegration implements BasicInterface, ConfigFormInterface, IntegrationInterface
{
    public const NAME = 'LodgeSubscription';
    
    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return 'Lodge Subscription Manager';
    }

    public function getIcon(): string
    {
        return 'plugins/LodgeSubscriptionBundle/Assets/img/icon.png';
    }

    public function getConfigFormName(): string
    {
        return ConfigIntegrationType::class;
    }

    /**
     * Return the content template for the form.
     */
    public function getConfigFormContentTemplate(): ?string
    {
        return null;
    }

    /**
     * Return default configuration for the integration.
     */
    public function getDefaultConfigFormData(): array
    {
        return [
            'standard_subscription_amount' => 250.00,
            'senior_subscription_amount' => 125.00,
            'stripe_webhook_secret' => '',
            'payment_success_url' => '',
            'payment_cancel_url' => '',
        ];
    }
} 