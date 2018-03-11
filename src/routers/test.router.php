<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// GET api to development test purpose
$app->get('/dev/example/test', function (Request $request, Response $response) {
    $body = $response->getBody();
    $body->write('{"result":"make sure here is already json formatted."}');
    return classes\Cors::modify($response,$body,200);
});