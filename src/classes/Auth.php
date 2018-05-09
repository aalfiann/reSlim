<?php 
/**
 * This class is a part of reSlim project for authentication generated token
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use PDO;
use \classes\BaseConverter as BaseConverter;
    /**
     * A class for secure authentication user in rest api way
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    Class Auth {

        // $characters is variable char to use in encryption. Default is base62 (char and number only)
        public static $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        /** 
         * HashPassword is to secure your login and password
         *
         * @param $username : input username
         * @param $password : input password
         * @return string Hashed Password
         */
        public static function hashPassword($username,$password)
        {
        	$options = [
                'cost' => 11,
                'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
            ];
            return password_hash($username.$password, PASSWORD_BCRYPT, $options);
        }

        /** 
         * Verify Password is to verify your login and password is match or not
         *
         * @param $username : input username
         * @param $password : input password
         * @param $hash : your password hash saved in database
         * @return boolean true / false
         */
        public static function verifyPassword($username,$password,$hash)
        {
            $result = false;
        	if (password_verify($username.$password, $hash)) {
              $result = true;  
            }
            return $result;
        }

        /** 
         * Encode to generate API Key
         *
         * @param $data : source to encode
         * @return string base62
         */
        public static function encodeAPIKey($data){            
            return BaseConverter::convertFromBinary($data, self::$characters);
        }

        /** 
         * Decode the API Key
         *
         * @param $encoded : encoded data
         * @return string the decoded data
         */
        public static function decodeAPIKey($encoded){
            return BaseConverter::convertToBinary($encoded, self::$characters);
        }

        /** 
         * Get the domain of API Key
         *
         * @param $encoded : encoded data
         * @return string domain of API Key
         */
        public static function getDomainAPIKey($encoded){
            if(!empty($encoded)){
                $host = explode('::',BaseConverter::convertToBinary($encoded, self::$characters));
                if (!empty($host[0])){
                    return $host[0];
                }
            }
            return "";
        }

        /** 
         * Convert any char into Numeric
         *
         * @param $char : source to be converted into numeric
         * @return string
         */
        public static function convertToNumeric($char){
            if ($char){
            	$data = '';
                $result = str_split($char);
        	    foreach ($result as $key => $value) {
                    $data .= ord($value);
                	}
            	return $data;
            } else {
                return 0;
            }
        }

        /** 
         * Generate Tiny Hash
         *
         * @param $data : source to generate (should be integer only)
         * @return string tiny hash
         */
        public static function generateTinyHash($data){
            return base_convert($data, 10, 36);
        }

        /** 
         * Generate Unique ID
         *
         * @param $lenght : default uniqid gives 13 chars, but you could adjust it to your needs.
         * @return string tiny hash
         */
        public static function generateUniqueID($lenght = 13) {
            if (function_exists("random_bytes")) {
                $bytes = random_bytes(ceil($lenght / 2));
            } elseif (function_exists("openssl_random_pseudo_bytes")) {
                $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
            } else {
                throw new Exception("no cryptographically secure random function available");
            }
            return substr(bin2hex($bytes), 0, $lenght);
        }

        /**
         * Generate Unique Numeric ID in PHP
         * Note: 
         * - In 32bit, if set $abs to true will make lengths of digits fixed to 10, but will reduce the level of uniqueness
         * - In 32bit, if set $abs to false sometimes will return 11 digits because of negative number.
         * - This is based of function uniqid() which uniqueness is still not guaranteed as mentioned in http://php.net/manual/en/function.uniqid.php
         *  
         * @param prefix = adding additional value on the first to get more uniqueness level.
         * @param suffix = adding additional value on the last to get more uniqueness level.
         * @param fixedkey = adding additional value on uniqid string before converted to numeric
         * @param abs = Will make sure all return value is positive and fixed length 10 (without any prefix or suffix). Default is true.
         *
         * @return string
         */
        public static function uniqidNumeric($prefix="",$suffix="",$fixedkey="",$abs=true){
            $data = (($abs)?abs(crc32(uniqid($fixedkey))):crc32(uniqid($fixedkey)));
            $pad = (10 - strlen($data));
            if($pad > 0){
                $leading = "";
                for ($i=1;$i<=$pad;$i++){
                    $leading .= '0';
                }
                return $prefix.str_replace('-','00',str_pad($data, 10, $leading, STR_PAD_LEFT)).$suffix;
            }
            return $prefix.str_replace('-','0',$data).$suffix;
        }
        
        /** 
         * Generate reSlim Token when user logged
         *
         * @param $db : Dabatase connection (PDO)
         * @param $username : input the registered username
         * @return json encoded data
         */
        public static function generateToken($db, $username){
            try {
                $hash = self::EncodeAPIKey($username.'::'.date("Y-m-d H:i:s"));
                $db->beginTransaction();
		    	$sql = "INSERT INTO user_auth (Username,RS_Token,Created,Expired) 
    				VALUES (:username,:rstoken,current_timestamp,date_add(current_timestamp, interval 7 day));";
	    			$stmt = $db->prepare($sql);
			   		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		    		$stmt->bindParam(':rstoken', $hash, PDO::PARAM_STR);
			    	if ($stmt->execute()) {
		    			$data = [
			   				'status' => 'success',
			    			'code' => 'RS301',
                            'token' => $hash,
				    		'message' => CustomHandlers::getreSlimMessage('RS301')
					    ];	
    				} else {
	    				$data = [
		    				'status' => 'error',
			   				'code' => 'RS201',
			    			'message' => CustomHandlers::getreSlimMessage('RS201')
				    	];
				    }
    			$db->commit();
	    	} catch (PDOException $e) {
	    		$data = [
		    		'status' => 'error',
				    'code' => $e->getCode(),
				    'message' => $e->getMessage()
    			];
	    		$db->rollBack();
	    	}
		    return $data;
    		$db = null;
        }

        /** 
         * Determine the token is valid
         *
         * @param $db : Dabatase connection (PDO)
         * @param $token : input the token
         * @param $username : input for more secure identify. Default is null.
         * @return boolean true / false 
         */
        public static function validToken($db, $token,$username=null){
            $r = false;
            if (self::isKeyCached('token-'.$username.'-'.$token,600)){
                $r = true;
            } else {
                $sql = "SELECT a.Username
			        FROM user_auth a 
                    INNER JOIN user_data b ON a.Username = b.Username
        			WHERE b.StatusID = '1' AND a.RS_Token = BINARY :token AND a.Expired > current_timestamp LIMIT 1;";
	        	$stmt = $db->prepare($sql);
		        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        		if ($stmt->execute()) {	
                    if ($stmt->rowCount() > 0){
                        if ($username == null){
                            $r = true;
                            self::writeCache('token-'.$username.'-'.$token);
                        } else {
                            $single = $stmt->fetch();
					        if ($single['Username'] == strtolower($username)){
                                $r = true;
                                self::writeCache('token-'.$username.'-'.$token,$username);
                            }
                        }                    
                    }          	   	
	    	    }
            } 		
		    return $r;
    		$this->db = null;
        }

        /** 
         * Get all data user token
         *
         * @param $db : Dabatase connection (PDO)
         * @param $username : input username to get data token
         * @return json encoded data
         */
        public static function getDataToken($db, $username){
            $r = false;
		    $sql = "SELECT a.Username,a.RS_Token,a.Created,a.Expired
			    FROM user_auth a 
                INNER JOIN user_data b ON a.Username = b.Username
    			WHERE a.Username=:username AND b.StatusID = '1' AND a.Expired > current_timestamp
                ORDER BY a.Expired ASC;";
	    	$stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            if ($stmt->execute()) {	
                if ($stmt->rowCount() > 0){
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $data = [
                        'results' => $results, 
                        'status' => 'success', 
                        'code' => 'RS501',
                        'message' => CustomHandlers::getreSlimMessage('RS501')
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 'RS601',
                        'message' => CustomHandlers::getreSlimMessage('RS601')
                    ];
                }          	   	
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 'RS202',
                    'message' => CustomHandlers::getreSlimMessage('RS202')
                ];
            }	
		    return $data;
    		$this->db = null;
        }

        /** 
         * To clear single token user
         *
         * @param $db : Dabatase connection (PDO)
         * @param $username : input the registered username
         * @param $token : input the token
         * @return json encoded data 
         */
        public static function clearSingleToken($db, $username, $token){
            try{
                $db->beginTransaction();

                $sql = "DELETE FROM user_auth 
                    WHERE Username = :username AND RS_Token = :token;";
	        	$stmt = $db->prepare($sql);
		        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        		$stmt->execute();
                
                $db->commit();

                $data = [
			   		'status' => 'success',
			    	'code' => 'RS305',
				    'message' => CustomHandlers::getreSlimMessage('RS305')
                ];
                self::deleteCache('token-'.$username.'-'.$token,30);
                self::deleteCache('token--'.$token,30);
                self::deleteCache('token-'.$token.'-group',30);
            } catch (PDOException $e){
                $data = [
		    		'status' => 'error',
				    'code' => $e->getCode(),
				    'message' => $e->getMessage()
    			];
                $db->rollBack();
            }
            return $data;
            $db = null;
        }

        /** 
         * To clear all token user except active one
         *
         * @param $db : Dabatase connection (PDO)
         * @param $username : input the registered username
         * @param $token : input the token
         * @return json encoded data 
         */
        public static function clearSafeUserToken($db, $username, $safetoken){
            try{
                $db->beginTransaction();

                $sql = "DELETE FROM user_auth 
                    WHERE Username = :username AND RS_Token <> :token;";
	        	$stmt = $db->prepare($sql);
		        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':token', $safetoken, PDO::PARAM_STR);
        		$stmt->execute();
                
                $db->commit();

                $data = [
			   		'status' => 'success',
			    	'code' => 'RS305',
				    'message' => CustomHandlers::getreSlimMessage('RS305')
                ];
                // Tell server to refresh all keys above 10 minutes old
                self::deleteCacheAll('token-'.'*',600);
            } catch (PDOException $e){
                $data = [
		    		'status' => 'error',
				    'code' => $e->getCode(),
				    'message' => $e->getMessage()
    			];
                $db->rollBack();
            }
            return $data;
            $db = null;
        }

        /** 
         * To clear any expired token after user logout
         *
         * @param $db : Dabatase connection (PDO)
         * @param $username : input the registered username
         * @param $token : input the token
         * @return json encoded data 
         */
        public static function clearToken($db, $username, $token){
            try{
                $db->beginTransaction();

                $sql = "DELETE FROM user_auth 
                    WHERE Username = :username AND RS_Token = :token;";
	        	$stmt = $db->prepare($sql);
		        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        		$stmt->execute();

                $sql = "DELETE FROM user_auth 
                    WHERE Username = :username AND Expired < current_timestamp;";
	        	$stmt = $db->prepare($sql);
		        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        		$stmt->execute();
                
                $db->commit();

                $data = [
			   		'status' => 'success',
			    	'code' => 'RS305',
				    'message' => CustomHandlers::getreSlimMessage('RS305')
                ];
                self::deleteCache('token--'.$token,30);
                self::deleteCacheAll('token-'.$username.'*',30);
                self::deleteCacheAll('token-'.$token.'-group',30);
            } catch (PDOException $e){
                $data = [
		    		'status' => 'error',
				    'code' => $e->getCode(),
				    'message' => $e->getMessage()
    			];
                $db->rollBack();
            }
            return $data;
            $db = null;
        }

        /** 
         * To clear any token after user change password
         *
         * @param $db : Dabatase connection (PDO)
         * @param $username : input the registered username
         * @param $token : input the token
         * @return json encoded data 
         */
        public static function clearUserToken($db, $username){
            try{
                $db->beginTransaction();

                $sql = "DELETE FROM user_auth 
                    WHERE Username = :username;";
	        	$stmt = $db->prepare($sql);
		        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        		$stmt->execute();
                
                $db->commit();
                
                $data = [
			   		'status' => 'success',
			    	'code' => 'RS305',
				    'message' => CustomHandlers::getreSlimMessage('RS305')
                ];
                // Tell server to refresh all keys above 10 minutes old
                self::deleteCacheAll('token-'.'*',600);
            } catch (PDOException $e){
                $data = [
		    		'status' => 'error',
				    'code' => $e->getCode(),
				    'message' => $e->getMessage()
    			];
                $db->rollBack();
            }
            return $data;
            $db = null;
        }

        /** 
         * Get informasi role user by token
         *
         * @param $db : Dabatase connection (PDO)
         * @param $token : input the token
         * @return string RoleID 
         */
        public static function getRoleID($db, $token){
            $roles = 0;
            if (self::isKeyCached('token-'.$token.'-group',600)){
                $data = json_decode(self::loadCache('token-'.$token.'-group'));
                if (!empty($data)){
                    $roles = $data->Role;
                }
            } else {
                $sql = "SELECT b.RoleID
			    	FROM user_auth a 
				    INNER JOIN user_data b ON a.Username = b.Username
    				WHERE a.RS_Token = BINARY :token LIMIT 1;";
	    		$stmt = $db->prepare($sql);
		    	$stmt->bindParam(':token', $token, PDO::PARAM_STR);
			    if ($stmt->execute()){
				    if ($stmt->rowCount() > 0){
    					$single = $stmt->fetch();
                        $roles = $single['RoleID'];
                        self::writeCache('token-'.$token.'-group',$roles);
		    		}
			    }
            }
			return $roles;
			$db = null;
        }

        /** 
         * Add and Generate reSlim API Key
         *
         * @param $db : Dabatase connection (PDO)
         * @param $domain : input the registered domain for origin apikey
         * @param $username : input the registered username
         * @return json encoded data
         */
        public static function generateAPIKey($db, $domain, $username){
            if (self::isDomainExist($db,$domain) == false){
                try {
                    $hash = self::EncodeAPIKey($domain.'::'.date("Y-m-d H:i:s"));
                    $db->beginTransaction();
	    	    	$sql = "INSERT INTO user_api (Domain,ApiKey,StatusID,Created_at,Username) 
    	    			VALUES (:domain,:apikey,'1',current_timestamp,:username);";
	    			$stmt = $db->prepare($sql);
			   		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		    		$stmt->bindParam(':apikey', $hash, PDO::PARAM_STR);
                    $stmt->bindParam(':domain', $domain, PDO::PARAM_STR);
			    	if ($stmt->execute()) {
		    			$data = [
			   				'status' => 'success',
			    			'code' => 'RS101',
                            'apikey' => $hash,
				    		'message' => CustomHandlers::getreSlimMessage('RS101')
					    ];	
    				} else {
	    				$data = [
		    				'status' => 'error',
			   				'code' => 'RS201',
			    			'message' => CustomHandlers::getreSlimMessage('RS201')
				    	];
				    }
    			    $db->commit();
    	    	} catch (PDOException $e) {
	        		$data = [
		        		'status' => 'error',
			    	    'code' => $e->getCode(),
				        'message' => $e->getMessage()
        			];
	        		$db->rollBack();
	        	}
            } else {
                $data = [
		    		'status' => 'error',
					'code' => 'RS916',
	    			'message' => CustomHandlers::getreSlimMessage('RS916')
			    ];
            }
            
		    return $data;
    		$db = null;
        }

         /** 
         * Determine the API Key is valid
         *
         * @param $db : Dabatase connection (PDO)
         * @param $apikey : input the token
         * @param $domain : input for origin apikey. Default is null.
         * @return boolean true / false 
         */
        public static function validAPIKey($db, $apikey,$domain=null){
            $r = false;
            if (self::isKeyCached('api-'.$apikey)){
                $r = true;
            } else {
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
                            self::writeCache('api-'.$apikey);
                        } else {
                            $single = $stmt->fetch();
					        if (strtolower($single['Domain']) == strtolower($domain)){
                                $r = true;
                                self::writeCache('api-'.$apikey,$domain);
                            }
                        }       
                    }          	   	
    	    	}
            }
		    return $r;
    		$this->db = null;
        }

        /** 
         * Determine the Domain is exist
         *
         * @param $db : Dabatase connection (PDO)
         * @param $domain : input for origin apikey.
         * @return boolean true / false 
         */
        public static function isDomainExist($db, $domain){
            $r = false;
		    $sql = "SELECT a.Domain
			    FROM user_api a 
                INNER JOIN user_data b ON a.Username = b.Username
    			WHERE a.Domain = :domain;";
	    	$stmt = $db->prepare($sql);
		    $stmt->bindParam(':domain', $domain, PDO::PARAM_STR);
    		if ($stmt->execute()) {	
                if ($stmt->rowCount() > 0){
                    $r = true;                   
                }          	   	
	    	} 		
		    return $r;
    		$this->db = null;
        }

        /** 
         * Update API Key
         *
         * @param $db : Dabatase connection (PDO)
         * @param $username : input the registered username
         * @param $apikey : input the api key
         * @param $statusid : input the statusid in number. (1 or 42)
         * @return json encoded data 
         */
        public static function updateAPIKey($db, $username, $apikey, $statusid){
            try{
                $db->beginTransaction();

                $sql = "UPDATE user_api a SET a.StatusID=:statusid,a.Updated_by=:username 
                    WHERE a.ApiKey = :apikey;";
	        	$stmt = $db->prepare($sql);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
		        $stmt->bindParam(':apikey', $apikey, PDO::PARAM_STR);
                $stmt->bindParam(':statusid', $statusid, PDO::PARAM_STR);
        		$stmt->execute();
                
                $db->commit();

                $data = [
			   		'status' => 'success',
			    	'code' => 'RS103',
				    'message' => CustomHandlers::getreSlimMessage('RS103')
				];
            } catch (PDOException $e){
                $data = [
		    		'status' => 'error',
				    'code' => $e->getCode(),
				    'message' => $e->getMessage()
    			];
                $db->rollBack();
            }
            return $data;
            $db = null;
        }

        /** 
         * To clear API Key
         *
         * @param $db : Dabatase connection (PDO)
         * @param $apikey : input the api key
         * @return json encoded data 
         */
        public static function clearAPIKey($db, $apikey){
            try{
                $db->beginTransaction();

                $sql = "DELETE FROM user_api WHERE ApiKey = :apikey;";
	        	$stmt = $db->prepare($sql);
                $stmt->bindParam(':apikey', $apikey, PDO::PARAM_STR);
        		$stmt->execute();
                
                $db->commit();

                $data = [
			   		'status' => 'success',
			    	'code' => 'RS306',
				    'message' => CustomHandlers::getreSlimMessage('RS306')
                ];
                self::deleteCache($apikey);
            } catch (PDOException $e){
                $data = [
		    		'status' => 'error',
				    'code' => $e->getCode(),
				    'message' => $e->getMessage()
    			];
                $db->rollBack();
            }
            return $data;
            $db = null;
        }


        // CACHE TOKEN / API KEYS=========================

        /**
         * Cache will run if you set variable runcache to true
         * If you set to false, this will only disable the cache process
         */
        private static $runcache = true;

        /**
         * Default folder is cache-keys
         * Path folder is api/cache-keys/ 
         */
        private static $filefolder = 'cache-keys';

        /**
		 * Get filepath cache
         * 
         * @param key = Filename (without .cache), token or api key value
		 *
		 * @return string
		 */
        public static function filePath($key){
            if (!is_dir(self::$filefolder)) {
                mkdir(self::$filefolder,0775,true);
            }            
            return self::$filefolder.'/'.$key.'.cache';
        }

        /**
         * Determine is current key already cached or not
         * 
         * @param cachetime = Set expired time in second. Default value is 3600 seconds (1 hour)
         * 
         * @return bool
         */
        public static function isKeyCached($key,$cachetime=3600) {
            if (self::$runcache){
                $file = self::filePath($key);
                // check the expired file cache.
                $mtime = 0;
                if (file_exists($file)) {
                    $mtime = filemtime($file);
                }
                $filetimemod = $mtime + $cachetime;
                // if the renewal date is smaller than now, return true; else false (no need for update)
                if ($filetimemod < time()) {
                    return false;
                }
            } else {
                return false;
            }
            return true;
        }

        /**
         * Load cached file
         * 
         * @param key = Filename (without .cache), token or api key value
         * 
         * @return string
         */
        public static function loadCache($key) {
            $file = self::filePath($key);
            if (file_exists($file)) {
                return file_get_contents($file);
            }
            return "";
        }

        /**
         * Write key to static file cache
         * 
         * @param key = Filename (without .cache), token or api key value
         * @param roleid = input with user role id. (This will work for cache role id only)
         * 
         */
        public static function writeCache($key,$roleid="") {
            if (!empty($key)) {
                $file = self::filePath($key);
                $content = '{"Key":"'.$key.'","Refreshed":"'.date('Y-m-d h:i:s a', time()).'"'.(!empty($roleid)?',"Role":"'.$roleid.'"':'').'}';   
                if (self::$runcache) file_put_contents($file, $content, LOCK_EX);
            }
        }

        /**
         * Delete static key file cache
         * 
         * @param key = Filename (without .cache), token or api key value
         * @param agecache = Specify the age of cache file to be deleted. Default will delete file immediately.
         * 
         */
        public static function deleteCache($key,$agecache=0) {
            if (!empty($key)) {
                $file = self::filePath($key);
                if (file_exists($file)){
                    if ($agecache=0){
                        unlink($file);
                    } else {
                        $now   = time();
                        if ($now - filemtime($file) >= $agecache) {
                            unlink($file);
                        }
                    }
                }
            }
        }

        /**
         * Delete all static token / api key file cache
         * 
         * @param wildcard = You can set whatever kind of pathname matching wildcard to be deleted. Default is *
         * @param agecache = Specify the age of cache file to be deleted. Default will delete cached files which is already have more 300 seconds old.
         */
        public static function deleteCacheAll($wildcard="*",$agecache=300) {
            if (file_exists(self::$filefolder)) {
                //Build list cached files
                $files = glob(self::$filefolder.'/'.$wildcard,GLOB_NOSORT);
                $now   = time();

                $total = 0;
                $deleted = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $total++;
                        if ($now - filemtime($file) >= $agecache) {
                            unlink($file);
                            $deleted++;
                        }
                    }
                }
                $datajson = '{"status":"success","age":'.$agecache.',"total_files":'.$total.',"total_deleted":'.$deleted.',"execution_time":"'.(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]).'","message":"To prevent any error occured on the server, only cached files that have age more than '.$agecache.' seconds old, will be deleted."}';
            } else {
                $datajson = '{"status:"error","message":"Directory not found!"}';
            }
            return $datajson;
        }

        /**
         * Determine if cache is activated
         * 
         * @return bool
         */
        public static function isCacheActive() {
            return self::$runcache;
        }

        /**
         * Get total size of the cache folder
         * 
         * @param formatted if set to false will return bytes
         * 
         * @return string
         */
        public static function getCacheSize($formatted=true) {
            $size = 0;
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::$filefolder)) as $file){
                $size += $file->getSize();
            }
            return (($formatted)?self::formatSize($size):$size);
        }

        /**
         * Get total available size on harddisk
         * 
         * @param formatted if set to false will return bytes
         * 
         * @return string
         */
        public static function getCacheAvailSize($formatted=true) {
            return (($formatted)?self::formatSize(disk_free_space(".")):disk_free_space("."));
        }

        /**
         * Get total available size on harddisk
         * 
         * @param formatted if set to false will return bytes
         * 
         * @return string
         */
        public static function getCacheHDDSize($formatted=true){
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $ds = disk_total_space("C:");
            } else {
                $ds = disk_total_space("/");
            }
            return (($formatted)?self::formatSize($ds):$ds);
        }

        /**
         * Get info folder name
         * 
         * @return string
         */
        public static function getCacheFolder() {
            return self::$filefolder;
        }

        /**
         * Get status cache
         * 
         * @return array
         */
        public static function getCacheStatus() {
            if(self::$runcache) {
                return ['status'=>'active','description'=>'Cache is running!'];
            }
            return ['status'=>'disabled','description'=>'Cache is disabled!'];
        }

        /**
         * Get info data cache
         * 
         * @return array
         */
        public static function getCacheInfo() {
            $size = 0;
            $files = -2;
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::$filefolder)) as $file){
                $size += $file->getSize();
                $files++;
            }
            $total = self::getCacheHDDSize(false);
            $free = self::getCacheAvailSize(false);
            $usehdd = $total-$free;
            $usecache = $size;
            $freehddpercent = sprintf('%1.2f',((($total-$usehdd)/$total)*100)).'%';
            $usehddpercent = sprintf('%1.2f',((($total-$free)/$total)*100)).'%';
            $usecachepercent = sprintf('%1.6f',((($total-($total-$usecache))/$total)*100)).'%';
            $data = self::getCacheStatus();
            $data['folder'] = self::$filefolder;
            $data['files'] = $files;
            return [
                'status'=>'success',
                'info'=>$data,
                'size'=>[
                    'cache'=>['use'=>self::formatSize($size),'free'=>self::formatSize($free)],
                    'hdd'=>['use'=>self::formatSize($usehdd),'free'=>self::formatSize($free),'total'=>self::formatSize($total)]
                ],
                'percent'=>[
                    'cache'=>['use'=>$usecachepercent,'free'=>$freehddpercent],
                    'hdd'=>['use'=>$usehddpercent,'free'=>$freehddpercent]
                ],
                'bytes'=>[
                    'cache'=>['use'=>$size,'free'=>$free],
                    'hdd'=>['use'=>$usehdd,'free'=>$free,'total'=>$total]
                ]
            ];
        }

        /**
         * Formatting bytes to human readable format
         * 
         * @param bytes is the value
         * 
         * @return string
         */
        private static function formatSize($bytes) {
            $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
            $base = 1024;
            $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
            return sprintf('%1.2f' ,$bytes / pow($base,$class)).' '.$si_prefix[$class];
        }

    }