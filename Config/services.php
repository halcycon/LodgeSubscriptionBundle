<?php

declare(strict_types=1);

use Mautic\CoreBundle\DependencyInjection\MauticCoreExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $excludes = [
        'Integration', // Exclude all integrations from autowiring
        'Entity',      // Exclude Entity directory entirely
    ];

    $services->load('MauticPlugin\\LodgeSubscriptionBundle\\', '../')
        ->exclude('../{'.implode(',', array_merge(MauticCoreExtension::DEFAULT_EXCLUDES, $excludes)).'}');
    
    // Register the main integration manually
    $services->set('mautic.integration.lodgesubscription', \MauticPlugin\LodgeSubscriptionBundle\Integration\LodgeSubscriptionIntegration::class)
        ->tag('mautic.integration')
        ->tag('mautic.basic_integration');
    
    // Register repositories by getting them from the entity manager
    $services->set('MauticPlugin\\LodgeSubscriptionBundle\\Entity\\PaymentRepository')
        ->factory([new Reference('doctrine.orm.entity_manager'), 'getRepository'])
        ->args(['MauticPlugin\\LodgeSubscriptionBundle\\Entity\\Payment']);
        
    $services->set('MauticPlugin\\LodgeSubscriptionBundle\\Entity\\SettingsRepository')
        ->factory([new Reference('doctrine.orm.entity_manager'), 'getRepository'])
        ->args(['MauticPlugin\\LodgeSubscriptionBundle\\Entity\\Settings']);
        
    $services->set('MauticPlugin\\LodgeSubscriptionBundle\\Entity\\YearEndLogRepository')
        ->factory([new Reference('doctrine.orm.entity_manager'), 'getRepository'])
        ->args(['MauticPlugin\\LodgeSubscriptionBundle\\Entity\\YearEndLog']);
    
    // Load the Stripe integration separately since it has special requirements
    $services->set('lodge_subscription.integration.stripe', \MauticPlugin\LodgeSubscriptionBundle\Integration\StripeIntegration::class)
        ->args([
            '$eventDispatcher' => new Reference('event_dispatcher'),
            '$cacheStorageHelper' => new Reference('mautic.helper.cache_storage'),
            '$entityManager' => new Reference('doctrine.orm.entity_manager'),
            '$sessionFactory' => new Reference('session.factory'),
            '$requestStack' => new Reference('request_stack'),
            '$router' => new Reference('router'),
            '$translator' => new Reference('translator'),
            '$logger' => new Reference('logger'),
            '$encryptionHelper' => new Reference('mautic.helper.encryption'),
            '$leadModel' => new Reference('mautic.lead.model.lead'),
            '$companyModel' => new Reference('mautic.lead.model.company'),
            '$pathsHelper' => new Reference('mautic.helper.paths'),
            '$notificationModel' => new Reference('mautic.core.model.notification'),
            '$fieldModel' => new Reference('mautic.lead.model.field'),
            '$integrationEntityModel' => new Reference('mautic.plugin.model.integration_entity'),
            '$doNotContact' => new Reference('mautic.lead.model.dnc'),
        ])
        ->public();
    
    // Preserve backwards compatibility for services defined in config.php
    // These aliases ensure old service IDs still work
    $services->alias('lodge_subscription.model.subscription', \MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel::class);
    $services->alias('lodge_subscription.model.payment', \MauticPlugin\LodgeSubscriptionBundle\Model\PaymentModel::class);
    $services->alias('lodge_subscription.model.yearend', \MauticPlugin\LodgeSubscriptionBundle\Model\YearEndModel::class);
    $services->alias('lodge_subscription.helper.stripe', \MauticPlugin\LodgeSubscriptionBundle\Helper\StripeHelper::class);
}; 