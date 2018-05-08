<?php 
/**
 * This class is a part of middleware reSlim project for authentication registered api key
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes\middleware;
use \classes\Auth as Auth;
use \classes\JSON as JSON;
use \classes\CustomHandlers as CustomHandlers;
use \classes\Cors as Cors;
use PDO;
    /**
     * A class for secure authentication registered api key
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class ApiKey
    {
        private $apikey,$pdo,$conf;

        public function __construct(){
            require '../config.php';
            $db = $config['db'];
            $this->apikey = filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING);
            $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo = $pdo;
            $this->conf = $config['enableApiKeys'];
        }

        /**
         * ApiKey middleware invokable class
         * 
         * @param \Psr\Http\Message\ServerRequestInterface  $request    PSR7 request
         * @param \Psr\Http\Message\ResponseInterface       $response   PSR7 response
         * @param callable                                  $next       Next middleware
         * 
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function __invoke($request, $response, $next){
            if ($this->conf == true){
                if (!empty($this->apikey)){
                    if (Auth::validAPIKey($this->pdo,$this->apikey)){
                        $response = $next($request, $response);    
                        return $response;
                    } else {
                        $body = $response->getBody();
                        $body->write(JSON::encode([
	    	        	    'status' => 'error',
                            'code' => 'RS406',
			        	    'message' => CustomHandlers::getreSlimMessage('RS406')
    				    ],true));
                        return Cors::modify($response,$body,401);
                    }
                } else {
                    if ($request->hasHeader('Authorization')){
                        if (Auth::validAPIKey($this->pdo,$request->getHeaderLine('Authorization'))){
                            $response = $next($request, $response);    
                            return $response;
                        } else {
                            $body = $response->getBody();
                            $body->write(JSON::encode([
                                'status' => 'error',
                                'code' => 'RS406',
                                'message' => CustomHandlers::getreSlimMessage('RS406')
                            ],true));
                            return Cors::modify($response,$body,401);
                        }
                    } else {
                        $body = $response->getBody();
                        $body->write(JSON::encode([
		        	        'status' => 'error',
    			   	    	'code' => 'RS407',
	    		    	    'message' => CustomHandlers::getreSlimMessage('RS407')
    	    			],true));
                        return Cors::modify($response,$body,400);
                    }
                }
            } else {
                $response = $next($request, $response);    
                return $response;
            }
        }
    }