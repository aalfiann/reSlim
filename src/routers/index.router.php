<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    // GET example api to show all data role
    $app->get('/', function (Request $request, Response $response) {
        $data = [
		    'status' => 'success',
			'code' => '200',
			'welcome' => 'Hello World, here is the default index reSlim',
            'author' => [
                'name' => 'M ABD AZIZ ALFIAN (aalfiann@gmail.com)',
                'github' => 'https://github.com/aalfian/reSlim',
                'license' => 'https://github.com/aalfiann/reSlim/blob/master/license.md'
            ],
            'how to use' => 'reSlim is using authentication by token. So You have to register and login to get generated new token.'
		];
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag30min.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write(json_encode($data, JSON_PRETTY_PRINT));
        return classes\Cors::modify($response,$body,200);
    });