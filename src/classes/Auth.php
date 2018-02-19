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
                    } else {
                        $single = $stmt->fetch();
					    if ($single['Username'] == strtolower($username)){
                            $r = true;
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
            if (self::isAPICached($apikey)){
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
                        } else {
                            $single = $stmt->fetch();
					        if (strtolower($single['Domain']) == strtolower($domain)){
                                $r = true;
                            }
                        }
                        self::writeCache($apikey);            
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


        // CACHE API KEYS=========================

        /**
         * Cache will run if you set variable runcache to true
         * If you set to false, this only disable the cache process and will not deleted the current cache files
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
		 * @return string
		 */
        public static function filePath($apikey){
            if (!is_dir(self::$filefolder)) {
                mkdir(self::$filefolder,0775,true);
            }            
            return self::$filefolder.'/'.$apikey.'.cache';
        }

        /**
         * Determine is current apikey already cached or not
         * 
         * @param cachetime = Set expired time in second. Default value is 3600 seconds (1 hour)
         * 
         * @return bool
         */
        public static function isAPICached($apikey,$cachetime=3600) {
            if (self::$runcache){
                $file = self::filePath($apikey);
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
         * Write api key to static file cache
         * 
         * @param apikey = apikey value
         * 
         */
        public static function writeCache($apikey) {
            if (!empty($apikey)) {
                $file = self::filePath($apikey);
                $content = '{"APIKey":"'.$apikey.'","Refreshed":"'.date('Y-m-d h:i:s a', time()).'"}';   
                if (self::$runcache) file_put_contents($file, $content, LOCK_EX);
            }
        }

        /**
         * Delete static api key file cache
         * 
         * @param apikey = apikey value
         * 
         */
        public static function deleteCache($apikey) {
            if (!empty($apikey)) {
                $file = self::filePath($apikey);
                if (file_exists($file)){
                    if (self::$runcache) unlink($file);
                }
            }
        }

        /**
         * Delete all static api key file cache
         * 
         * @param apikey = apikey value
         * @param asbool = will make this function return bool
         * 
         * @return bool with condition param
         * 
         */
        public static function deleteCacheAll() {
            if (file_exists(self::$filefolder)) {
                //Auto delete useless cache
                $files = glob(self::$filefolder.'/*');
                $now   = time();

                $total = 0;
                $deleted = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $total++;
                        if ($now - filemtime($file) >= 60 * 5) { // 5 minutes ago
                            unlink($file);
                            $deleted++;
                        }
                    }
                }
                $datajson = '{"status":"success","total_files":'.$total.',"total_deleted":'.$deleted.',"execution_time":"'.(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]).'","message":"To prevent any error occured on the server, only cache files that have age more than 5 minutes old, will be deleted."}';
            } else {
                $datajson = '{"status:"error","message":"Directory not found!"}';
            }
            return $datajson;
        }

    }