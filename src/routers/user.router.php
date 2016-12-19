<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\CustomHandlers as CustomHandler;

    // GET example api to show all data user
    $app->get('/user/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->Token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->showAll());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // POST example api to show all data user
    $app->post('/user', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        $users->Token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->showAll());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // POST example api register user
    $app->post('/user/register', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        
        $users->Username = $datapost['Username'];
        $users->Password = $datapost['Password'];
        $users->Fullname = $datapost['Fullname'];
        $users->Address = $datapost['Address'];
        $users->Phone = $datapost['Phone'];
        $users->Email = $datapost['Email'];
        $users->Aboutme = $datapost['Aboutme'];
        $users->Avatar = $datapost['Avatar'];
        $users->Role = $datapost['Role'];

        $body = $response->getBody();
        $body->write($users->register());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // POST example api login user
    $app->post('/user/login', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        
        $users->Username = $datapost['Username'];
        $users->Password = $datapost['Password'];
        $body = $response->getBody();
        $body->write($users->login());

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // POST example api logout user
    $app->post('/user/logout', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        
        $users->Username = $datapost['Username'];
        $users->Token = $datapost['Token'];

        $body = $response->getBody();
        $body->write($users->logout());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // PUT example api update user
    $app->put('/user/update', function (Request $request, Response $response) {
    });

    // DELETE example api logout user
    $app->delete('/user/delete', function (Request $request, Response $response) {
    });