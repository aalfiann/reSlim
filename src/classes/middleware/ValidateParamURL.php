<?php 
/**
 * This class is a part of middleware reSlim project to make validation for url parameter using Regular Expression
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes\middleware;
use \classes\CustomHandlers as CustomHandlers;
use \classes\Cors as Cors;
use \classes\JSON as JSON;
    /**
     * A class for validation the url parameter using Regular Expression
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class ValidateParamURL
    {
        private $parameter,$between,$regex,$message,$length,$error,$lang,$min=0,$max=0;

        /**
         * Constructor
         * 
         * @var parameter is the url parameter of the request. Example http://xxx.com/?example=abcdef&count=123456 => "example" and "count" is the url parameter after the "?" letter.
         * @var between is the min and max chars length of the parameter. Default is empty means unlimited chars and allow empty chars.
         * @var regex is to validate the value of the parameter. See the validateRegex function for the shortcut regex. Default is empty.
         */
        public function __construct($parameter,$between='',$regex=''){
            $this->parameter = $parameter;
            $this->regex = $regex;
            $this->between = $between;
            $this->lang = filter_var((empty($_GET['lang'])?'':$_GET['lang']),FILTER_SANITIZE_STRING);
        }

        /**
         * Validation middleware invokable class
         * 
         * @param \Psr\Http\Message\ServerRequestInterface  $request    PSR7 request
         * @param \Psr\Http\Message\ResponseInterface       $response   PSR7 response
         * @param callable                                  $next       Next middleware
         * 
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function __invoke($request, $response, $next){
            if($this->validate($request,$this->parameter,$this->between,$this->regex)){
                $response = $next($request, $response);    
                return $response;
            } else {
                $body = $response->getBody();
                $datajson = "";
                if (empty($this->message)){
                    if (empty($this->error)){
                        $datajson = ['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801',$this->lang)];
                    } else {
                        $datajson = ['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801',$this->lang),'description' => $this->error];
                    }
                } else {
                    if (empty($this->length)){
                        $datajson = ['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801',$this->lang),'parameter' => $this->message];
                    } else {
                        $datajson = ['status' => 'error','code' => 'RS801','message' => CustomHandlers::getreSlimMessage('RS801',$this->lang),'parameter' => $this->message,'length' => $this->length];
                    }
                }
                $body->write(JSON::encode($datajson,true));
                return Cors::modify($response,$body,400);
            }
        }

        private function validateRegex($regex,$key,$value){
            switch($regex){
                case 'required':
                    $msg = CustomHandlers::getreSlimMessage('MD001',$this->lang);
                    return $this->blankTest($key,$value,$msg);
                case 'domain':
                    $regex = '/([a-zA-Z0-9]+\.)*[a-zA-Z0-9]+\.[a-zA-Z]{2,}/';
                    $msg = CustomHandlers::getreSlimMessage('MD002',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'url':
                    $msg = CustomHandlers::getreSlimMessage('MD003',$this->lang);
                    return $this->urlTest($key,$value,$msg);
                case 'date':
                    $regex = '/([123456789]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/';
                    $msg = CustomHandlers::getreSlimMessage('MD004',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'timestamp':
                    $regex = '/^\d\d\d\d-(0?[1-9]|1[0-2])-(0?[1-9]|[12][0-9]|3[01]) (00|[0-9]|1[0-9]|2[0-3]):([0-9]|[0-5][0-9]):([0-9]|[0-5][0-9])$/';
                    $msg = CustomHandlers::getreSlimMessage('MD005',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'alphanumeric':
                    $regex = '/^[a-zA-Z0-9]+$/';
                    $msg = CustomHandlers::getreSlimMessage('MD006',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'alphabet':
                    $regex = '/^[a-zA-Z]+$/';
                    $msg = CustomHandlers::getreSlimMessage('MD007',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'decimal':
                    $regex = '/^[+-]?[0-9]+(?:\.[0-9]+)?$/';
                    $msg = CustomHandlers::getreSlimMessage('MD008',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'notzero':
                    $regex = '/^[1-9][0-9]*$/';
                    $msg = CustomHandlers::getreSlimMessage('MD009',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'numeric':
                    $regex = '/^[0-9]+$/';
                    $msg = CustomHandlers::getreSlimMessage('MD010',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'double':
                    $regex = '/^[+-]?[0-9]+(?:,[0-9]+)*(?:\.[0-9]+)?$/';
                    $msg = CustomHandlers::getreSlimMessage('MD011',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'username':
                    $regex = '/^[a-zA-Z0-9]{3,20}$/';
                    $msg = CustomHandlers::getreSlimMessage('MD012',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'email':
                    $regex = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
                    $msg = CustomHandlers::getreSlimMessage('MD013',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
                case 'json':
                    $msg = CustomHandlers::getreSlimMessage('MD014',$this->lang);
                    return $this->jsonTest($key,$value,$msg);
                default:
                    $regex = $regex;
                    $msg = CustomHandlers::getreSlimMessage('MD015',$this->lang);
                    return $this->regexTest($regex,$key,$value,$msg);
            }
        }

        private function regexTest($regex,$key,$value,$msg){
            if(!preg_match($regex, $value)){
                $this->message[$key] = $msg;
                return false;
            }
            return true;
        }

        private function blankTest($key,$value,$msg){
            if (empty($value) || ctype_space($value)){
                $this->message[$key] = $msg;
                return false;
            }
            return true;
        }

        private function jsonTest($key,$value,$msg){
            if (JSON::isValid($value) == false){
                $this->message[$key] = $msg;
                return false;
            }
            return true;
        }

        private function urlTest($key,$value,$msg){
            if (!filter_var($value, FILTER_VALIDATE_URL) === false) return true;
            $this->message[$key] = $msg;
            return false;
        }

        private function validateBetween($key,$value,$between=''){
            $between = str_replace(' ','',$between);
            if(!empty($between)){
                if(strpos($between,'-') !== false){
                    if(substr_count($between, '-') == 1){
                        $data = explode('-',$between);
                        if (!empty($data[0]) || $data[0] == 0){
                            $this->min = $data[0];
                            $this->max = $data[1];
                            $total = strlen($value);
                            if ($total >= $this->min && $total <= $this->max){
                                return true;
                            } else {
                                $this->message[$key] = CustomHandlers::getreSlimMessage('MD101',$this->lang).' '.$this->min.' - '.$this->max;
                                $this->length[$key] = $total;
                                return false;
                            }
                        } else {
                            $this->message[$key] = CustomHandlers::getreSlimMessage('MD102',$this->lang);
                            return false;
                        }
                    } else {
                        $this->message[$key] = CustomHandlers::getreSlimMessage('MD103',$this->lang);
                        return false;
                    }
                } else {
                    $this->message[$key] = CustomHandlers::getreSlimMessage('MD103',$this->lang);
                    return false;
                }
            }
            return true;
        }

        private function valueTest($parameter,$data,$between='',$regex=''){
            $count = 0;
            if (is_array($parameter)){
                $aa = 0;
                foreach ($parameter as $singleparam){
                    $tt = 0;
                    foreach ($data as $key => $value) {
                        if ($key==$singleparam){
                            if ($this->validateBetween($key,$value,$between)){
                                if (!empty($regex)){
                                    if ($this->min > 0 || strlen($value) > 0 || $regex == 'required'){
                                        if($this->validateRegex($regex,$key,$value)){
                                            $tt += 1;    
                                        }
                                    } else {
                                        $tt += 1;
                                    }
                                } else {
                                    $tt += 1;
                                }
                            }
                        }
                    }
                    if($tt == 0) $aa += 1;
                }
                if($aa > 0) {
                    $count += 0;
                } else {
                    $count += 1;
                }
            } else if(is_string($parameter)) {
                $tt=0;
                foreach ($data as $key => $value) {
                    if ($key==$parameter){
                        if ($this->validateBetween($key,$value,$between)){
                            if (!empty($regex)){
                                if ($this->min > 0 || strlen($value) > 0 || $regex == 'required'){
                                    if($this->validateRegex($regex,$key,$value)){
                                        $tt += 1;    
                                    }
                                } else {
                                    $tt += 1;
                                }
                            } else {
                                $tt += 1;
                            }
                        }
                    }
                }
                if($tt > 0){
                    $count += 1;
                } else {
                    $count += 0;
                }
            }
            return $count;
        }

        private function validate($request,$parameter,$between='',$regex=''){
            if ($this->valueTest($parameter,$_GET,$between,$regex)>0) return true;
            $this->error = ['info'=>CustomHandlers::getreSlimMessage('RS805',$this->lang),'required'=>$parameter];
            return false;
        }
    }