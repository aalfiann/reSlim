<?php
/**
 * reSlim is based on Slim Framework version 3 (http://slimframework.com)
 *
 * @link      https://github.com/aalfiann/reSlim
 * @copyright Copyright (c) 2016 M ABD AZIZ ALFIAN
 * @license   https://github.com/aalfiann/reSlim/blob/master/license.md (MIT License)
 */

// Load all class libraries
require '../vendor/autoload.php';
// Load config
require '../config.php';

// Autoload all external classes
spl_autoload_register(function ($classname) {
    require (realpath(__DIR__ . '/..'). '/'.str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php');
});

// Set time zone
date_default_timezone_set($config['reslim']['timezone']);

// Set up dependencies
require __DIR__.'/dependencies.php';

// Set up middleware
require __DIR__.'/middleware.php';

// Load all router files before run
$routers = glob('../routers/*.router.php',GLOB_NOSORT);
foreach ($routers as $router) {
    require $router;
}

$app->run();

?>