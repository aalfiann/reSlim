<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    //Hello Word
    $app->get('/', function (Request $request, Response $response) {
        $oStuff = new models\Starter();
        $hello = $oStuff->setHello();

        $response = $this->viewfrontend->render($response, "index.html", [
            "hello" => $hello['hello'],
            "description1" => $hello['description1'],
            "description2" => $hello['description2'],
            "author" => $hello['author'],
            "router" => $this->router]);
        return $response;
    })->setName("/");
