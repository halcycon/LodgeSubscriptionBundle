<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    // Register core models
    $services->set(\MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel::class)
        ->args([
            '$entityManager' => new Reference('doctrine.orm.entity_manager'),
            '$fieldModel' => new Reference('mautic.lead.model.field'),
        ]);
        
    $services->set(\MauticPlugin\LodgeSubscriptionBundle\Model\PaymentModel::class)
        ->args([
            '$entityManager' => new Reference('doctrine.orm.entity_manager'),
        ]);
        
    $services->set(\MauticPlugin\LodgeSubscriptionBundle\Model\YearEndModel::class)
        ->args([
            '$entityManager' => new Reference('doctrine.orm.entity_manager'),
            '$fieldModel' => new Reference('mautic.lead.model.field'),
        ]);
    
    // Register the main integration
    $services->set('mautic.integration.lodgesubscription', \MauticPlugin\LodgeSubscriptionBundle\Integration\LodgeSubscriptionIntegration::class)
        ->tag('mautic.integration')
        ->tag('mautic.basic_integration');
    
    // Backwards compatibility aliases
    $services->alias('lodge_subscription.model.subscription', \MauticPlugin\LodgeSubscriptionBundle\Model\SubscriptionModel::class);
    $services->alias('lodge_subscription.model.payment', \MauticPlugin\LodgeSubscriptionBundle\Model\PaymentModel::class);
    $services->alias('lodge_subscription.model.yearend', \MauticPlugin\LodgeSubscriptionBundle\Model\YearEndModel::class);
}; 