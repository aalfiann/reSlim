<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    //Hello user
    $app->get('/hello/{name}', function (Request $request, Response $response) {
        $username = $request->getAttribute('name');
        $response = $this->viewfrontend->render($response, "user.html", ["username" => $username, "router" => $this->router]);
        return $response;
    });