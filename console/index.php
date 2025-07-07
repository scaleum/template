<?php

require __DIR__ . '/../vendor/autoload.php';

use Application\Behaviors\LoggerAdapter;
use Application\Behaviors\Services;
use Scaleum\Http\Application;

// For our dev environment we will report all errors to the screen
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
// ini_set('display_startup_errors', FALSE);
// ini_set('display_errors', FALSE);

$app = new Application([
    'application_dir' => dirname(__DIR__, 1) . '/protected',
    'config_dir'      => dirname(__DIR__, 1) . '/protected/config',

    // 'environment'     => 'dev', // it can/will be calculated automatically

    // kernel configs(for overriding)
    // 'kernel'      => [
    // basic definitions, will be merged/overrided with the definitions from the kernel->config
    // 'definitions' => [
    //     'kernel.version'   => '1.0.0',
    //     'routes.file'      => 'routes.php',
    //     'routes.directory' => 'path/to/routes',
    // ],
    // ],
    // 'behaviors'   => [],
    // 'services'    => [],
]);

$app->bootstrap([
    // kernel configs
    'kernel'    => [
        // expansion of definitions, will merge/override definitions from kernel->config
        // 'definitions' => [
        //     'kernel.version'   => '1.0.0',
        //     'routes.file'      => 'routes.php',
        //     'routes.directory' => 'path/to/routes',
        // ],

        // config files which will be loaded on bootstrap
        // file names relative to the `application_dir/config` folder or full path
        'configs' => [
            'di.kernel.php',
            'di.logger-adapter.php',
            'di.services.php',
        ],
    ],

    // basic behaviors
    'behaviors' => [
        LoggerAdapter::class,
        Services::class
    ],

    // basic services
    // 'services'    => [],
]);

$app->run();
