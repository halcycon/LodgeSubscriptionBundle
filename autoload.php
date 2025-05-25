<?php

// Include Mautic's autoloader
$mauticAutoloader = __DIR__ . '/../../../vendor/autoload.php';

if (file_exists($mauticAutoloader)) {
    require_once $mauticAutoloader;
}

// If you need to register additional namespaces or paths, you can do so here
// For example:
// $loader = new \Composer\Autoload\ClassLoader();
// $loader->addPsr4('MauticPlugin\\LodgeSubscriptionBundle\\', __DIR__);
// $loader->register(); 