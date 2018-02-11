<?php

// Create container
$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
$app->add(new \Slim\HttpCache\Cache('public',604800));

// Register component Http-cache
$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

// Default generate eTag per 5minutes
$container['etag'] = function(){
    $fix = date('Y-m-d H:');
    $rate = date('i');
    $maxminute = 60;
    $intervalminute = 5;

    $n=0;
    for ($i = 0; $i <= $maxminute; $i+=$intervalminute) {
        if($i<=$rate) {$n++;}
    }
    return md5($fix.$n);
};

// Default generate eTag per 30minutes
$container['etag30min'] = function(){
    $fix = date('Y-m-d H:');
    $rate = date('i');
    $maxminute = 60;
    $intervalminute = 30;

    $n=0;
    for ($i = 0; $i <= $maxminute; $i+=$intervalminute) {
        if($i<=$rate) {$n++;}
    }
    return md5($fix.$n);
};

// Default generate eTag per every hour
$container['etag1hour'] = function(){
    $fix = date('Y-m-d ');
    $rate = date('H');
    $maxhour = 24;
    $intervalhour = 1;

    $n=0;
    for ($i = 0; $i <= $maxhour; $i+=$intervalhour) {
        if($i<=$rate) {$n++;}
    }
    return md5($fix.$n);
};

// Default generate eTag per 2hours
$container['etag2hour'] = function(){
    $fix = date('Y-m-d ');
    $rate = date('H');
    $maxhour = 24;
    $intervalhour = 2;

    $n=0;
    for ($i = 0; $i <= $maxhour; $i+=$intervalhour) {
        if($i<=$rate) {$n++;}
    }
    return md5($fix.$n);
};

// Register component Monolog
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('reSlim_logger');
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

// Register component phpmailer on container
$container['mail'] = function ($c) { return new PHPMailer; };

// Override the default Not Found Handler
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $data = [
            'status' => 'error',
            'code' => '404',
            'message' => $response->withStatus(404)->getReasonPhrase()
        ];
        return $container->get('response')
            ->withStatus(404)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->write(json_encode($data, JSON_PRETTY_PRINT));
    };
};

// Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        $data = [
            'status' => 'error',
            'code' => '405',
            'message' => $response->withStatus(405)->getReasonPhrase().', method must be one of: ' . implode(', ', $methods)
        ];
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->write(json_encode($data, JSON_PRETTY_PRINT));
    };
};

// Override the slim error handler
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $container->logger->addInfo('{ 
"code": "'.$exception->getCode().'", 
"message": "'.$exception->getMessage().'",
"file": "'.$exception->getFile().'",
"line": "'.$exception->getLine().'"}');
        $response->getBody()->rewind();
        $data = [
            'status' => 'error',
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ];
        return $response
            ->withStatus(500)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->write(json_encode($data, JSON_PRETTY_PRINT));
    };
};

// Override PHP 7 error handler
$container['phpErrorHandler'] = function ($container) {
    return $container['errorHandler'];
};

//PHP 5 Error Handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});