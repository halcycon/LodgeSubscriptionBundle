<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    // Load all classes in the bundle for autowiring
    $services->load('MauticPlugin\\LodgeSubscriptionBundle\\', '../')
        ->exclude('../{Entity,Tests,Migrations}');
}; 