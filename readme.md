iSlim3-basic
=======
[![Travis branch](https://img.shields.io/travis/rust-lang/rust/master.svg)](https://github.com/aalfiann/iSlim3-basic)
[![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg)](https://github.com/aalfiann/iSlim3-basic/blob/master/license.md)

I call this "iSlim" because I heart [Slim Framework](http://www.slimframework.com/).<br>
iSlim version 3 is the easiest and flexible way to create your PHP application using PSR 7 way,<br>
which is look alike MVC pattern and Bootstrap 4.


Getting Started
---------------
1. Get or download the project
2. Install it using Composer

Folder System
---------------
* classes/
* logs/
* models/
* public/
* routers/
	* name.router.php (routes by functionalities)
* templates/

### classes/

Here is the place for your application classes

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

### models/

Add the model classes here.
We are using PDO MySQL for the Database.

Example of models class:

Starter.php

```php

namespace models;

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

    public function setHello() {
        return array(
			'hello' => "Hello World!!!",
			'description1' => "Use this document as a way to quickly start any new project.",
			'description2' => "All you get is this text and a mostly barebones HTML document.",
			'author' => "iSlim3 is forged by M ABD AZIZ ALFIAN"
			);
    }
}
```

### public/

All the public files:
* Images, CSS and JS files which is inside a theme folder.
* index.php (this is required to run the core of Slim Framework)

### routers/

All the files with the routes. Each file contents the routes of an specific functionality.
It is very important that the names of the files inside this folder follow this pattern: name.router.php

Example of router file:

index.router.php

```php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

    //Hello Word
    $app->get('/', function (Request $request, Response $response) {
        $oStuff = new models\Starter();
        $hello = $oStuff->setHello();

        $response = $this->viewfrontend->render($response, "index.html", [
            "hello" => $hello['hello'],
            "description1" => $hello['description1'],
            "description2" => $hello['description2'],
            "author" => $hello['author'],
            "router" => $this->router]);
        return $response;
    })->setName("/");

//POST route
$app->post('/user/new', function (Request $request, Response $response) {
    echo 'This is a POST route';
});

// PUT route
$app->put('/user/update', function (Request $request, Response $response) {
    echo 'This is a PUT route';
});

// DELETE route
$app->delete('/user/delete', function (Request $request, Response $response) {
    echo 'This is a DELETE route';
});
```

### templates/

There are already 2 default themes in iSlim3:
* default (using twig way)
* defaultPHP (using render PHP)

iSlim3 have two type of templates:

1. Render as PHP
2. Use Twig

Default templates type is using twig, You can find change this configuration in config.php that placed in root folder.

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
 * Configuration Templates
 *
 * @var $config['templateRender'] is how slim3 to render a template. There are two options 'twig' or 'php'
 * @var $config['twigcache'] is cache options in twig only (won't work if you use it on php render)
 * @var $config['theme'] is options to choose which one theme will be use.
 *
 * Note: if You choose theme defaultPHP, make sure you have set templateRender to 'php'
 */
$config['templateRender']           = 'twig';
$config['twigcache']                = false;
$config['theme']                    = 'default';

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
$config['db']['dbname'] = 'iSlim';
```

How to Contribute
-----------------
### Pull Requests

1. Fork the iSlim3 repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the develop branch