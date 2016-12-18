<?php 

namespace classes;
use \classes\BaseConverter as BaseConverter;

    Class Auth {

        // The Default Secret Key is 1L0V3R3SL1M
    	public static $secret_key = "1L0V3R3SL1M";
        public static $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        public static function HashPassword($username,$password)
        {
        	$secret1 = self::$secret_key;
        	$password = md5($secret1.md5($password));
        	$hash = base64_encode($username.$password);
        	return $hash;
        }

        public static function EncodeAPIKey($data){            
            return BaseConverter::convertFromBinary($data, self::$characters);
        }

        public static function DecodeAPIKey($encoded){
            return BaseConverter::convertToBinary($data, self::$characters);
        }

    }