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