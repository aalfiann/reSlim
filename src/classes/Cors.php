<?php 
/**
 * This class is a part of middleware reSlim project for handle cors in api request
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
 namespace classes;
 use classes\Auth as Auth;
    /**
     * A class for modify the cors based on the API Key
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class Cors{
        /** Default Cors Origin */
        private static $defaultOrigin = "*";    //Wildcard is unsafe. So please change this with your main web host (not rest api host).

        /**
         * Determine your host scheme using http or https (works for cloudflare) 
         * @return boolean true means your scheme is https
         */
        private static function isHttpsButtflare() {
            $whitelist = array(
                '127.0.0.1',
                '::1'
            );
            
            if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
                if (!empty($_SERVER['HTTP_CF_VISITOR'])){
                    return isset($_SERVER['HTTPS']) ||
                    ($visitor = json_decode($_SERVER['HTTP_CF_VISITOR'])) &&
                    $visitor->scheme == 'https';
                } else {
                    return isset($_SERVER['HTTPS']);
                }
            } else {
                return 0;
            }            
        }

        /**
         * Check origin from generated API Key
         * @param apikey = your apikey
         */
        private static function checkOrigin($apikey=null){
            if (!empty($apikey)){
                $domain = Auth::getDomainAPIKey(filter_var($apikey,FILTER_SANITIZE_STRING));
                if (!empty($domain)) return $domain;
            }
            return (self::isHttpsButtflare() ? "https" : "http").'://'.$_SERVER['HTTP_HOST'];
        }

        /**
         * Modify cors
         * @param response is the response from PSR7 interface
         * @param body is the value to write the body response
         * @param status is the http code for the response. This should be 200.
         * @param request is the request from PSR7 interface. If null then ACAO will allow your defaultorigin. Default value is null.
         */
        public static function modify($response,$body,$status,$request=null){
            if (!empty($request)){
                if (!empty($request->getHeaderLine('Authorization'))){
                    return $response
                        ->withStatus($status)
                        ->withHeader('Content-Type','application/json; charset=utf-8')
                        ->withHeader('Access-Control-Allow-Origin', self::checkOrigin($request->getHeaderLine('Authorization')))
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, ETag')
                        ->withHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS')
                        ->withHeader('Access-Control-Expose-Headers','ETag')
                        ->withBody($body);
                } else {
                    return $response
                        ->withStatus($status)
                        ->withHeader('Content-Type','application/json; charset=utf-8')
                        ->withHeader('Access-Control-Allow-Origin', self::checkOrigin($_GET['apikey']))
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, ETag')
                        ->withHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS')
                        ->withHeader('Access-Control-Expose-Headers','ETag')
                        ->withBody($body);
                }
            }
            return $response
                ->withStatus($status)
                ->withHeader('Content-Type','application/json; charset=utf-8')
                ->withHeader('Access-Control-Allow-Origin', self::$defaultOrigin)
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, ETag')
                ->withHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Expose-Headers','ETag')
                ->withBody($body);
        }

    }