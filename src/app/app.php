<?php
/**
 * reSlim is based on Slim Framework version 3.6 (http://slimframework.com)
 *
 * @link      https://github.com/aalfiann/reSlim
 * @copyright Copyright (c) 2016 M ABD AZIZ ALFIAN
 * @license   https://github.com/reSlim/blob/license.md (MIT License)
 */

// Load all class libraries
require '../vendor/autoload.php';
// Load config
require '../config.php';

// Set time zone
date_default_timezone_set($config['reslim']['timezone']);

// Set up dependencies
require __DIR__.'/dependencies.php';

// Set up middleware
require __DIR__.'/middleware.php';

// Load all router files before run
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}

$app->run();

?>