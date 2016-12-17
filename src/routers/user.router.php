<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    // GET example api user route directly from database
    $app->get('/user', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $results = $users->getAll();
        $body = $response->getBody();
        if ($results != 0){
            $body->write(json_encode(array("result" => $results, "status" => "success", "code" => $response->getStatusCode()), JSON_PRETTY_PRINT));
        } else {
            $body->write(json_encode(array("result" => 'no records found!', "status" => "success", "code" => $response->getStatusCode()), JSON_PRETTY_PRINT));
        }
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });