<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    // GET example api user
    $app->get('/user', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $results = $users->getAll();
        $body = $response->getBody();
        if ($results != 0){
            $body->write(json_encode([
                'result' => $results, 
                'status' => 'success', 
                'code' => $response->getStatusCode(),
                'message' => 'Data records found!'], JSON_PRETTY_PRINT));
        } else {
            $body->write(json_encode([
                'status' => 'error',
                'code' => '404',
                'message' => 'Records not found!'], JSON_PRETTY_PRINT));
        }
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
    });

    // POST example api logout user
    $app->post('/user/logout', function (Request $request, Response $response) {
    });

    // PUT example api update user
    $app->put('/user/update/{rs_token}', function (Request $request, Response $response) {
    });

    // DELETE example api logout user
    $app->delete('/user/delete/{rs_token}', function (Request $request, Response $response) {
    });