<?php
/**
 * reSlim is based on Slim Framework version 3 (http://slimframework.com)
 *
 * @link      https://github.com/aalfiann/reSlim
 * @copyright Copyright (c) 2016 M ABD AZIZ ALFIAN
 * @license   https://github.com/aalfiann/reSlim/blob/master/license.md (MIT License)
 */

// reSlim Version
define('RESLIM_VERSION','1.14.1');

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

// Set up router cache if enabled in config
if ($config['router']['enableCache'] == true) {
    if (!is_dir($config['router']['folderCache'])) mkdir($config['router']['folderCache'],0775,true);
    $config['routerCacheFile'] = $config['router']['folderCache'].'/'.$config['router']['fileCache'];
} else {
    if (file_exists($config['router']['folderCache'].'/'.$config['router']['fileCache'])) {
        unlink($config['router']['folderCache'].'/'.$config['router']['fileCache']);
    }
}

// Set up dependencies
require __DIR__.'/dependencies.php';

// Set up middleware
require __DIR__.'/middleware.php';

// Set up scanner files
if (!function_exists('glob_recursive')) {
    function glob_recursive($pattern, $flags = 0){
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }    
}

// Load all router files before run
$routers = glob('../routers/*.router.php',GLOB_NOSORT);
foreach ($routers as $router) {
    require $router;
}

// Load all modules router files before run
$modrouters = glob_recursive('../modules/*.router.php',GLOB_NOSORT);
foreach ($modrouters as $modrouter) {
    require $modrouter;
}

$app->run();

?>