<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\CustomHandlers as CustomHandler;

    // GET example api to show all data role
    $app->get('/user/role/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->Token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->showOptionRole());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // GET example api to show all data status
    $app->get('/user/status/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->Token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->showOptionStatus());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

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

    // GET example api to show profile user (doesn't need a authentication)
    $app->get('/user/profile/{username}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->Username = $request->getAttribute('username');
        $body = $response->getBody();
        $body->write($users->showUser());
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

    // POST example api update user
    $app->post('/user/update', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        
        $users->Username = $datapost['Username'];
        $users->Fullname = $datapost['Fullname'];
        $users->Address = $datapost['Address'];
        $users->Phone = $datapost['Phone'];
        $users->Email = $datapost['Email'];
        $users->Aboutme = $datapost['Aboutme'];
        $users->Avatar = $datapost['Avatar'];
        $users->Role = $datapost['Role'];
        $users->Status = $datapost['Status'];
        $users->Token = $datapost['Token'];

        $body = $response->getBody();
        $body->write($users->update());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // POST example api delete user
    $app->post('/user/delete', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        
        $users->Username = $datapost['Username'];
        $users->Token = $datapost['Token'];

        $body = $response->getBody();
        $body->write($users->delete());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });

    // POST example api change password
    $app->post('/user/changepassword', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        
        $users->Username = $datapost['Username'];
        $users->NewPassword = $datapost['NewPassword'];
        $users->Token = $datapost['Token'];

        $body = $response->getBody();
        $body->write($users->changePassword());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });