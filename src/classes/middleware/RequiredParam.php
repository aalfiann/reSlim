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
use \classes\middleware\ApiKey as ApiKey;
use \classes\CustomHandlers as CustomHandlers;
use \classes\Cors as Cors;
use PDO;
    /**
     * A class for validate the required parameter
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class RequiredParam
    {
        private $parameter;

        public function __construct($parameter){
            $this->parameter = $parameter;
        }

        public function __invoke($request, $response, $next){
            if($this->validate($request,$this->parameter)){
                $response = $next($request, $response);    
                return $response;
            } else {
                $body = $response->getBody();
                $body->write(json_encode([
                    'status' => 'error',
                    'code' => 'RS801',
                    'message' => CustomHandlers::getreSlimMessage('RS801')
                ]));
                return Cors::modify($response,$body,400);
            }
        }

        private function validate($request,$parameter){
            $parsedBody = $request->getParsedBody();
            if (empty($parsedBody)) return true;
            if (is_array($parameter)){
                foreach ($parameter as $singleparam){
                    $tt = 0;
                    foreach ($parsedBody as $key => $value) {
                        if ($key==$singleparam){
                            $tt += 1;
                        }    
                    }
                    if($tt == 0) return false;
                }
            } else {
                $tt=0;
                foreach ($parsedBody as $key => $value) {
                    if ($key==$parameter){
                        $tt +=1;
                    }
                }
                if($tt == 0) return false;
            }
            return true;
        }
    }