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

// Load all classes
$classes = glob('../classes/*.php');
foreach ($classes as $class) {
    require $class;
}

// Load all models
$models = glob('../models/*.php');
foreach ($models as $model) {
    require $model;
}

// Register component templates on container
if (strtolower($config['templateRender']) == "php")
{
    $container['viewbackend'] = new \Slim\Views\PhpRenderer('../templates/'.$container['settings']['theme'].'/backend');
    $container['viewfrontend'] = new \Slim\Views\PhpRenderer('../templates/'.$container['settings']['theme'].'/frontend');
}
else
{
    $container['viewbackend'] = function ($container) {
        if ($container['settings']['twigcache'] == true){
            $options = ['cache' => 'cache'];
        } else {
            $options = [];
        }
        
        $viewbackend = new \Slim\Views\Twig('../templates/'.$container['settings']['theme'].'/backend', $options);
    
        // Instantiate and add Slim specific extension
        $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
        $viewbackend->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

        return $viewbackend;
    };

    $container['viewfrontend'] = function ($container) {
        if ($container['settings']['twigcache'] == true){
            $options = ['cache' => 'cache'];
        } else {
            $options = [];
        }
        $viewfrontend = new \Slim\Views\Twig('../templates/'.$container['settings']['theme'].'/frontend', $options);
    
        // Instantiate and add Slim specific extension
        $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
        $viewfrontend->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

        return $viewfrontend;
    };
}


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
        return $response->withRedirect($container['request']->getUri()->getBasePath().'/404');
    };
};

// Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html')
            ->write('Method must be one of: ' . implode(', ', $methods));
    };
};

// Load all router files before run
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}

$app->run();

?>