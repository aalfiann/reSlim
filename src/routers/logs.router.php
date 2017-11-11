<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// POST api to update data log
$app->post('/logs/data/update', function (Request $request, Response $response) {
    $logs = new classes\Logs($this->db);
    $datapost = $request->getParsedBody();    
    $logs->username = $datapost['Username'];
    $logs->token = $datapost['Token'];
    $logs->content = $datapost['Content'];
    $body = $response->getBody();
    $body->write($logs->updateLog());
    return classes\Cors::modify($response,$body,200);
});

// GET api to clear data log
$app->get('/logs/data/clear/{username}/{token}', function (Request $request, Response $response) {
    $logs = new classes\Logs($this->db);
    $logs->username = $request->getAttribute('username');
    $logs->token = $request->getAttribute('token');
    $body = $response->getBody();
    $body->write($logs->clearLog());
    return classes\Cors::modify($response,$body,200);
});