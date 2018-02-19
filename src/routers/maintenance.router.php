<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\CustomHandlers as CustomHandlers;
use \classes\Auth as Auth;

    // Delete All cache data
    $app->get('/maintenance/cache/data/delete/{username}/{token}', function (Request $request, Response $response) {
        $usertoken = $request->getAttribute('token');
        if (Auth::validToken($this->db,$usertoken,$request->getAttribute('username'))){
            if (Auth::getRoleID($this->db,$usertoken) == '1'){
                $datajson = \classes\SimpleCache::ClearAll();
            } else {
                $data = json_encode([
                    'status' => 'error',
                    'code' => 'RS404',
                    'message' => CustomHandlers::getreSlimMessage('RS404')
                ]);
            }
        } else {
            $datajson = json_encode([
                'status' => 'error',
                'code' => 'RS401',
                'message' => CustomHandlers::getreSlimMessage('RS401')
            ]);
        }
        $body = $response->getBody();
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200);
    });

    // Delete All cache api keys
    $app->get('/maintenance/cache/apikey/delete/{username}/{token}', function (Request $request, Response $response) {
        $usertoken = $request->getAttribute('token');
        if (Auth::validToken($this->db,$usertoken,$request->getAttribute('username'))){
            if (Auth::getRoleID($this->db,$usertoken) == '1'){
                $datajson = Auth::deleteCacheAll();
            } else {
                $data = json_encode([
                    'status' => 'error',
                    'code' => 'RS404',
                    'message' => CustomHandlers::getreSlimMessage('RS404')
                ]);
            }
        } else {
            $datajson = json_encode([
                'status' => 'error',
                'code' => 'RS401',
                'message' => CustomHandlers::getreSlimMessage('RS401')
            ]);
        }
        $body = $response->getBody();
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200);
    });