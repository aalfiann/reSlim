<?php
/**
 * iSlim3 is based on Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/aalfiann/iSlim3
 * @copyright Copyright (c) 2016 M ABD AZIZ ALFIAN
 * @license   https://github.com/iSlim3/license.md (MIT License)
 */

// Load all class libraries
require '../vendor/autoload.php';
// Load config
require '../config.php';


// Create container
$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();


// Register component Monolog
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

// Register component database connection on container
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// Override the default Not Found Handler
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $custom = new classes\Custom();
        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->write($custom->prettyPrint('{ "status": "error", "code": "404", "message": "Request not found!" }'));
    };
};

// Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        $custom = new classes\Custom();
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->write($custom->prettyPrint('{ "status": "error", "code": "405", "message": "Method must be one of: ' . implode(', ', $methods).'" }'));
    };
};

// Load all router files before run
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}

$app->run();

?>