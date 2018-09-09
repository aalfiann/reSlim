<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\CustomHandlers as CustomHandlers;
use \classes\SimpleCache as SimpleCache;
use \classes\UniversalCache as UniversalCache;
use \classes\Auth as Auth;
use \classes\JSON as JSON;

    // Delete All cache data
    $app->get('/maintenance/cache/data/delete/{username}/{token}', function (Request $request, Response $response) {
        $usertoken = $request->getAttribute('token');
        $lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        if (Auth::validToken($this->db,$usertoken,$request->getAttribute('username'))){
            $roles = Auth::getRoleID($this->db,$usertoken);
            if ($roles == '1' || $roles == '2'){
                $datajson = SimpleCache::ClearAll();
            } else {
                $data = JSON::encode([
                    'status' => 'error',
                    'code' => 'RS404',
                    'message' => CustomHandlers::getreSlimMessage('RS404',$lang)
                ],true);
            }
        } else {
            $datajson = JSON::encode([
                'status' => 'error',
                'code' => 'RS401',
                'message' => CustomHandlers::getreSlimMessage('RS401',$lang)
            ],true);
        }
        $body = $response->getBody();
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200);
    });

    // Delete All cache api keys
    $app->get('/maintenance/cache/apikey/delete/{username}/{token}', function (Request $request, Response $response) {
        $usertoken = $request->getAttribute('token');
        $lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        if (Auth::validToken($this->db,$usertoken,$request->getAttribute('username'))){
            $roles = Auth::getRoleID($this->db,$usertoken);
            if ($roles == '1' || $roles == '2'){
                $datajson = Auth::deleteCacheAll();
            } else {
                $data = JSON::encode([
                    'status' => 'error',
                    'code' => 'RS404',
                    'message' => CustomHandlers::getreSlimMessage('RS404',$lang)
                ],true);
            }
        } else {
            $datajson = JSON::encode([
                'status' => 'error',
                'code' => 'RS401',
                'message' => CustomHandlers::getreSlimMessage('RS401',$lang)
            ],true);
        }
        $body = $response->getBody();
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200);
    });

    // Delete All cache universal
    $app->get('/maintenance/cache/universal/delete/{username}/{token}', function (Request $request, Response $response) {
        $usertoken = $request->getAttribute('token');
        $lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        if (Auth::validToken($this->db,$usertoken,$request->getAttribute('username'))){
            $roles = Auth::getRoleID($this->db,$usertoken);
            if ($roles == '1' || $roles == '2'){
                $datajson = UniversalCache::deleteCacheAll();
            } else {
                $data = JSON::encode([
                    'status' => 'error',
                    'code' => 'RS404',
                    'message' => CustomHandlers::getreSlimMessage('RS404',$lang)
                ],true);
            }
        } else {
            $datajson = JSON::encode([
                'status' => 'error',
                'code' => 'RS401',
                'message' => CustomHandlers::getreSlimMessage('RS401',$lang)
            ],true);
        }
        $body = $response->getBody();
        $body->write($datajson);
        return classes\Cors::modify($response,$body,200);
    });

    // Get information about cache data
    $app->get('/maintenance/cache/data/info', function (Request $request, Response $response) {
        $body = $response->getBody();
        $body->write(JSON::encode(SimpleCache::getCacheInfo(),true));
        return classes\Cors::modify($response,$body,200);
    });
    
    // Get information about cache api keys
    $app->get('/maintenance/cache/apikey/info', function (Request $request, Response $response) {
        $body = $response->getBody();
        $body->write(JSON::encode(Auth::getCacheInfo(),true));
        return classes\Cors::modify($response,$body,200);
    });

    // Get information about cache universal
    $app->get('/maintenance/cache/universal/info', function (Request $request, Response $response) {
        $body = $response->getBody();
        $body->write(JSON::encode(UniversalCache::getCacheInfo(),true));
        return classes\Cors::modify($response,$body,200);
    });

    // Listen cache data
    $app->post('/maintenance/cache/data/listen', function (Request $request, Response $response) {
        $datapost = $request->getParsedBody();
        $secretkey = (empty($datapost['secretkey'])?'':$datapost['secretkey']);
        $filepath = (empty($datapost['filepath'])?'':$datapost['filepath']);
        $content = (empty($datapost['content'])?'':$datapost['content']);
        $body = $response->getBody();
        $body->write(JSON::encode(SimpleCache::listen($secretkey,$filepath,$content),true));
        return classes\Cors::modify($response,$body,200);
    });

    // Listen to delete cache data
    $app->post('/maintenance/cache/data/listen/delete', function (Request $request, Response $response) {
        $datapost = $request->getParsedBody();
        $secretkey = (empty($datapost['secretkey'])?'':$datapost['secretkey']);
        $wildcard = (empty($datapost['wildcard'])?'':$datapost['wildcard']);
        $agecache = (empty($datapost['agecache'])?'':$datapost['agecache']);
        $body = $response->getBody();
        $body->write(JSON::encode(SimpleCache::listenToDelete($secretkey,$wildcard,$agecache),true));
        return classes\Cors::modify($response,$body,200);
    });