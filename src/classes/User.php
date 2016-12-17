<?php

namespace classes;
use PDO;

class User {

	protected $db;

	function __construct($db=null) {
		if (!empty($db)) 
        {
            $this->db = $db;
        }
	}
	
	// Get all data from database mysql
	public function getAll() {
		$r = array();		

		$sql = "SELECT * FROM user a order by a.created;";
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