reSlim
=======
[![Travis branch](https://img.shields.io/travis/rust-lang/rust/master.svg)](https://github.com/aalfiann/reSlim)
[![Version](https://img.shields.io/badge/version-1.0.0-brightgreen.svg)](https://github.com/aalfiann/reSlim)
[![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg)](https://github.com/aalfiann/reSlim/blob/master/license.md)

reSlim is Lightweight, Fast, Secure and Powerful rest api.<br>
reSlim is based on [Slim Framework version 3.6](http://www.slimframework.com/).<br>


Getting Started
---------------
1. Get or download the project
2. Install it using Composer

Folder System
---------------
* database
    * reSlim.sql (example dummy database)
* src/
    * api/
    * classes/
    * logs/
    * routers/
	    * name.router.php (routes by functionalities)

### api/

Here is the place for slim framework

### classes/

Add the classes here.
We are using PDO MySQL for the Database.

Example of class:

Starter.php

```php

namespace classes;
use PDO;

class Starter {

	protected $db;

	function __construct($db=null) {
		if (!empty($db)) 
        {
            $this->db = $db;
        }
	}
	
	// Get all data from database mysql
	public function getAll() {
		$r = array();		

		$sql = "SELECT * FROM user a order by a.created;";
		$stmt = $this->db->prepare($sql);		

		if ($stmt->execute()) {	
            if ($stmt->rowCount() > 0){
                $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else{
                $r = 0;
            }          	   	
		} else {
			$r = 0;
		}		
        
		return $r;
        $stmt->Close();
	}

}
```

### logs/

Here is the place your custom log.
You can add your custom log in your any container or router.

Example adding custom log in a router post
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

    // GET example api user route directly from database
    $app->get('/user', function (Request $request, Response $response) {
        $users = new classes\User($this->db);
        $results = $users->getAll();
        $body = $response->getBody();
        if ($results != 0){
            $body->write(json_encode(array("result" => $results, "status" => "success", "code" => $response->getStatusCode()), JSON_PRETTY_PRINT));
        } else {
            $body->write(json_encode(array("result" => 'no records found!', "status" => "success", "code" => $response->getStatusCode()), JSON_PRETTY_PRINT));
        }
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

How to Contribute
-----------------
### Pull Requests

1. Fork the reSlim repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the develop branch