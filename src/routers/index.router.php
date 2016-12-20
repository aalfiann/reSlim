<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    // GET example api to show all data role
    $app->get('/', function (Request $request, Response $response) {
        $data = [
		    'status' => 'success',
			'code' => '0',
			'welcome' => 'Hello World, here is the default index reSlim',
            'author' => [
                'name' => 'M ABD AZIZ ALFIAN (aalfiann@gmail.com)',
                'github' => 'https://github.com/aalfian/reSlim',
                'license' => 'https://github.com/aalfiann/reSlim/blob/master/license.md'
            ],
            'how to use' => 'reSlim is using authentication by token. So You have to register and login to get generated new token.'
		];
        $body = $response->getBody();
        $body->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withBody($body);
    });