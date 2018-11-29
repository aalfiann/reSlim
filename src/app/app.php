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

// Declare reSlim Version
define('RESLIM_VERSION','1.22.0');
// Declare reSlim built-in cache
define('AUTH_CACHE',$config['reslim']['authcache']);
define('SIMPLE_CACHE',$config['reslim']['simplecache']);
define('UNIVERSAL_CACHE',$config['reslim']['universalcache']);
// Define cache transfer
define('CACHE_TRANSFER',$config['cache']['transfer']);
define('CACHE_SECRET_KEY',$config['cache']['secretkey']);
define('CACHE_LISTENFROM',json_encode($config['cache']['listenfrom']));
// Declare Redis
define('REDIS_ENABLE',$config['redis']['enable']);
define('REDIS_PARAMETER',json_encode($config['redis']['parameter']));
define('REDIS_OPTION',json_encode($config['redis']['option']));

// Autoload all external classes
spl_autoload_register(function ($classname) {
    require (realpath(__DIR__ . '/..').DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php');
});

// Set time zone
date_default_timezone_set($config['reslim']['timezone']);

// Set up router cache if enabled in config
if ($config['router']['enableCache'] == true) {
    if (!is_dir($config['router']['folderCache'])) mkdir($config['router']['folderCache'],0775,true);
    $config['routerCacheFile'] = $config['router']['folderCache'].DIRECTORY_SEPARATOR.$config['router']['fileCache'];
} else {
    if (file_exists($config['router']['folderCache'].DIRECTORY_SEPARATOR.$config['router']['fileCache'])) {
        unlink($config['router']['folderCache'].DIRECTORY_SEPARATOR.$config['router']['fileCache']);
    }
}

// Set up dependencies
require __DIR__.'/dependencies.php';

// Set up middleware
require __DIR__.'/middleware.php';

// Load all router files before run
$routers = \classes\helper\Scanner::fileSearch('../routers/','router.php');
foreach ($routers as $router) {
    require $router;
}

// Load all modules router files before run
$modrouters = \classes\helper\Scanner::fileSearch('../modules/','router.php');
foreach ($modrouters as $modrouter) {
    require $modrouter;
}

// Release unecessary memory
unset($routers);
unset($modrouters);

$app->run();

?>