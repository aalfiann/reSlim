<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\middleware\ValidateParam as ValidateParam;
use \classes\middleware\ValidateParamURL as ValidateParamURL;
use \classes\middleware\ValidateParamJSON as ValidateParamJSON;

// GET api to development test purpose
$app->get('/dev/response/test', function (Request $request, Response $response) {
    $body = $response->getBody();
    $body->write('{"result":"make sure here is already json formatted."}');
    return classes\Cors::modify($response,$body,200);
});

// Test middleware ValidateParam class
$app->post('/dev/middleware/test/param/body', function (Request $request, Response $response) {
    $body = $response->getBody();
    $body->write('{"result":"Congrats, Validation body form is passed successfully."}');
    return classes\Cors::modify($response,$body,200);
})->add(new ValidateParam(['username'],'6-50','required'))
    ->add(new ValidateParam(['address'],'','required'))
    ->add(new ValidateParam(['phone'],'1-15','notzero'))
    ->add(new ValidateParam(['fax'],'0-15','numeric'))
    ->add(new ValidateParam(['email'],'0-50','email'))
    ->add(new ValidateParam(['fullname','aboutme'],'0-160'));

// Test middleware ValidateParamJSON class
$app->post('/dev/middleware/test/param/json', function (Request $request, Response $response) {
    $body = $response->getBody();
    $body->write('{"result":"Congrats, Validation JSON is passed successfully."}');
    return classes\Cors::modify($response,$body,200);
})->add(new ValidateParamJSON(['username'],'6-50','required'))
    ->add(new ValidateParamJSON(['address'],'','required'))
    ->add(new ValidateParamJSON(['phone'],'1-15','notzero'))
    ->add(new ValidateParamJSON(['fax'],'0-15','numeric'))
    ->add(new ValidateParamJSON(['email'],'0-50','email'))
    ->add(new ValidateParamJSON(['fullname','aboutme'],'0-160'));

// Test middleware ValidateParamURL class
$app->get('/dev/middleware/test/param/url/', function (Request $request, Response $response) {
    $body = $response->getBody();
    $body->write('{"result":"Congrats, Validation query parameter URL is passed successfully."}');
    return classes\Cors::modify($response,$body,200);
})->add(new ValidateParamURL(['username'],'6-50','required'))
    ->add(new ValidateParamURL(['address'],'','required'))
    ->add(new ValidateParamURL(['phone'],'1-15','notzero'))
    ->add(new ValidateParamURL(['fax'],'0-15','numeric'))
    ->add(new ValidateParamURL(['email'],'0-50','email'))
    ->add(new ValidateParamURL(['fullname','aboutme'],'0-160'));