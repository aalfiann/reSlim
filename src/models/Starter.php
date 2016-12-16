<?php

namespace models;
use PDO;

class Starter {

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

    public function setHello() {
        return array(
			'hello' => "Hello World!!!",
			'description1' => "Use this document as a way to quickly start any new project.",
			'description2' => "All you get is this text and a mostly barebones HTML document.",
			'author' => "iSlim3 is forged by M ABD AZIZ ALFIAN"
			);
    }
}