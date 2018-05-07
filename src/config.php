<?php 
/** 
 * Configuration App
 *
 * @var $config['displayErrorDetails'] to display error details on slim
 * @var $config['addContentLengthHeader'] should be set to false. This will allows the web server to set the Content-Length header which makes Slim behave more predictably
 * @var $config['limitLoadData'] to protect high request data load. Default is 1000.
 * @var $config['enableApiKeys'] to protect api from guest or anonymous. Guest which don't have api key can not using this service. Default is true.
 * @var $config['language'] is language that we use for delivery message information. Default is en means english.
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
$config['language']                             = 'en';
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