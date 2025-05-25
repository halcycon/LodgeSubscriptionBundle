<?php

namespace MauticPlugin\LodgeSubscriptionBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class LodgeSubscriptionIntegration extends BasicIntegration implements BasicInterface
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
} 