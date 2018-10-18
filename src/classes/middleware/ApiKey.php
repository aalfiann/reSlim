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
        private $apikey,$conf,$lang,$dbconfig;

        public function __construct(){
            require '../config.php';
            $this->dbconfig = $config['db'];
            $this->lang = filter_var((empty($_GET['lang'])?'':$_GET['lang']),FILTER_SANITIZE_STRING);
            $this->apikey = filter_var((empty($_GET['apikey'])?'':$_GET['apikey']),FILTER_SANITIZE_STRING);
            $this->conf = $config['enableApiKeys'];
        }

        private function openDB($db){
            $pdo = new SafePDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        }

        private function isValidAPIKey($apikey,$domain=null){
            $r = false;
            if (Auth::isKeyCached('api-'.$apikey)){
                $r = true;
            } else {
                $db = $this->openDB($this->dbconfig);
                $sql = "SELECT a.Domain
			        FROM user_api a 
                    INNER JOIN user_data b ON a.Username = b.Username
        			WHERE a.StatusID = '1' AND b.StatusID = '1' AND a.ApiKey = BINARY :apikey LIMIT 1;";
	        	$stmt = $db->prepare($sql);
		        $stmt->bindParam(':apikey', $apikey, PDO::PARAM_STR);
        		if ($stmt->execute()) {	
                    if ($stmt->rowCount() > 0){
                        if ($domain == null){
                            $r = true;
                            Auth::writeCache('api-'.$apikey);
                        } else {
                            $single = $stmt->fetch();
					        if (strtolower($single['Domain']) == strtolower($domain)){
                                $r = true;
                                Auth::writeCache('api-'.$apikey,$domain);
                            }
                        }       
                    }          	   	
                }
                $db = null;
            }
		    return $r;
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
                    if ($this->isValidAPIKey($this->apikey)){
                        $response = $next($request, $response);    
                        return $response;
                    } else {
                        $body = $response->getBody();
                        $body->write(JSON::encode([
	    	        	    'status' => 'error',
                            'code' => 'RS406',
			        	    'message' => CustomHandlers::getreSlimMessage('RS406',$this->lang)
    				    ],true));
                        return Cors::modify($response,$body,401);
                    }
                } else {
                    if ($request->hasHeader('Authorization')){
                        if ($this->isValidAPIKey($request->getHeaderLine('Authorization'))){
                            $response = $next($request, $response);    
                            return $response;
                        } else {
                            $body = $response->getBody();
                            $body->write(JSON::encode([
                                'status' => 'error',
                                'code' => 'RS406',
                                'message' => CustomHandlers::getreSlimMessage('RS406',$this->lang)
                            ],true));
                            return Cors::modify($response,$body,401);
                        }
                    } else {
                        $body = $response->getBody();
                        $body->write(JSON::encode([
		        	        'status' => 'error',
    			   	    	'code' => 'RS407',
	    		    	    'message' => CustomHandlers::getreSlimMessage('RS407',$this->lang)
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

    Class SafePDO extends PDO {
        public static function exception_handler($exception) {
            //Output the exception details
            header("Content-type: application/json; charset=utf-8");
            $data = [
                'status' => 'error',
                'code' => $exception->getCode(),
                'message' => trim($exception->getMessage())
            ];
            die(json_encode($data));
        }

        public function __construct($dsn, $username='', $password='', $driver_options=array()) {
            // Temporarily change the PHP exception handler while we . . .
            set_exception_handler(array(__CLASS__, 'exception_handler'));
            // . . . create a PDO object
            parent::__construct($dsn, $username, $password, $driver_options);
            // Change the exception handler back to whatever it was before
            restore_exception_handler();
        }
    }