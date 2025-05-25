<?php

declare(strict_types=1);

namespace MauticPlugin\LodgeSubscriptionBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class StripeIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    public const INTEGRATION_NAME = 'LodgeSubscription';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::INTEGRATION_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName(): string
    {
        return 'Lodge Subscription Manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return 'plugins/LodgeSubscriptionBundle/Assets/images/lodge.png';
    }
} 