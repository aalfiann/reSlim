<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\Upload as Upload;
use \classes\Validation as Validation;
use PDO;
    /**
     * A class for user upload file in reSlim
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
	class Upload {
		
		protected $db;
        
        var $username,$datafile,$token,$itemid,$baseurl,$title,$alternate,$externallink,$status,$apikey,$filename;

        // limit size upload
        var $maxUploadSize = '100000000';

        // for pagination
		var $page,$itemsPerPage;

		// for search
		var $search;

        function __construct($db=null) {
			if (!empty($db)) 
	        {
    	        $this->db = $db;
        	}
		}

		/**
		 * Check file on the server is already exist or not
		 *
         * @param $url is full path filename on the server. Example: http://www.example.com/api/upload/2017/tester.txt
         * @return boolean true|false
         */
		function isFileOnServer($url){
			$result = false;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_exec($ch);
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			// $retcode >= 400 -> not found, $retcode = 200, found.
			curl_close($ch);
			if ($retcode==200){
				$result = true;
			}
			return $result;
		}

		/**
		 * Check file is allowed or not
		 *
         * @param $filename is the filename with extension in the end. Example: tester.txt
         * @return boolean true|false
         */
		function isFileNotAllowed($fileName){
			$result = false;
			$notAllowedExts = array("php","sql","sqlite3","db","dbf","js","json","xml","html");
			$temp = explode(".", $fileName);
			$extension = end($temp);

			if (in_array($extension, $notAllowedExts)) {
		    	$result = true;
			}
			return $result;
		}

		/** 
		 * Process upload to server
		 * @return result process in json encoded data
		 */
        function doUpload()
        {
    	    //Auto create subfolder upload in every month
            $formatFolder = date('m-Y');
            $fileFolder = 'upload/'.$formatFolder.'/';
			
	        if (!is_dir($fileFolder)) 
        	{
				$newcontent = '<?php header(\'Content-type:application/json; charset=utf-8\');header("Access-Control-Allow-Origin: *");header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");header(\'HTTP/1.0 403 Forbidden\');echo \'{
  "status": "error",
  "code": "403",
  "message": "This page is forbidden."
}\';?>';
				$newprotection = '<Files ~ "\.(php|pdf|js|sql|sqlite3|doc|xls|db|dbf|json|xml|html)$">
   Order allow,deny
   Deny from all
</Files>';
            	mkdir($fileFolder,0775,true);
				if(!$this->isFileOnServer($this->baseurl.'/upload/index.php')){
					$ihandle = fopen('upload/index.php','w+'); 
					fwrite($ihandle,$newcontent); 
					fclose($ihandle);
				}
				$handle = fopen($fileFolder.'index.php','w+'); 
				fwrite($handle,$newcontent); 
				fclose($handle);
				$xhandle = fopen($fileFolder.'.htaccess','w+'); 
				fwrite($xhandle,$newprotection); 
				fclose($xhandle);       
        	}

            $file = $this->datafile;
			
        	// determine filepath 
        	$filePath = $fileFolder.$file->getClientFilename();
        	// determine filename
        	$fileName = $file->getClientFilename();
        	// determine filetype
        	$fileType = $file->getClientMediaType();
        	// determine filesize
        	$fileSize = $file->getSize();
        	// determine error
        	$fileError = $file->getError();

			//Determine if file is not allowed
			if (!$this->isFileNotAllowed($fileName)){
				//Determine if file already exist
				if(!$this->isFileOnServer($this->baseurl.'/'.$filePath)) {
	 			   	// check if file size is allowed
					if ($fileSize <= $this->maxUploadSize){
						//Check proses upload status
						if ($file->getError() === UPLOAD_ERR_OK){
							$uploadresult = $file->moveTo($fileFolder.$fileName);
							if ($uploadresult == null){
								$newusername = strtolower($this->username);

    	                	    try{
        	                	    $this->db->beginTransaction();
            	                	$sql = "INSERT INTO user_upload (Date_Upload,Filename,Filepath,Filetype,Filesize,Username,StatusID,Title,Alternate,External_link) 
					        			VALUES(current_timestamp,:filename,:filepath,:filetype,:filesize,:username,'49',:title,:alternate,:externallink);";
    	            	            $stmt = $this->db->prepare($sql);
					    	        $stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
            	            	    $stmt->bindparam(':filename', $fileName, PDO::PARAM_STR);
    					        	$stmt->bindparam(':filepath', $filePath, PDO::PARAM_STR);
	        						$stmt->bindparam(':filetype', $fileType, PDO::PARAM_STR);
			        				$stmt->bindparam(':filesize', $fileSize, PDO::PARAM_STR);
									$stmt->bindparam(':title', $this->title, PDO::PARAM_STR);
									$stmt->bindparam(':alternate', $this->alternate, PDO::PARAM_STR);
									$stmt->bindparam(':externallink', $this->externallink, PDO::PARAM_STR);
                    	        	if ($stmt->execute()) {
					    	        	$data = [
            								'status' => 'success',
	            							'code' => 'RS101',
    	    								'message' => CustomHandlers::getreSlimMessage('RS101'),
											'datafile' => [ 'Title' => $this->title,
												'Alternate' => $this->alternate,
												'External_link' => $this->externallink,
												'Filename' => $fileName,
												'Filepath' => $this->baseurl.'/'.$filePath,
												'Filetype' => $fileType,
												'Filesize' => $fileSize]
	        							];	
			            			} else {
	    	        					$data = [
    	    	    						'status' => 'error',
					            			'code' => 'RS909',
							            	'message' => CustomHandlers::getreSlimMessage('RS909')
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
							} else {
								$data = [
        	    	    			'status' => 'error',
						        	'code' => '0',
                					'message' => $uploadresult
            					];
							}
					    } else {
    	    	    		$data = [
        	    				'status' => 'error',
				    	    	'code' => 'RS910',
			    	    		'message' => CustomHandlers::getreSlimMessage('RS910')
	            			];
				        }
					}else{
						$data = [
            				'status' => 'error',
			    	    	'code' => 'RS911',
				        	'message' => CustomHandlers::getreSlimMessage('RS911')
    	        		];
					}
				} else {
					$data = [
        				'status' => 'error',
		    	    	'code' => 'RS912',
			    		'message' => CustomHandlers::getreSlimMessage('RS912')
	            	];
				}
			} else {
				$data = [
	        		'status' => 'error',
		        	'code' => 'RS908',
			    	'message' => CustomHandlers::getreSlimMessage('RS908')
        		];
			}
			

        	return $data;
			$this->db = null;
        }

		/** 
		 * Get all data Status User Upload
		 * @return result process in json encoded data
		 */
		public function showOptionStatus() {
			if (Auth::validToken($this->db,$this->token)){
				$sql = "SELECT a.StatusID,a.Status
					FROM core_status a
					WHERE a.StatusID = '49' OR a.StatusID = '50'
					ORDER BY a.Status ASC";
				
				$stmt = $this->db->prepare($sql);		
				$stmt->bindParam(':token', $this->token, PDO::PARAM_STR);

				if ($stmt->execute()) {	
    	    	    if ($stmt->rowCount() > 0){
        	   		   	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
						$data = [
			   	            'result' => $results, 
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
		 * Process Upload and verify user
		 * @return result process in json encoded data
		 */
		public function process(){
			if (Auth::validToken($this->db,$this->token,$this->username)){
					$data = $this->doUpload();
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
		 * Get all data user upload
		 * @return result process in json encoded data
		 */
		public function showAllAsPagination() {
			$newusername = strtolower($this->username);
			if (Auth::validToken($this->db,$this->token)){
				//count total row
				$sqlcountrow = "SELECT count(a.ItemID) as TotalRow 
					from user_upload a 
					where a.StatusID = '49' or a.Username=:username;";
				$stmt = $this->db->prepare($sqlcountrow);		
				$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
				
				if ($stmt->execute()) {	
    	    		if ($stmt->rowCount() > 0){
						$single = $stmt->fetch();
						$base = $this->baseurl."/";
						// Paginate won't work if page and items per page is negative.
						// So make sure that page and items per page is always return minimum zero number.
						$newpage = Validation::integerOnly($this->page);
						$newitemsperpage = Validation::integerOnly($this->itemsPerPage);
						$limits = (((($newpage-1)*$newitemsperpage) <= 0)?0:(($newpage-1)*$newitemsperpage));
						$offsets = (($newitemsperpage <= 0)?0:$newitemsperpage);

							// Query Data
							$sql = "SELECT a.ItemID,a.Date_Upload,a.Title,a.Alternate,a.External_link,a.Filename,a.Filepath,concat(:baseurl,a.Filepath) as Fullpath,a.Filetype,a.Filesize,a.Username as 'Upload_by',a.Updated_at,a.Updated_by,a.StatusID,b.`Status` 
								from user_upload a 
								inner join core_status b on a.StatusID=b.StatusID
								where a.StatusID = '49' or a.Username=:username
								order by a.Date_Upload desc LIMIT :limpage , :offpage;";
								$stmt2 = $this->db->prepare($sql);
								$stmt2->bindParam(':baseurl', $base, PDO::PARAM_STR);
								$stmt2->bindParam(':username', $newusername, PDO::PARAM_STR);
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
        
			return json_encode($data, JSON_PRETTY_PRINT);
	        $this->db= null;
		}

		/** 
		 * Search all data user paginated
		 * @return result process in json encoded data
		 */
		public function searchAllAsPagination() {
			if (Auth::validToken($this->db,$this->token)){
				$newusername = strtolower($this->username);
				$search = "%$this->search%";
				//count total row
				$sqlcountrow = "SELECT count(a.ItemID) as TotalRow 
					from user_upload a 
					where a.StatusID = '49' and a.Filename like :search 
					or a.Username=:username and a.Filename like :search
					or a.StatusID = '49' and a.Title like :search
					or a.Username=:username and a.Title like :search;";
				$stmt = $this->db->prepare($sqlcountrow);		
				$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
				$stmt->bindParam(':search', $search, PDO::PARAM_STR);
				
				if ($stmt->execute()) {	
    	    		if ($stmt->rowCount() > 0){
						$single = $stmt->fetch();
						$base = $this->baseurl."/";
						// Paginate won't work if page and items per page is negative.
						// So make sure that page and items per page is always return minimum zero number.
						$newpage = Validation::integerOnly($this->page);
						$newitemsperpage = Validation::integerOnly($this->itemsPerPage);
						$limits = (((($newpage-1)*$newitemsperpage) <= 0)?0:(($newpage-1)*$newitemsperpage));
						$offsets = (($newitemsperpage <= 0)?0:$newitemsperpage);

							// Query Data
							$sql = "SELECT a.ItemID,a.Date_Upload,a.Title,a.Alternate,a.External_link,a.Filename,a.Filepath,concat(:baseurl,a.Filepath) as Fullpath,a.Filetype,a.Filesize,a.Username as 'Upload_by',a.Updated_at,a.Updated_by,a.StatusID,b.`Status` 
								from user_upload a 
								inner join core_status b on a.StatusID=b.StatusID
								where a.StatusID = '49' and a.Filename like :search 
								or a.Username=:username and a.Filename like :search
								or a.StatusID = '49' and a.Title like :search
								or a.Username=:username and a.Title like :search
								order by a.Date_Upload desc LIMIT :limpage , :offpage;";
								$stmt2 = $this->db->prepare($sql);
								$stmt2->bindParam(':baseurl', $base, PDO::PARAM_STR);
								$stmt2->bindParam(':username', $newusername, PDO::PARAM_STR);
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
        
			return json_encode($data, JSON_PRETTY_PRINT);
	        $this->db= null;
		}

		/** 
		 * Get data single user upload by Item ID
		 * @return result process in json encoded data
		 */
		public function showItem() {
			$sql = "SELECT a.ItemID,a.Date_Upload,a.Title,a.Alternate,a.External_link,a.Filename,a.Filepath,a.Filetype,a.Filesize,a.Username as 'Upload_by',a.Updated_at,a.Updated_by,b.`Status` 
				from user_upload a 
				inner join core_status b on a.StatusID=b.StatusID
				where a.StatusID = '49' and a.ItemID=:itemid or a.Username=:username and a.ItemID=:itemid;";
			$newusername = strtolower($this->username);
			$stmt = $this->db->prepare($sql);		
			$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
			$stmt->bindParam(':itemid', $this->itemid, PDO::PARAM_STR);

			if ($stmt->execute()) {	
    			if ($stmt->rowCount() > 0){
        			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$data = [
		   	        	'result' => $results, 
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
        
			return json_encode($data, JSON_PRETTY_PRINT);
	        $this->db= null;
		}

		/**
		 * Update user upload item
		 * @return result process in json encoded data
		 */
		function doUpdate(){
			try {
				$this->db->beginTransaction();
				if (Auth::getRoleID($this->db,$this->token) == '1'){
					$sql = "UPDATE user_upload 
					SET Title=:title,Alternate=:alternate,External_link=:external,StatusID=:status,Updated_by=:username 
					WHERE ItemID=:itemid;";
				} else if (Auth::getRoleID($this->db,$this->token) == '2'){
					$sql = "UPDATE user_upload 
					SET Title=:title,Alternate=:alternate,External_link=:external,StatusID=:status,Updated_by=:username 
					WHERE ItemID=:itemid;";
				} else {
					$sql = "UPDATE user_upload 
					SET Title=:title,Alternate=:alternate,External_link=:external,StatusID=:status,Updated_by=:username 
					WHERE ItemID=:itemid and Username=:username;";
				}
					$newusername = strtolower($this->username);
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
					$stmt->bindParam(':alternate', $this->alternate, PDO::PARAM_STR);
					$stmt->bindParam(':external', $this->externallink, PDO::PARAM_STR);
					$stmt->bindParam(':itemid', $this->itemid, PDO::PARAM_STR);
					$stmt->bindParam(':status', $this->status, PDO::PARAM_STR);
					$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
					if ($stmt->execute()) {
						if ($stmt->rowCount() > 0){
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
		 * Delete user upload item
		 * @return result process in json encoded data
		 */
		function doDelete(){
			$sqldata = "SELECT a.Filepath
			    FROM user_upload a 
    			WHERE a.ItemID = :itemid;";
	    	$stmt = $this->db->prepare($sqldata);
		    $stmt->bindParam(':itemid', $this->itemid, PDO::PARAM_STR);
    		if ($stmt->execute()) {	
                if ($stmt->rowCount() > 0){
                    $single = $stmt->fetch();
					$filepath = $single['Filepath'];
					try {
						$this->db->beginTransaction();
						if (Auth::getRoleID($this->db,$this->token) == '1'){
							$sql = "DELETE from user_upload  
								WHERE ItemID=:itemid;";
							$newusername = strtolower($this->username);
							$stmt2 = $this->db->prepare($sql);
							$stmt2->bindParam(':itemid', $this->itemid, PDO::PARAM_STR);
						} else {
							$sql = "DELETE from user_upload  
								WHERE ItemID=:itemid and Username=:username;";
							$newusername = strtolower($this->username);
							$stmt2 = $this->db->prepare($sql);
							$stmt2->bindParam(':itemid', $this->itemid, PDO::PARAM_STR);
							$stmt2->bindParam(':username', $newusername, PDO::PARAM_STR);
						}
						
						if ($stmt2->execute()) {
							if ($stmt2->rowCount() > 0){
								if (file_exists($filepath)){
									if(unlink($filepath)){
										$data = [
											'status' => 'success',
											'code' => 'RS104',
											'message' => CustomHandlers::getreSlimMessage('RS104')
										];
									} else {
										$data = [
											'status' => 'error',
											'code' => 'RS913',
											'message' => CustomHandlers::getreSlimMessage('RS913')
										];
									}
								} else {
									$data = [
											'status' => 'success',
											'code' => 'RS104',
											'message' => CustomHandlers::getreSlimMessage('RS104')
										];
								}
							} else {
								$data = [
									'status' => 'error',
									'code' => 'RS204',
									'message' => CustomHandlers::getreSlimMessage('RS204')
								];
							}	
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
                } else {
					$data = [
						'status' => 'error',
						'code' => 'RS601',
						'message' => CustomHandlers::getreSlimMessage('RS601')
					];
				}          	   	
	    	} 	
			return $data;
			$this->db = null;
		}

		/** 
		 * Update user upload item
		 * @return result process in json encoded data
		 */
		public function update(){
			if (Auth::validToken($this->db,$this->token,$this->username)){
				$data = $this->doUpdate();
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
		 * Delete user upload item
		 * @return result process in json encoded data
		 */
		public function delete(){
			if (Auth::validToken($this->db,$this->token,$this->username)){
				$data = $this->doDelete();
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
		 * Determine is filename exist on our server
		 * @return string
		 */
		private function isFilenameInExplorer(){
			$r = false;
			$sql = "SELECT a.Filepath
				FROM user_upload a 
				WHERE a.Filename=:filename;";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':filename', $this->filename, PDO::PARAM_STR);
			if ($stmt->execute()) {	
            	if ($stmt->rowCount() > 0){
					$single = $stmt->fetch();
					$r = $single['Filepath'];
    	        }          	   	
			} 		
			return $r;
			$this->db = null;
		}

		/** 
		 * Force stream inline or attachment to protect from hotlinking
		 * @return result stream data or process in json encoded data
		 */
		public function forceStream($stream=true){
			if (Auth::validToken($this->db,$this->token)){
				$datapath = $this->isFilenameInExplorer();
				if ( $datapath != false){
					if ($stream == false){
						$disposition = 'attachment';
					} else {
						$disposition = 'inline';
					}
					$path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..').'/api/'.$datapath;
					$fp = fopen($path, "r") ;
					header('HTTP/1.0 200 OK');
					header('Cache-Control: public, must-revalidate, max-age=0');
					header('Pragma: no-cache');
					header('Accept-Ranges: bytes');
				    header('Content-Description: File Transfer');
					header('Content-Transfer-Encoding: binary');
    				header('Content-Disposition: '.$disposition.'; filename="'.$this->filename.'"');
		    		header('Content-length: '.filesize($path));
			    	header('Content-type: '.pathinfo($path, PATHINFO_EXTENSION));
					ob_clean();
					flush();
					while (!feof($fp)) {
						$buff = fread($fp, 1024);
						print $buff;
					}
					exit;
				} else {
					$data = [
		    			'status' => 'error',
						'code' => 'RS601',
        		    	'message' => CustomHandlers::getreSlimMessage('RS601')
					];
					return json_encode($data, JSON_PRETTY_PRINT);
				}
			} else {
				$data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
				return json_encode($data, JSON_PRETTY_PRINT);
			}
			$this->db= null;
			exit;
		}
    }