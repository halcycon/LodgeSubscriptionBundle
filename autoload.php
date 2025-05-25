<?php
// This file is for autoloading vendor libraries
$vendorDir = __DIR__ . '/../../../vendor';

// Check if vendor directory exists at this path (local dev)
if (!is_dir($vendorDir) && !file_exists($vendorDir . '/autoload.php')) {
    // Try to find the vendor directory from the current working directory (CI environment)
    $vendorDir = getenv('GITHUB_WORKSPACE') ? getenv('GITHUB_WORKSPACE') . '/vendor' : $vendorDir;
    
    // If still not found, try to locate it from the current directory
    if (!is_dir($vendorDir) && !file_exists($vendorDir . '/autoload.php')) {
        $vendorDir = getcwd() . '/vendor';
    }
}

if (file_exists($vendorDir . '/autoload.php')) {
    require_once $vendorDir . '/autoload.php';
} else {
    echo "Cannot find autoload.php in any of the expected locations.";
    exit(1);
} 