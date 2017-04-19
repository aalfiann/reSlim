<?php 

/** 
 * Configuration App
 *
 * @var $config['displayErrorDetails'] to display error details on slim
 * @var $config['addContentLengthHeader'] to set the Content-Length header which makes Slim behave more predictably
 * @var $config['limitLoadData'] to protect high request data load. Default is 1000.
 * 
 */
$config['displayErrorDetails']      = true;
$config['addContentLengthHeader']   = false;
$config['limitLoadData'] = 1000;

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
 * @var $config['smtp']['username'] your username to login into smtp server
 * @var $config['smtp']['password'] the password to login into smtp server
 * @var $config['smtp']['debug'] get more information by set debug
 */
$config['smtp']['host'] = 'smtp.gmail.com';
$config['smtp']['autotls'] = false;
$config['smtp']['auth'] = true;
$config['smtp']['secure'] = 'tls';
$config['smtp']['port'] = 587;
$config['smtp']['username'] = 'youremail@gmail.com';
$config['smtp']['password'] = 'secret';
$config['smtp']['debug'] = 1;

// Configuration timezone
$config['reslim']['timezone'] = 'Asia/Jakarta';