<?php

return [
    'router' => 'Concrete\Core\Http\Middleware\Concrete\DispatcherMiddleware',
    'middleware' => [

        // Deliver from cache if available
        'cache' => ['Concrete\Core\Http\Middleware\Concrete\CacheMiddleware', 10],

        // Enable session, we want this to happen first
        'session' => ['Concrete\Core\Http\Middleware\Concrete\SessionMiddleware', 12],

        // Check if we're trying to install
        'install' => ['Concrete\Core\Http\Middleware\Concrete\InstallMiddleware', 14],

        // Load package autoloaders
        'package_autoload' => ['Concrete\Core\Http\Middleware\Concrete\PackageAutoloadMiddleware', 16],

        // Load preprocess file
        'preprocess' => ['Concrete\Core\Http\Middleware\Concrete\PreprocessMiddleware', 18],

        // Localization
        'localization' => ['Concrete\Core\Http\Middleware\Concrete\LocalizationMiddleware', 20],

        // Load package autoloaders
        'auto_update' => ['Concrete\Core\Http\Middleware\Concrete\AutomaticUpdateMiddleware', 22],

        // Load packages
        'package_startup' => ['Concrete\Core\Http\Middleware\Concrete\PackageStartupMiddleware', 24],

        // Load permissions
        'permission_key' => ['Concrete\Core\Http\Middleware\Concrete\PermissionKeyMiddleware', 26],

    ]
];
