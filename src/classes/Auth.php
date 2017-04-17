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
            return BaseConverter::convertToBinary($data, self::$characters);
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
    			WHERE b.StatusID = '1' AND a.RS_Token = :token AND a.Expired > current_timestamp;";
	    	$stmt = $db->prepare($sql);
		    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    		if ($stmt->execute()) {	
                if ($stmt->rowCount() > 0){
                    if ($username == null){
                        $r = true;
                    } else {
                        $single = $stmt->fetch();
					    if ($single['Username'] == $username){
                            $r = true;
                        }
                    }                    
                }          	   	
	    	} 		
		    return $r;
    		$this->db = null;
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
				WHERE a.RS_Token=:token;";
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

    }