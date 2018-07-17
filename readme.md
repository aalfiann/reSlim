reSlim
=======
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](https://github.com/aalfiann/reSlim)
[![reSlim](https://img.shields.io/badge/stable-1.11.1-brightgreen.svg)](https://github.com/aalfiann/reSlim)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/aalfiann/reSlim/blob/master/license.md)

reSlim is Lightweight, Fast, Secure, Simple, Scalable and Powerful rest api.<br>
reSlim is based on [Slim Framework version 3](http://www.slimframework.com/).<br>

Features:
---------------
Reslim is already build with essentials of user management system in rest api way.

1. User register, login and logout
2. Auto generated token every login
3. User can revoke all active token
4. User can manage their API Keys
5. Included with default role for superuser, admin and member
6. Auto clear current token when logout,user deleted or password was changed
7. Change, reset, forgot password concept is very secure
8. Mailer for sending email or contact form
9. File management system
10. Pages management system
11. Pagination json response
12. Support Multi Language
13. Server Side Caching
14. Scalable architecture with modular concept
15. Load Balancer with multiple database server (master to master or master to slave)
16. Etc

Extensions:
---------------
- **Modules**  
Here is the available modules created by reSlim author >> [reSlim-modules](https://github.com/aalfiann/reSlim-modules).

- **UI Boilerplate**  
Here is the basic UI template for production use with reSlim >> [reSlim-ui-boilerplate](https://github.com/aalfiann/reSlim-ui-boilerplate).

System Requirements
---------------

1. Web server with URL rewriting
2. Web server with mcrypt extension
3. PHP 5.5 or newer


Getting Started
---------------
1. Get or download the project
2. Install it using Composer

Folder System
---------------
* database
    * event_delete_all_expired_auth_scheduler.sql (An expired token will auto deletion in 7 Days after expired date)
    * reSlim.sql (Structure database in reSlim to work with default example)
* src/
    * api/
    * app/
    * classes/
        * middleware/
            * ApiKey.php (For handling authentication api key)
            * index.php (Default forbidden page)
            * ValidateParam.php (For handling validation in body form request)
            * ValidateParamJSON.php (For handling validation in JSON request)
            * ValidateParamURL.php (For handling validation in query parameter url)
        * Auth.php (For handling authentication)
        * BaseConverter.php (For encryption)
        * Cors.php (For accessing web resources)
        * CustomHandlers.php (For handle message)
        * index.php (Default forbidden page)
        * JSON.php (Default handler JSON)
        * Logs.php (For handle Log Server)
        * Mailer.php (For sending mail)
        * Pagination.php (For pagination json response)
        * SimpleCache.php (For handle cache server side)
        * Upload.php (For user upload and management file)
        * User.php (For user management)
        * Validation.php (For validation)
    * logs/
        * app.log (Log data will stored in here)
        * index.php (Default forbidden page)
    * modules
        * backup/ (Default module backup for database)
        * flexibleconfig/ (Default module flexibleconfig for extend the app config)
        * packager/ (Default module packager for app management)
        * pages/ (Default module package for pages management)
        * index.php (Default forbidden page)
    * routers/
	    * name.router.php (routes by functionalities)
* test/
    * example/ (This is a GUI for test)
    * reSlim User.postman_collection.json (Is the file to run example test in PostMan)

### api/
    
Here is the place to run your application

### app/

Here is the place for slim framework<br>
We are using PDO MySQL for the Database.

### classes/

reSlim core classes are here.

### classes/middleware

reSlim middleware classes are here.

### logs/

Your custom log will be place in here as default.<br>
You can add your custom log in your any container or router.

Example adding custom log in a router
```php
$app->post('/custom/log/new', function (Request $request, Response $response) {
    $this->logger->addInfo(
        '{"message":"Response post is succesfully complete!!!"}',
        [
            'type'=>'customlog',
            'created_by'=>'yourname',
            'IP'=>$this->visitorip
        ]
    );
});
```

### modules/{your-module}

You have to create new folder for each different module project.

**How to create new reSlim modules?**  
Please look at this very simple project on [Github.com](https://github.com/aalfiann/reSlim-modules-first_mod).  

### routers/

All the files with the routes. Each file contents the routes of an specific functionality.<br>
It is very important that the names of the files inside this folder follow this pattern: name.router.php

Example of router file:

user.router.php

```php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \classes\SimpleCache as SimpleCache;
use \classes\User as User;
use \classes\Cors as Cors;
use \classes\middleware\ApiKey as ApiKey;

    // POST example api to show all data user
    $app->post('/user', function (Request $request, Response $response) {
        $users = new User($this->db);
        $datapost = $request->getParsedBody();
        $users->Token = $datapost['Token'];
        $body = $response->getBody();
        $body->write($users->showAll());
        return Cors::modify($response,$body,200);
    });

    // GET example api to show profile user (for public is need an api key)
    $app->get('/user/profile/{username}/', function (Request $request, Response $response) {
        $users = new User($this->db);
        $users->Username = $request->getAttribute('username');
        $body = $response->getBody();
        
        // Use Http Cache Control and ETag
        $response = $this->cache->withEtag($response, $this->etag30min.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        
        // Working with server side cache.
        if (SimpleCache::isCached(600,["apikey"])){
            $datajson = SimpleCache::load(["apikey"]);
        } else {
            $datajson = SimpleCache::save($users->showUserPublic(),["apikey"]);
        }
        $body->write($datajson);
        return Cors::modify($response,$body,200,$request);
    })->add(new ApiKey());
```

### reSlim Configuration

Example Config.php
```php
/** 
 * Configuration App
 *
 * @var $config['displayErrorDetails'] to display error details on slim
 * @var $config['addContentLengthHeader'] should be set to false. This will allows the web server to set the Content-Length header which makes Slim behave more predictably
 * @var $config['limitLoadData'] to protect high request data load. Default is 1000.
 * @var $config['enableApiKeys'] to protect api from guest or anonymous. Guest which don't have api key can not using this service. Default is true.
 * @var $config['httpVersion'] The protocol version used by the Response object. Default is '1.1'. 
 * @var $config['responseChunkSize'] Size of each chunk read from the Response body when sending to the browser. Default is 4096
 * @var $config['outputBuffering'] If false, then no output buffering is enabled. If 'append' or 'prepend', then any echo or print statements are captured and are either appended or prepended to the Response returned from the route callable. Default is 'append'
 * @var $config['determineRouteBeforeAppMiddleware'] When true, the route is calculated before any middleware is executed. This means that you can inspect route parameters in middleware if you need to. Default is false.
 * 
 */
$config['displayErrorDetails']                  = true;
$config['addContentLengthHeader']               = false;
$config['limitLoadData']                        = 1000;
$config['enableApiKeys']                        = true;
$config['httpVersion']                          = '1.1';
$config['responseChunkSize']                    = 4096;
$config['outputBuffering']                      = 'append';
$config['determineRouteBeforeAppMiddleware']    = false;

/**
 * Configuration Router Cache
 * 
 * @var $config['router']['enableCache'] If set to true, this will make your router performance faster. If you in development mode, just set to false. The exist file cache will automatically deleted from server.
 * @var $config['router']['folderCache'] To set the folder of router cache. Don't leave this blank.  
 * @var $config['router']['fileCache'] To set the filename of router cache. Don't leave this blank.
 * 
 */
$config['router']['enableCache']    = false;
$config['router']['folderCache']    = 'cache-router';
$config['router']['fileCache']      = 'routes.cache.php';

/** 
 * Configuration PDO MySQL Database MASTER
 *
 * @var $config['db']['host'] = where is database was hosted
 * @var $config['db']['user'] = username database to login
 * @var $config['db']['pass'] = pass database to login
 * @var $config['db']['dbname'] = the database name
 * 
 * 
 * Example to set up multiple database (master to many master)
 * Because this multiple database configuration uses arrays, so make sure the sequence is the same
 * 
 * $config['db']['host']   = ['localhost','another1','another2'];
 * $config['db']['user']   = ['root','root1','root2'];
 * $config['db']['pass']   = ['secret','secret1','secret2'];
 * $config['db']['dbname'] = ['dbname','dbname1','dbname2'];
 * 
 * Note: 
 * - Multiple database will work as load balancer in this reSlim framework
 */
$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = 'root';
$config['db']['dbname'] = 'reSlim';

/** 
 * Configuration PDO MySQL Database SLAVE
 *
 * @var $config['dbslave']['host'] = where is database was hosted
 * @var $config['dbslave']['user'] = username database to login
 * @var $config['dbslave']['pass'] = pass database to login
 * @var $config['dbslave']['dbname'] = the database name
 * 
 * 
 * Example to set up multiple slave database
 * Because this multiple database configuration uses arrays, so make sure the sequence is the same
 * 
 * $config['dbslave']['host']   = ['localhost','another1','another2'];
 * $config['dbslave']['user']   = ['root','root1','root2'];
 * $config['dbslave']['pass']   = ['secret','secret1','secret2'];
 * $config['dbslave']['dbname'] = ['dbname','dbname1','dbname2'];
 * 
 * Note: 
 * - Multiple database will work as load balancer in this reSlim framework
 * - reSlim is not create slave architecture as default, so you have to modify it by yourself 
 */
$config['dbslave']['host']   = '';
$config['dbslave']['user']   = '';
$config['dbslave']['pass']   = '';
$config['dbslave']['dbname'] = '';

/**
 * Configuration SMTP for Mailer
 *
 * @var $config['smtp']['host'] is smtp host. example smtp.gmail.com
 * @var $config['smtp']['autotls'] is make smtp will send using tls protocol as default
 * @var $config['smtp']['auth'] will connect to smtp using authentication
 * @var $config['smtp']['secure'] this is type of smtp security. You can use tls or ssl
 * @var $config['smtp']['port'] this is port smtp
 * @var $config['smtp']['defaultnamefrom'] this is default name from. You can filled with yourname / yourwebsitetitle
 * @var $config['smtp']['username'] your username to login into smtp server
 * @var $config['smtp']['password'] the password to login into smtp server
 * @var $config['smtp']['debug'] get more information by set debug.
 *                               To work using rest api, You should set debug 1,
 *                               because other than 1, there is special characters that will broke json format. 
 */
$config['smtp']['host']             = 'smtp.gmail.com';
$config['smtp']['autotls']          = false;
$config['smtp']['auth']             = true;
$config['smtp']['secure']           = 'tls';
$config['smtp']['port']             = 587;
$config['smtp']['defaultnamefrom']  = 'reSlim admin';
$config['smtp']['username']         = 'youremail@gmail.com';
$config['smtp']['password']         = 'secret';
$config['smtp']['debug']            = 1;

// Configuration timezone
$config['reslim']['timezone'] = 'Asia/Jakarta';
```

Working with default example for testing
-----------------
I recommend you to use PostMan an add ons in Google Chrome to get Started with test.

1. Import reSlim.sql in your database then config your database connection in config.php inside folder "reSlim/src/"
2. Import file reSlim User.postman_collection.json in your PostMan.
3. Edit the path in PostMan. Because the example test is using my path server which is my server is run in http://localhost:1337 
    The path to run reSlim is inside folder api.<br> 
    Example for my case is: http://localhost:1337/reSlim/src/api/<br><br>
    In short, It's depend on your server configuration.
4. Then you can do the test by yourself

Working with gui example for testing
-----------------

1. Import reSlim.sql in your database then config your database connection in config.php inside folder "reSlim/src/"
2. Edit the config.php inside folder "reSlim/test/example"<br>
    $config['title'] = 'your title website';<br>
    $config['email'] = 'your default email address';<br>
    $config['basepath'] = 'url location of base path example';<br>
    $config['api'] = 'url location of base path of api';<br>
    $config['apikey'] = 'your api key, you can leave this blank and fill this later';
3. Visit yourserver/reSlim/test/example<br>
    For my case is http://localhost:1337/reSlim/test/example
4. You can login with default superuser account:<br>
    Username : reslim<br>
    Password : reslim
5. All is done

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