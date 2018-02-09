<?php 
/**
 * This class is a part of middleware reSlim project for handle cors in api request
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
 namespace classes;
 use classes\Cors as Cors;

    class Cors{

        public static function modify($response,$body,$status){
            return $response
            ->withStatus($status)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, ETag')
            ->withBody($body);
        }

    }