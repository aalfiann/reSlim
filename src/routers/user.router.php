<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    // GET example api to show all data role
    $app->get('/user/role/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->showOptionRole());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to show all data status
    $app->get('/user/status/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->showOptionStatus());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to show all data user with pagination
    $app->get('/user/data/{page}/{itemsperpage}/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->page = $request->getAttribute('page');
        $users->itemsPerPage = $request->getAttribute('itemsperpage');
        $users->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->showAllAsPagination());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to search data user with pagination
    $app->get('/user/data/search/{page}/{itemsperpage}/{token}/', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->search = filter_var($_GET['query'],FILTER_SANITIZE_STRING);
        $users->page = $request->getAttribute('page');
        $users->itemsPerPage = $request->getAttribute('itemsperpage');
        $users->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->searchAllAsPagination());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to show profile user (for public is need an api key)
    $app->get('/user/profile/{username}/', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->username = $request->getAttribute('username');
        $body = $response->getBody();
        $body->write($users->showUserPublic());
        return classes\Cors::modify($response,$body,200);
    })->add(new \classes\middleware\ApiKey(filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING)));

    // GET example api to show profile user (for internal is need an authentication token)
    $app->get('/user/profile/{username}/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->token = $request->getAttribute('token');
        $users->username = $request->getAttribute('username');
        $body = $response->getBody();
        $body->write($users->showUser());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to verify user token
    $app->get('/user/verify/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->verifyToken());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to get role user token
    $app->get('/user/scope/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->getRole());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to show all data user
    $app->get('/user/{token}', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($users->showAll());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api to show all data user
    $app->post('/user', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        $users->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->showAll());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api register user
    $app->post('/user/register', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();    
        $users->username = $datapost['Username'];
        $users->password = $datapost['Password'];
        $users->fullname = $datapost['Fullname'];
        $users->address = $datapost['Address'];
        $users->phone = $datapost['Phone'];
        $users->email = $datapost['Email'];
        $users->aboutme = $datapost['Aboutme'];
        $users->avatar = $datapost['Avatar'];
        $users->role = $datapost['Role'];
        $body = $response->getBody();
        $body->write($users->register());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api login user
    $app->post('/user/login', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();       
        $users->username = $datapost['Username'];
        $users->password = $datapost['Password'];
        $body = $response->getBody();
        $body->write($users->login());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api logout user
    $app->post('/user/logout', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();    
        $users->username = $datapost['Username'];
        $users->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->logout());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api update user
    $app->post('/user/update', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody(); 
        $users->username = $datapost['Username'];
        $users->fullname = $datapost['Fullname'];
        $users->address = $datapost['Address'];
        $users->phone = $datapost['Phone'];
        $users->email = $datapost['Email'];
        $users->aboutme = $datapost['Aboutme'];
        $users->avatar = $datapost['Avatar'];
        $users->role = $datapost['Role'];
        $users->status = $datapost['Status'];
        $users->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->update());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api delete user
    $app->post('/user/delete', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        $users->username = $datapost['Username'];
        $users->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->delete());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api change password
    $app->post('/user/changepassword', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();  
        $users->username = $datapost['Username'];
        $users->password = $datapost['Password'];
        $users->newPassword = $datapost['NewPassword'];
        $users->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->changePassword());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api reset password
    $app->post('/user/resetpassword', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();     
        $users->username = $datapost['Username'];
        $users->newPassword = $datapost['NewPassword'];
        $users->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->resetPassword());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api upload
    $app->post('/user/upload', function (Request $request, Response $response) {
        $upload = new classes\Upload($this->db);
        $datapost = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        if (empty($files['Datafile'])) {
            throw new Exception('Expected a newfile');
        }
        $upload->username = $datapost['Username'];
        $upload->title = $datapost['Title'];
        $upload->alternate = $datapost['Alternate'];
        $upload->externallink = $datapost['External'];
        $upload->datafile = $files['Datafile'];
        $upload->token = $datapost['Token'];
        $upload->baseurl = $request->getUri()->getBaseUrl();
        $body = $response->getBody();
        $body->write($upload->process());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api update user upload item
    $app->post('/user/upload/update', function (Request $request, Response $response) {
        $upload = new classes\Upload($this->db);
        $datapost = $request->getParsedBody();
        $upload->username = $datapost['Username'];
        $upload->title = $datapost['Title'];
        $upload->alternate = $datapost['Alternate'];
        $upload->externallink = $datapost['External'];
        $upload->status = $datapost['Status'];
        $upload->itemid = $datapost['ItemID'];
        $upload->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($upload->update());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api delete user upload item
    $app->post('/user/upload/delete', function (Request $request, Response $response) {
        $upload = new classes\Upload($this->db);
        $datapost = $request->getParsedBody();
        $upload->username = $datapost['Username'];
        $upload->itemid = $datapost['ItemID'];
        $upload->token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($upload->delete());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to show all data status for upload
    $app->get('/user/upload/status/{token}', function (Request $request, Response $response) {
        $upload = new classes\Upload($this->db);
        $upload->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($upload->showOptionStatus());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to show all data user upload with pagination
    $app->get('/user/{username}/upload/data/{page}/{itemsperpage}/{token}', function (Request $request, Response $response) {
        $upload = new classes\Upload($this->db);
        $upload->page = $request->getAttribute('page');
        $upload->itemsPerPage = $request->getAttribute('itemsperpage');
        $upload->token = $request->getAttribute('token');
        $upload->username = $request->getAttribute('username');
        $body = $response->getBody();
        $body->write($upload->showAllAsPagination());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to search all data user upload with pagination
    $app->get('/user/{username}/upload/data/search/{page}/{itemsperpage}/{token}/', function (Request $request, Response $response) {
        $upload = new classes\Upload($this->db);
        $upload->page = $request->getAttribute('page');
        $upload->itemsPerPage = $request->getAttribute('itemsperpage');
        $upload->token = $request->getAttribute('token');
        $upload->username = $request->getAttribute('username');
        $upload->search = filter_var($_GET['query'],FILTER_SANITIZE_STRING);
        $upload->baseurl = $request->getUri()->getBaseUrl();
        $body = $response->getBody();
        $body->write($upload->searchAllAsPagination());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to show user upload item (need an api key)
    $app->get('/user/{username}/upload/data/item/{itemid}/', function (Request $request, Response $response) {
        $upload = new classes\Upload($this->db);
        $upload->username = $request->getAttribute('username');
        $upload->itemid = $request->getAttribute('itemid');
        $body = $response->getBody();
        $body->write($upload->showItem());
        return classes\Cors::modify($response,$body,200);
    })->add(new \classes\middleware\ApiKey(filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING)));

    // POST example api user forgot password
    $app->post('/user/forgotpassword', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();    
        $users->email = $datapost['Email'];
        $body = $response->getBody();
        $body->write($users->generatePassKey());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api verify passkey to reset password
    $app->post('/user/verifypasskey', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        $users->passKey = $datapost['PassKey'];
        $users->newPassword = $datapost['NewPassword'];
        $body = $response->getBody();
        $body->write($users->verifyPassKey());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api create new API Key
    $app->post('/user/keys/create', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        $users->token = $datapost['Token'];
        $users->username = $datapost['Username'];
        $users->domain = $datapost['Domain'];
        $body = $response->getBody();
        $body->write($users->generateApiKey());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api update status API Key
    $app->post('/user/keys/update', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        $users->token = $datapost['Token'];
        $users->username = $datapost['Username'];
        $users->apikey = $datapost['ApiKey'];
        $users->status = $datapost['Status'];
        $body = $response->getBody();
        $body->write($users->updateApiKey());
        return classes\Cors::modify($response,$body,200);
    });

    // POST example api delete API Key
    $app->post('/user/keys/delete', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $datapost = $request->getParsedBody();
        $users->token = $datapost['Token'];
        $users->username = $datapost['Username'];
        $users->apikey = $datapost['ApiKey'];
        $body = $response->getBody();
        $body->write($users->deleteApiKey());
        return classes\Cors::modify($response,$body,200);
    });

    // GET example api to search all data user api keys with pagination
    $app->get('/user/{username}/keys/data/search/{page}/{itemsperpage}/{token}/', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $users->page = $request->getAttribute('page');
        $users->itemsPerPage = $request->getAttribute('itemsperpage');
        $users->token = $request->getAttribute('token');
        $users->username = $request->getAttribute('username');
        $users->search = filter_var($_GET['query'],FILTER_SANITIZE_STRING);
        $body = $response->getBody();
        $body->write($users->searchAllApiKeysAsPagination());
        return classes\Cors::modify($response,$body,200);
    });