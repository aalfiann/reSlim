<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    // GET example api loop route directly from database
    $app->get('/api/loop', function (Request $request, Response $response) {
        $oStuff = new models\Starter($this->db);
        $results = $oStuff->getAll();
        $body = $response->getBody();
        if ($results != 0){
            $body->write(json_encode(array("result" => $results, "status" => "success", "code" => $response->getStatusCode()), JSON_PRETTY_PRINT));
        } else {
            $body->write(json_encode(array("result" => 'no records found!', "status" => "success", "code" => $response->getStatusCode()), JSON_PRETTY_PRINT));
        }
        return $response
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withBody($body);
    });