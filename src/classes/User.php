<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\Auth as Auth;
use PDO;
	/**
     * A class for user management in reSlim
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
     */
	class User {

		protected $db;

		var $Username,$Password,$Fullname,$Address,$Phone,$Email,$Aboutme,$Avatar,$Role,$Status,$Token;

		function __construct($db=null) {
			if (!empty($db)) 
	        {
    	        $this->db = $db;
        	}
		}

		/**
		 * Inserting into database to register user
		 * @return result process in json encoded data
		 */
		private function doRegister(){
			if (strtolower($this->Role) == 'admin'){
				$newRole = '1';
			}else{
				$newRole = '2';
			}
			
			$newusername = strtolower($this->Username);
			$hash = Auth::HashPassword($newusername, $this->Password);
			
			try {
				$this->db->beginTransaction();
				$sql = "INSERT INTO user_data (Username,Password,Fullname,Address,Phone,Email,Aboutme,Avatar,RoleID,StatusID) 
					VALUES (:username,:password,:fullname,:address,:phone,:email,:aboutme,:avatar,:role,'1');";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
					$stmt->bindParam(':password', $hash, PDO::PARAM_STR);
					$stmt->bindParam(':fullname', $this->Fullname, PDO::PARAM_STR);
					$stmt->bindParam(':address', $this->Address, PDO::PARAM_STR);
					$stmt->bindParam(':phone', $this->Phone, PDO::PARAM_STR);
					$stmt->bindParam(':email', $this->Email, PDO::PARAM_STR);
					$stmt->bindParam(':aboutme', $this->Aboutme, PDO::PARAM_STR);
					$stmt->bindParam(':avatar', $this->Avatar, PDO::PARAM_STR);
					$stmt->bindParam(':role', $newRole, PDO::PARAM_STR);
					if ($stmt->execute()) {
						$data = [
							'status' => 'success',
							'code' => 'RS101',
							'message' => CustomHandlers::getreSlimMessage('RS101')
						];	
					} else {
						$data = [
							'status' => 'error',
							'code' => 'RS901',
							'message' => CustomHandlers::getreSlimMessage('RS901')
						];
					}
				$this->db->commit();
			} catch (PDOException $e) {
				$data = [
					'status' => 'error',
					'code' => $e->getCode(),
					'message' => $e->getMessage()
				];
				$this->db->rollBack();
			}
			return $data;
			$this->db = null;
		}

		/**
		 * Update user
		 * @return result process in json encoded data
		 */
		private function doUpdate(){
			if (strtolower($this->Role) == 'admin'){
				$newRole = '1';
			}else{
				$newRole = '2';
			}
			
			$newusername = strtolower($this->Username);
			
			try {
				$this->db->beginTransaction();
				$sql = "UPDATE user_data 
					SET Fullname=:fullname,Address=:address,Phone=:phone,Email=:email,Aboutme=:aboutme,Avatar=:avatar,
					RoleID=:role,StatusID=:status  
					WHERE Username=:username;";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
					$stmt->bindParam(':fullname', $this->Fullname, PDO::PARAM_STR);
					$stmt->bindParam(':address', $this->Address, PDO::PARAM_STR);
					$stmt->bindParam(':phone', $this->Phone, PDO::PARAM_STR);
					$stmt->bindParam(':email', $this->Email, PDO::PARAM_STR);
					$stmt->bindParam(':aboutme', $this->Aboutme, PDO::PARAM_STR);
					$stmt->bindParam(':avatar', $this->Avatar, PDO::PARAM_STR);
					$stmt->bindParam(':role', $newRole, PDO::PARAM_STR);
					$stmt->bindParam(':status', $this->Status, PDO::PARAM_STR);
					if ($stmt->execute()) {
						$data = [
							'status' => 'success',
							'code' => 'RS101',
							'message' => CustomHandlers::getreSlimMessage('RS101')
						];	
					} else {
						$data = [
							'status' => 'error',
							'code' => 'RS904',
							'message' => CustomHandlers::getreSlimMessage('RS904')
						];
					}
				$this->db->commit();
			} catch (PDOException $e) {
				$data = [
					'status' => 'error',
					'code' => $e->getCode(),
					'message' => $e->getMessage()
				];
				$this->db->rollBack();
			}
			return $data;
			$this->db = null;
		}

		/**
		 * Delete user
		 * @return result process in json encoded data
		 */
		private function doDelete(){
			$newusername = strtolower($this->Username);
			
			try {
				$this->db->beginTransaction();
				$sql = "DELETE FROM user_data WHERE Username=:username;";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
					if ($stmt->execute()) {
						$data = [
							'status' => 'success',
							'code' => 'RS104',
							'message' => CustomHandlers::getreSlimMessage('RS104')
						];	
					} else {
						$data = [
							'status' => 'error',
							'code' => 'RS905',
							'message' => CustomHandlers::getreSlimMessage('RS905')
						];
					}
				$this->db->commit();
			} catch (PDOException $e) {
				$data = [
					'status' => 'error',
					'code' => $e->getCode(),
					'message' => $e->getMessage()
				];
				$this->db->rollBack();
			}
			return $data;
			$this->db = null;
		}

		/**
		 * Determine if user is already registered or not
		 * @return boolean true / false
		 */
		private function isRegistered(){
			$r = false;
			$sql = "SELECT a.Username
				FROM user_data a 
				WHERE a.Username = :username;";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':username', $this->Username, PDO::PARAM_STR);
			if ($stmt->execute()) {	
            	if ($stmt->rowCount() > 0){
	                $r = true;
    	        }          	   	
			} 		
			return $r;
			$this->db = null;
		}

		/**
		 * Determine if password is match
		 * @return boolean true / false
		 */
		private function isPasswordMatch(){
			$match = false;
			$sql = "SELECT a.Password
				FROM user_data a 
				WHERE a.Username = :username;";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':username', $this->Username, PDO::PARAM_STR);
			if ($stmt->execute()){
				if ($stmt->rowCount() > 0){
					$single = $stmt->fetch();
					if ($single['Password'] == Auth::HashPassword($this->Username, $this->Password)){
						$match = true;
					}
				}
			}
			return $match;
			$this->db = null;
		}

		public function showOptionRole() {

		}

		public function showOptionStatus() {

		}
	
		/** 
		 * Get all data user
		 * @return result process in json encoded data
		 */
		public function showAll() {
			if (Auth::ValidToken($this->db,$this->Token)){
				$sql = "SELECT a.Username, a.Fullname, a.Address, a.Phone, a.Email, a.Aboutme,a.Avatar, b.Role , c.Status,
					a.Created_at, a.Updated_at
					FROM user_data a 
					INNER JOIN user_role b ON a.RoleID = b.RoleID
					INNER JOIN core_status c ON a.StatusID = c.StatusID
					ORDER BY a.Fullname ASC;";
				$stmt = $this->db->prepare($sql);		

				if ($stmt->execute()) {	
        		    if ($stmt->rowCount() > 0){
            		   	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
						$data = [
			                'result' => $results, 
    	    		        'status' => 'success', 
			                'code' => 'RS501',
        			        'message' => CustomHandlers::getreSlimMessage('RS501')
						];
		            }
    		        else{
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
			} else {
				$data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
			}		
        
			return json_encode($data, JSON_PRETTY_PRINT);
	        $this->db= null;
		}

		/** 
		 * Regiter new user
		 * @return result process in json encoded data
		 */
		public function register(){
			if ( preg_match('/[A-Za-z0-9]+/',$this->Username) == false ){
				$data = [
					'status' => 'error',
					'code' => 'RS804',
					'message' => CustomHandlers::getreSlimMessage('RS804')
				];
			} else {
				if ($this->isRegistered() == false){
					$data = $this->doRegister();
				} else {
					$data = [
						'status' => 'error',
						'code' => 'RS902',
						'message' => CustomHandlers::getreSlimMessage('RS902')
					];
				}
			}
			
			return json_encode($data,JSON_PRETTY_PRINT);
		}

		/** 
		 * Login user
		 * @return result process in json encoded data
		 */
		public function login(){
			if ( preg_match('/[A-Za-z0-9]+/',$this->Username) == false ){
				$data = [
					'status' => 'error',
					'code' => 'RS804',
					'message' => CustomHandlers::getreSlimMessage('RS804')
				];
			} else {
				if ($this->isRegistered()){
					if ($this->isPasswordMatch()){
						$data = Auth::GenerateToken($this->db,$this->Username);
					} else {
						$data = [
							'status' => 'error',
							'code' => 'RS903',
							'message' => CustomHandlers::getreSlimMessage('RS903')
						];
					}
				} else {
					$data = [
						'status' => 'error',
						'code' => 'RS902',
						'message' => CustomHandlers::getreSlimMessage('RS902')
					];
				}
			}
		
			return json_encode($data, JSON_PRETTY_PRINT);
		}

		/** 
		 * Logout user
		 * @return result process in json encoded data
		 */
		public function logout(){
			$data = Auth::ClearToken($this->db,$this->Username,$this->Token);
			return json_encode($data, JSON_PRETTY_PRINT);
		}

		/** 
		 * Update user
		 * @return result process in json encoded data
		 */
		public function update(){
			if (Auth::ValidToken($this->db,$this->Token)){
				$data = doUpdate();
			} else {
				$data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
			}
			return json_encode($data, JSON_PRETTY_PRINT);
		}

		/** 
		 * Delete user
		 * @return result process in json encoded data
		 */
		public function delete(){
			if (Auth::ValidToken($this->db,$this->Token)){
				$data = doDelete();
			} else {
				$data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
			}
			return json_encode($data, JSON_PRETTY_PRINT);
		}
	}