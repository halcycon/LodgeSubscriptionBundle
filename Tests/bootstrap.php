<?php

declare(strict_types=1);

// Find the autoloader from vendor/autoload.php
$vendorDir = __DIR__.'/../../../vendor';

if (!file_exists($vendorDir.'/autoload.php')) {
    throw new \RuntimeException('Install dependencies using Composer to run the test suite.');
}

$autoload = require $vendorDir.'/autoload.php'; 