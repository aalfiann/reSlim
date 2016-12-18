<?php

namespace classes;
use \classes\Auth as Auth;
use PDO;

class User {

	protected $db;

	var $Username,$Password,$Fullname,$Address,$Phone,$Email,$Aboutme,$Avatar,$Role,$Status;

	function __construct($db=null) {
		if (!empty($db)) 
        {
            $this->db = $db;
        }
	}
	
	// Get all data from database mysql
	public function getAll() {
		$r = array();		

		$sql = "SELECT a.Username, a.Fullname, a.Address, a.Phone, a.Email, b.Role , c.Status
			FROM user_data a 
			INNER JOIN user_role b ON a.RoleID = b.RoleID
			INNER JOIN core_status c ON a.StatusID = c.StatusID
			ORDER BY a.Fullname ASC;";
		$stmt = $this->db->prepare($sql);		

		if ($stmt->execute()) {	
            if ($stmt->rowCount() > 0){
                $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else{
                $r = 0;
            }          	   	
		} else {
			$r = 0;
		}		
        
		return $r;
        $stmt->Close();
	}

	// Regiter new user
	public function register(){
		if ( preg_match('/\s/',$this->Username) ){
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

	private function doRegister(){
		if (strtolower($this->Role) == 'admin'){
				$newRole = '1';
			}else{
				$newRole = '2';
			}

			$hash = Auth::HashPassword($this->Username, $this->Password);
			
			try {
				$this->db->beginTransaction();
				$sql = "INSERT INTO user_data (Username,Password,Fullname,Address,Phone,Email,Aboutme,Avatar,RoleID,StatusID) 
					VALUES (:username,:password,:fullname,:address,:phone,:email,:aboutme,:avatar,:role,'1');";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(':username', $this->Username, PDO::PARAM_STR);
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
	}

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
	}

	// Login user
	public function login(){
		
	}

	// Logout user
	public function logout(){
		
	}

	// Auth User
	private function auth(){
		
	}

	// Update User
	public function update(){
		
	}

	// Delete User
	public function delete(){
		
	}
}