<?php

// Auto-loader for plugin testing
$pluginDir = __DIR__;
$vendorDir = dirname(dirname(dirname($pluginDir))) . '/vendor';

if (file_exists($vendorDir . '/autoload.php')) {
    require_once $vendorDir . '/autoload.php';
} else {
    throw new \RuntimeException('Unable to find autoload.php. Please run composer install.');
} 