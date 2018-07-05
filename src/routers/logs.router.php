<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\middleware\ValidateParam as ValidateParam;
use \classes\CustomHandlers as CustomHandlers;

// POST api to append new log
$app->map(['POST','GET'],'/logs/data/append', function (Request $request, Response $response) {
    $datapost = $request->getParsedBody();    
    $this->logger->addInfo('{"code":'.json_encode($datapost['Code']).',"message":'.json_encode($datapost['Message']).'}',['created_by'=> $datapost['Name'],'email' => $datapost['Email'],'IP'=>$this->visitorip]);
    $body = $response->getBody();
    $lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
    $body->write('{"status":"success","code":"RS106",message":"'.CustomHandlers::getreSlimMessage('RS106',$lang).'"}');
    return classes\Cors::modify($response,$body,200);
})->add(new ValidateParam('Code','1-20','alphanumeric'))
    ->add(new ValidateParam('Message','1-250','required'))
    ->add(new ValidateParam('Name','1-50','required'))
    ->add(new ValidateParam('Email','6-50','email'));

// POST api to update data log
$app->post('/logs/data/update', function (Request $request, Response $response) {
    $logs = new classes\Logs($this->db);
    $logs->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
    $datapost = $request->getParsedBody();
    $logs->username = $datapost['Username'];
    $logs->token = $datapost['Token'];
    $logs->content = $datapost['Content'];
    $body = $response->getBody();
    $body->write($logs->updateLog());
    return classes\Cors::modify($response,$body,200);
})->add(new ValidateParam('Content'))
    ->add(new ValidateParam('Token','1-250','required'))
    ->add(new ValidateParam('Username','1-50','required'));

// GET api to clear data log
$app->get('/logs/data/clear/{username}/{token}', function (Request $request, Response $response) {
    $logs = new classes\Logs($this->db);
    $logs->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
    $logs->username = $request->getAttribute('username');
    $logs->token = $request->getAttribute('token');
    $body = $response->getBody();
    $body->write($logs->clearLog());
    return classes\Cors::modify($response,$body,200);
});