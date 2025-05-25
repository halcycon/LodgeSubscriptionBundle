<?php

declare(strict_types=1);

use Mautic\CoreBundle\DependencyInjection\MauticCoreExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $excludes = [
        'Integration/StripeIntegration.php',
    ];

    $services->load('MauticPlugin\\LodgeSubscriptionBundle\\', '../')
        ->exclude('../{'.implode(',', array_merge(MauticCoreExtension::DEFAULT_EXCLUDES, $excludes)).'}');
        
    // Load entity repositories
    $services->load('MauticPlugin\\LodgeSubscriptionBundle\\Entity\\', '../Entity/*Repository.php');
    
    // Load the Stripe integration separately since it has special requirements
    $services->set('lodge_subscription.integration.stripe', \MauticPlugin\LodgeSubscriptionBundle\Integration\StripeIntegration::class)
        ->args([
            '$eventDispatcher' => '@event_dispatcher',
            '$cacheStorageHelper' => '@mautic.helper.cache_storage',
            '$entityManager' => '@doctrine.orm.entity_manager',
            '$sessionFactory' => '@session.factory',
            '$requestStack' => '@request_stack',
            '$router' => '@router',
            '$translator' => '@translator',
            '$logger' => '@logger',
            '$encryptionHelper' => '@mautic.helper.encryption',
            '$leadModel' => '@mautic.lead.model.lead',
            '$companyModel' => '@mautic.lead.model.company',
            '$pathsHelper' => '@mautic.helper.paths',
            '$notificationModel' => '@mautic.core.model.notification',
            '$fieldModel' => '@mautic.lead.model.field',
            '$integrationEntityModel' => '@mautic.plugin.model.integration_entity',
            '$doNotContact' => '@mautic.lead.model.dnc',
        ])
        ->public();
}; 