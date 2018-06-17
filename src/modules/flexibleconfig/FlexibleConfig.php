<?php
namespace modules\flexibleconfig;                   //Make sure namespace is same structure with parent directory

use \classes\Auth as Auth;                          //For authentication internal user
use \classes\JSON as JSON;                          //For handling JSON in better way
use \classes\CustomHandlers as CustomHandlers;      //To get default response message
use \classes\Validation as Validation;              //To validate the string
use PDO;                                            //To connect with database

	/**
     * Example to create flexibleconfig module in reSlim
     *
     * @package    modules/flexibleconfig
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim-modules-flexibleconfig/blob/master/LICENSE.md  MIT License
     */
    class FlexibleConfig {

        // database var
		protected $db,$dbconfig;
		
		//base var
        protected $basepath,$baseurl,$basemod;

        //master var
        var $username,$token;

        //data var
		var $key,$value,$description,$created_at,$created_by,$updated_at,$updated_by;

		//folder data
		var $folderdata = 'flexibleconfig-data';

        //search var
        var $search;
        
        //pagination var
        var $page,$itemsPerPage;
        
        //construct database object
        function __construct($db=null,$baseurl=null) {
			if (!empty($db)) $this->db = $db;
            $this->baseurl = (($this->isHttps())?'https://':'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
			$this->basepath = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);
            $this->basemod = dirname(__FILE__);
            $this->dbconfig = $this->dataConfig();
        }
        
        //Detect scheme host
        function isHttps() {
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

        //Get modules information
        public function viewInfo(){
            return file_get_contents($this->basemod.'/package.json');
        }


        //FlexibleConfig===========================================


		private function isDataVerified(){
			if (!is_dir($this->folderdata)) mkdir($this->folderdata,0775,true);
			if (!file_exists($this->folderdata.'/data.sqlite3')){
				return copy($this->basemod.'/table.sqlite3',$this->folderdata.'/data.sqlite3');
			} else {
				return true;
			}
			return false;
		}

        private function dataConfig() {
			if ($this->isDataVerified()){
				$dir = 'sqlite:'.$this->folderdata.'/data.sqlite3';
            	$pdo  = new PDO($dir);
	            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        	    return $pdo;
			}
			return null;
        }

        public function add(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
    		    try {
    				$this->dbconfig->beginTransaction();
	    			$sql = "INSERT INTO config (key,value,description,created_at,created_by) 
		    			VALUES (:key,:value,:description,current_timestamp,:created_by);";
					$stmt = $this->dbconfig->prepare($sql);
					$stmt->bindParam(':key', $this->key, PDO::PARAM_STR);
					$stmt->bindParam(':value', $this->value, PDO::PARAM_STR);
					$stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
					$stmt->bindParam(':created_by', $this->username, PDO::PARAM_STR);
                    if ($stmt->execute()) {
						$data = [
							'status' => 'success',
							'code' => 'RS101',
							'message' => CustomHandlers::getreSlimMessage('RS101')
						];	
					} else {
    					$data = [
					    	'status' => 'error',
				    		'code' => 'RS201',
			    			'message' => CustomHandlers::getreSlimMessage('RS201')
		    			];
	    			}
	    			$this->dbconfig->commit();
    			} catch (PDOException $e) {
			        $data = [
    	    			'status' => 'error',
	    				'code' => $e->getCode(),
    	    			'message' => $e->getMessage()
    			    ];
	    		    $this->dbconfig->rollBack();
    		    }
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }

			return JSON::encode($data,true);
			$this->dbconfig = null;
        }

        public function update() {
            if (Auth::validToken($this->db,$this->token,$this->username)){
    		    try {
    				$this->dbconfig->beginTransaction();
	    			$sql = "UPDATE config 
                        SET value=:value,Description=:description,Updated_at=current_timestamp,Updated_by=:updated_by
                        WHERE key=:key;";
					$stmt = $this->dbconfig->prepare($sql);
					$stmt->bindParam(':value', $this->value, PDO::PARAM_STR);
					$stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
					$stmt->bindParam(':updated_by', $this->username, PDO::PARAM_STR);
					$stmt->bindParam(':key', $this->key, PDO::PARAM_STR);
                    if ($stmt->execute()) {
						$data = [
							'status' => 'success',
							'code' => 'RS103',
							'message' => CustomHandlers::getreSlimMessage('RS103')
						];	
					} else {
    					$data = [
					    	'status' => 'error',
				    		'code' => 'RS203',
			    			'message' => CustomHandlers::getreSlimMessage('RS203')
		    			];
	    			}
	    			$this->dbconfig->commit();
    			} catch (PDOException $e) {
			        $data = [
    	    			'status' => 'error',
	    				'code' => $e->getCode(),
    	    			'message' => $e->getMessage()
    			    ];
	    		    $this->dbconfig->rollBack();
    		    }
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }

			return JSON::encode($data,true);
			$this->dbconfig = null;
        }

        public function delete() {
            if (Auth::validToken($this->db,$this->token,$this->username)){
    		    try {
    				$this->dbconfig->beginTransaction();
	    			$sql = "DELETE FROM config WHERE key=:key;";
					$stmt = $this->dbconfig->prepare($sql);
					$stmt->bindParam(':key', $this->key, PDO::PARAM_STR);
                    if ($stmt->execute()) {
						$data = [
							'status' => 'success',
							'code' => 'RS104',
							'message' => CustomHandlers::getreSlimMessage('RS104')
						];	
					} else {
    					$data = [
					    	'status' => 'error',
				    		'code' => 'RS204',
			    			'message' => CustomHandlers::getreSlimMessage('RS204')
		    			];
	    			}
	    			$this->dbconfig->commit();
    			} catch (PDOException $e) {
			        $data = [
    	    			'status' => 'error',
	    				'code' => $e->getCode(),
    	    			'message' => $e->getMessage()
    			    ];
	    		    $this->dbconfig->rollBack();
    		    }
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }

			return JSON::encode($data,true);
			$this->dbconfig = null;
        }

        public function read() {
            if (Auth::validToken($this->db,$this->token,$this->username)){
				//calculate keys
				$datakey = explode(',',$this->key);
				$listkeys = "";
				$listdata = "{";
				$n=0;
				foreach($datakey as $key){
					if(!empty(trim($key))){
						$listkeys .= 'key = :key'.$n.' or ';
    					$listdata .= '":key'.$n.'":"'.trim($key).'",';
						$n++;
					}
				}

				$listkeys = rtrim($listkeys," or ");
				$listdata = rtrim($listdata,",").'}';

				if ($n > 1){
					$sql = "SELECT key,value,description,created_at,created_by,Updated_at,Updated_by
						FROM config
						WHERE ".$listkeys.";";
				
					$stmt = $this->dbconfig->prepare($sql);
					if ($stmt->execute(json_decode($listdata,true))) {	
						$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
						if ($results && count($results)){
							$data = [
								'result' => $results, 
								'status' => 'success', 
								'code' => 'RS501',
								'message' => CustomHandlers::getreSlimMessage('RS501')
							];
						} else {
							$data = [
								'result' => [],
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
					$sql = "SELECT key,value,description,created_at,created_by,Updated_at,Updated_by
						FROM config
						WHERE key = :key LIMIT 1;";
				
					$stmt = $this->dbconfig->prepare($sql);		
					$stmt->bindParam(':key', $this->key, PDO::PARAM_STR);

					if ($stmt->execute()) {	
						$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
						if ($results && count($results)){
							$data = [
								'result' => $results, 
								'status' => 'success', 
								'code' => 'RS501',
								'message' => CustomHandlers::getreSlimMessage('RS501')
							];
						} else {
							$data = [
								'result' => [],
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
				}
			} else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
			}
			
			return JSON::encode($data,true);
	        $this->dbconfig = null;
		}
		
		public function readPublic() {
			//calculate keys
			$datakey = explode(',',$this->key);
			$listkeys = "";
			$listdata = "{";
			$n=0;
			foreach($datakey as $key){
				if(!empty(trim($key))){
					$listkeys .= 'key = :key'.$n.' or ';
					$listdata .= '":key'.$n.'":"'.trim($key).'",';
					$n++;
				}
			}

			$listkeys = rtrim($listkeys," or ");
			$listdata = rtrim($listdata,",").'}';

			if ($n > 1){
				$sql = "SELECT key,value,description,created_at,created_by,Updated_at,Updated_by
					FROM config
					WHERE ".$listkeys.";";
			
				$stmt = $this->dbconfig->prepare($sql);
				if ($stmt->execute(json_decode($listdata,true))) {	
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($results && count($results)){
						$data = [
							'result' => $results, 
							'status' => 'success', 
							'code' => 'RS501',
							'message' => CustomHandlers::getreSlimMessage('RS501')
						];
					} else {
						$data = [
							'result' => [],
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
				$sql = "SELECT key,value,description,created_at,created_by,Updated_at,Updated_by
					FROM config
					WHERE key = :key LIMIT 1;";
			
				$stmt = $this->dbconfig->prepare($sql);		
				$stmt->bindParam(':key', $this->key, PDO::PARAM_STR);

				if ($stmt->execute()) {	
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($results && count($results)){
						$data = [
							'result' => $results, 
							'status' => 'success', 
							'code' => 'RS501',
							'message' => CustomHandlers::getreSlimMessage('RS501')
						];
					} else {
						$data = [
							'result' => [],
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
			}
			
			return JSON::encode($data,true);
	        $this->dbconfig = null;
        }

        public function index() {
            if (Auth::validToken($this->db,$this->token)){
				$search = "%$this->search%";
				//count total row
				$sqlcountrow = "SELECT count(a.key) as TotalRow 
					from config a
					where a.key like :search
					order by a.key asc;";
				$stmt = $this->dbconfig->prepare($sqlcountrow);		
				$stmt->bindParam(':search', $search, PDO::PARAM_STR);
				
				if ($stmt->execute()) {	
					$single = $stmt->fetch();
    	    		if ($single && count($single)){
						
						// Paginate won't work if page and items per page is negative.
						// So make sure that page and items per page is always return minimum zero number.
						$newpage = Validation::integerOnly($this->page);
						$newitemsperpage = Validation::integerOnly($this->itemsPerPage);
						$limits = (((($newpage-1)*$newitemsperpage) <= 0)?0:(($newpage-1)*$newitemsperpage));
						$offsets = (($newitemsperpage <= 0)?0:$newitemsperpage);

						// Query Data
						$sql = "SELECT a.key,a.value,a.description,a.created_at,a.created_by,a.Updated_at,a.Updated_by 
							from config a
							where a.key like :search
							order by a.key asc LIMIT :limpage , :offpage;";
						$stmt2 = $this->dbconfig->prepare($sql);
						$stmt2->bindParam(':search', $search, PDO::PARAM_STR);
						$stmt2->bindValue(':limpage', (INT) $limits, PDO::PARAM_INT);
						$stmt2->bindValue(':offpage', (INT) $offsets, PDO::PARAM_INT);
						
						if ($stmt2->execute()){
							$pagination = new \classes\Pagination();
							$pagination->totalRow = $single['TotalRow'];
							$pagination->page = $this->page;
							$pagination->itemsPerPage = $this->itemsPerPage;
							$pagination->fetchAllAssoc = $stmt2->fetchAll(PDO::FETCH_ASSOC);
							$data = $pagination->toDataArray();
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
        
			return JSON::safeEncode($data,true);
	        $this->dbconfig = null;
		}
		
		public function get($key){
			$sql = "SELECT key,value,description,created_at,created_by,Updated_at,Updated_by
					FROM config
					WHERE key = :key LIMIT 1;";
				
			$stmt = $this->dbconfig->prepare($sql);		
			$stmt->bindParam(':key', $key, PDO::PARAM_STR);

			if ($stmt->execute()) {	
				$result = $stmt->fetch();
    	        if ($result && count($result)){
					$data = $result['value'];
		        } else {
        			$data = "";
	    	    }  	   	
			} else {
				$data = "";
			}
			return $data;
			$this->dbconfig = null;
		}

    }    