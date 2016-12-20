reSlim
=======
[![Build](https://img.shields.io/badge/build-on process-red.svg)](https://github.com/aalfiann/reSlim)
[![Version](https://img.shields.io/badge/version-1.1.0-red.svg)](https://github.com/aalfiann/reSlim)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/aalfiann/reSlim/blob/master/license.md)

reSlim is Lightweight, Fast, Secure and Powerful rest api.<br>
reSlim is based on [Slim Framework version 3.6](http://www.slimframework.com/).<br>

System Requirements
---------------
1. Web server with URL rewriting
2. PHP 5.5 or newer


Getting Started
---------------
1. Get or download the project
2. Install it using Composer

Folder System
---------------
* database
    * reSlim.sql (Structure database in reSlim to work with default example)
* src/
    * api/
    * app/
    * classes/
        * Auth.php (Default classes for handling authentication in reSlim way)
        * BaseConverter.php (Core for encryption that used in reSlim)
        * CustomHandlers.php (Default handle message in reSlim)
        * User.php (Default classes for user management in reSlim)
    * logs/
    * routers/
	    * name.router.php (routes by functionalities)
* test/
    * reSlim User.postman_collection.json (Is the file to run example test in PostMan)

### api/
    
Here is the place to run your application

### app/

Here is the place for slim framework

### classes/

Add your in classes here.
We are using PDO MySQL for the Database.


### logs/

Your custom log will be place in here as default.
You can add your custom log in your any container or router.

Example adding custom log in a router
```php
$app->post('/user/new', function (Request $request, Response $response) {
    echo 'This is a POST route';
    $this->logger->addInfo("Response post is succesfully complete!!!");
});
```

### routers/

All the files with the routes. Each file contents the routes of an specific functionality.
It is very important that the names of the files inside this folder follow this pattern: name.router.php

Example of router file:

user.router.php

```php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\CustomHandlers as CustomHandler;

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
```

### reSlim Configuration

Example Config.php
```php
/** 
 * Configuration App
 *
 * @var $config['displayErrorDetails'] to display error details on slim
 * @var $config['addContentLengthHeader'] to set the Content-Length header which makes Slim behave more predictably
 * 
 */
$config['displayErrorDetails']      = true;
$config['addContentLengthHeader']   = false;

// Configuration timezone
$config['reslim']['timezone'] = 'Asia/Jakarta';

/** 
 * Configuration PDO MySQL Database
 *
 * @var $config['db']['host'] = where is database was hosted
 * @var $config['db']['user'] = username database to login
 * @var $config['db']['pass'] = pass database to login
 * @var $config['db']['dbname'] = the database name
 */
$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = 'root';
$config['db']['dbname'] = 'reSlim';
```

Working with default example for testing
-----------------
I recommend you to use PostMan an add ons in Google Chrome to get Started with test.

1. Import file reSlim User.postman_collection.json in your PostMan.
2. Edit the path in PostMan. Because the example test is using my path server which is my server is run in http://localhost:1337 

The path to run reSlim is inside folder api.<br> 
Example for my case is: http://localhost:1337/reSlim/src/api/<br><br>

In short, It's depend on your server configuration.

3. Then you can do the test by yourself

The concept authentication in reSlim
-----------------

1. Register account first
2. Then You have to login to get the generated new token

The token is always generated new when You relogin and the token is will expired in 7 days as default.<br>
If You logout or change password or delete user, the token will be clear automatically.

How to Contribute
-----------------
### Pull Requests

1. Fork the reSlim repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the develop branch