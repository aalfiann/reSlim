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
 *                               To work using rest api, You should set debug 0,
 *                               because other than 0, there is special characters that will broke json format. 
 */
$config['smtp']['host']             = 'smtp.gmail.com';
$config['smtp']['autotls']          = false;
$config['smtp']['auth']             = true;
$config['smtp']['secure']           = 'tls';
$config['smtp']['port']             = 587;
$config['smtp']['defaultnamefrom']  = 'reSlim admin';
$config['smtp']['username']         = 'youremail@gmail.com';
$config['smtp']['password']         = 'secret';
$config['smtp']['debug']            = 0;

/**
 * Configuration built-in cache
 * 
 * @var $config['reslim']['authcache'] is used to reduce the api key validation for every request. Not really matter for multi server purpose. Default is true.
 * @var $config['reslim']['simplecache'] is used to cache the output json from reslim api. This is recommended to set false if you run the reslim api only for multiserver purpose. Default is true.
 * @var $config['reslim']['universalcache'] is used to cache for internal process. This is recommended to set false if you run the reslim api only for multiserver purpose. Default is true.
 */
$config['reslim']['authcache']      = true;
$config['reslim']['simplecache']    = true;
$config['reslim']['universalcache'] = true;

/**
 * Configuration redis server
 * 
 * @var $config['redis']['enable'] = If set to true then filebased cache will change to use memory RAM. Default is false.
 * @var $config['redis']['parameter'] = The parameter to create redis connection. Default is using standar redis connection.
 * @var $config['redis']['option'] = Option of redis connection.
 * 
 * Note: We use predis library, for more details about parameter an option, you can read at here >> https://github.com/nrk/predis
 */
$config['redis']['enable'] = false;
$config['redis']['parameter'] = ['tcp://127.0.0.1:6379'];
$config['redis']['option'] = [];

// Configuration timezone
$config['reslim']['timezone']       = 'Asia/Jakarta';