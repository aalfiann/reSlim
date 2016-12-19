<?php 

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

$config['reslim']['timezone'] = 'Asia/Jakarta';

// Autoload all classes
spl_autoload_register(function ($classname) {
    require ( $classname . ".php");
});