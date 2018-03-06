<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes\modules;
use \classes\Auth as Auth;
use \classes\modules\Pages as Pages;
use \classes\Validation as Validation;
use \classes\CustomHandlers as CustomHandlers;
use PDO;
	/**
     * A class for pages management
     *
     * @package    reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class Pages {
        protected $db;

        //master var
		var $username,$token,$statusid,$apikey,$adminname;
		
		//data
        var $pageid,$title,$image,$description,$content,$tags,$search,$firstdate,$lastdate,$sort;

		// for pagination
		var $page,$itemsPerPage;

        function __construct($db=null) {
			if (!empty($db)) {
    	        $this->db = $db;
        	}
        }


        //PAGE=====================================


		/** 
		 * Add new page
		 * @return result process in json encoded data
		 */
        public function addPage(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $newusername = strtolower(filter_var($this->username,FILTER_SANITIZE_STRING));
			
    		    try {
    				$this->db->beginTransaction();
	    			$sql = "INSERT INTO data_page (Title,Image,Description,Content,Tags,StatusID,Created_at,Username) 
		    			VALUES (:title,:image,:description,:content,:tags,'51',current_timestamp,:username);";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
					$stmt->bindParam(':image', $this->image, PDO::PARAM_STR);
					$stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
					$stmt->bindParam(':content', $this->content, PDO::PARAM_STR);
                    $stmt->bindParam(':tags', $this->tags, PDO::PARAM_STR);
                    $stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
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
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }

			return json_encode($data);
			$this->db = null;

        }

		/** 
		 * Update data page
		 * @return result process in json encoded data
		 */
        public function updatePage(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $role = Auth::getRoleID($this->db,$this->token);
				if ($role == '1' || $role == '2'){
                    $newusername = strtolower(filter_var($this->username,FILTER_SANITIZE_STRING));
                    $newpageid = Validation::integerOnly($this->pageid);
                    $newstatusid = Validation::integerOnly($this->statusid);
                    
        			try {
	        			$this->db->beginTransaction();
                        $sql = "UPDATE data_page 
                            SET Title=:title,Image=:image,Description=:description,Content=:content,Tags=:tags,
                                StatusID=:status,Updated_by=:username,
                                Updated_at=current_timestamp
		        		    WHERE PageID=:pageid;";

				    $stmt = $this->db->prepare($sql);
					$stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
					$stmt->bindParam(':image', $this->image, PDO::PARAM_STR);
					$stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
                    $stmt->bindParam(':content', $this->content, PDO::PARAM_STR);
                    $stmt->bindParam(':tags', $this->tags, PDO::PARAM_STR);
                    $stmt->bindParam(':status', $newstatusid, PDO::PARAM_STR);
					$stmt->bindParam(':pageid', $newpageid, PDO::PARAM_STR);
					$stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
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
						'code' => 'RS404',
	        	    	'message' => CustomHandlers::getreSlimMessage('RS404')
					];
            	}
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }

			return json_encode($data);
			$this->db = null;

        }

        /** 
		 * Update data view page
		 * @return result process in json encoded data
		 */
        public function updateViewPage(){
            $newpageid = Validation::integerOnly($this->pageid);
                    
        		try {
					$this->db->beginTransaction();
					$sql = "UPDATE data_page a SET a.Viewer=a.Viewer+1 where a.PageID=:pageid;";
					$stmt = $this->db->prepare($sql);		
					$stmt->bindParam(':pageid', $newpageid, PDO::PARAM_STR);
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
	    		    $this->db->commit();
		        } catch (PDOException $e) {
		    	    $data = [
    			    	'status' => 'error',
	    				'code' => $e->getCode(),
		    		    'message' => $e->getMessage()
    		    	];
	    		    $this->db->rollBack();
        		} 

			return json_encode($data);
			$this->db = null;

        }

		/** 
		 * Delete data page
		 * @return result process in json encoded data
		 */
        public function deletePage(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $role = Auth::getRoleID($this->db,$this->token);
                if ($role == '1' || $role == '2'){
                    $newpageid = Validation::integerOnly($this->pageid);
                    $newusername = strtolower(filter_var($this->username,FILTER_SANITIZE_STRING));
			
    			    try {
                        $this->db->beginTransaction();
                        if ($role == '1') {
                            $sql = "DELETE FROM data_page 
    		    	    		WHERE PageID=:pageid;";
	    		    		$stmt = $this->db->prepare($sql);
                            $stmt->bindParam(':pageid', $newpageid, PDO::PARAM_STR);
                        } else {
                            $sql = "DELETE FROM data_page 
    		    	    		WHERE PageID=:pageid AND Username=:username;";
	    		    		$stmt = $this->db->prepare($sql);
                            $stmt->bindParam(':pageid', $newpageid, PDO::PARAM_STR);
                            $stmt->bindParam(':username', $newusername, PDO::PARAM_STR);
                        }
	    	    		
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
	    				'code' => 'RS404',
            	    	'message' => CustomHandlers::getreSlimMessage('RS404')
			    	];
                }
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
            }
            
			return json_encode($data);
			$this->db = null;

		}

		/** 
		 * Show data page only single detail for registered user
		 * @return result process in json encoded data
		 */
		public function showSinglePage(){
			if (Auth::validToken($this->db,$this->token,$this->username)){
	            $newpageid = Validation::integerOnly($this->pageid);
				
				$sql = "SELECT a.PageID,a.Created_at,a.Title,a.Image,a.Description,a.Content,a.Tags,a.Viewer,a.Username,
									a.Updated_at,a.Updated_by,a.Last_updated,a.StatusID,b.`Status`
								from data_page a
								inner join core_status b on a.StatusID=b.StatusID
								where a.PageID = :pageid LIMIT 1;";
				
				$stmt = $this->db->prepare($sql);		
				$stmt->bindParam(':pageid', $newpageid, PDO::PARAM_STR);

				if ($stmt->execute()) {	
    	    	    if ($stmt->rowCount() > 0){
        	   		   	$datares = "[";
								while($redata = $stmt->fetch()) 
								{
									//Start Tags
									$return_arr = null;
									$names = $redata['Tags'];	
									$named = preg_split( "/[,]/", $names );
									foreach($named as $name){
										if ($name != null){$return_arr[] = trim($name);}
									}
									//End Tags

									$datares .= '{"PageID":'.json_encode($redata['PageID']).',
											"Title":'.json_encode($redata['Title']).',
											"Image":'.json_encode($redata['Image']).',
											"Description":'.json_encode($redata['Description']).',
											"Content":'.json_encode($redata['Content']).',
											"Tags_inline":'.json_encode($redata['Tags']).',
											"Tags":'.json_encode($return_arr).',
											"Viewer":'.json_encode($redata['Viewer']).',
											"Created_at":'.json_encode($redata['Created_at']).',
											"Username":'.json_encode($redata['Username']).',
											"Updated_at":'.json_encode($redata['Updated_at']).',
											"Updated_by":'.json_encode($redata['Updated_by']).',
											"StatusID":'.json_encode($redata['StatusID']).',
											"Status":'.json_encode($redata['Status']).'},';
								}
								$datares = substr($datares, 0, -1);
								$datares .= "]";
						$data = [
			   	            'result' => json_decode($datares), 
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
			
			return json_encode($data);
	        $this->db= null;
		}
		
		/** 
		 * Show data page only single detail for guest without login
		 * @return result process in json encoded data
		 */
		public function showSinglePagePublic(){
            $newpageid = Validation::integerOnly($this->pageid);
				
				$sql = "SELECT a.PageID,a.Created_at,a.Title,a.Image,a.Description,a.Content,a.Tags,a.Viewer,a.Username as 'User',
									a.Updated_at,a.Updated_by,a.Last_updated,a.StatusID,b.`Status`
								from data_page a
								inner join core_status b on a.StatusID=b.StatusID
								where a.StatusID = '51' and a.PageID = :pageid LIMIT 1;";
				
				$stmt = $this->db->prepare($sql);		
				$stmt->bindParam(':pageid', $newpageid, PDO::PARAM_STR);

				if ($stmt->execute()) {	
    	    	    if ($stmt->rowCount() > 0){
        	   		   	$datares = "[";
								while($redata = $stmt->fetch()) 
								{
									//Start Tags
									$return_arr = null;
									$names = $redata['Tags'];	
									$named = preg_split( "/[,]/", $names );
									foreach($named as $name){
										if ($name != null){$return_arr[] = trim($name);}
									}
									//End Tags

									$datares .= '{"PageID":'.json_encode($redata['PageID']).',
											"Title":'.json_encode($redata['Title']).',
											"Image":'.json_encode($redata['Image']).',
											"Description":'.json_encode($redata['Description']).',
											"Content":'.json_encode($redata['Content']).',
											"Tags_inline":'.json_encode($redata['Tags']).',
											"Tags":'.json_encode($return_arr).',
											"Viewer":'.json_encode($redata['Viewer']).',
											"Created_at":'.json_encode($redata['Created_at']).',
											"User":'.json_encode($redata['User']).',
											"Updated_at":'.json_encode($redata['Updated_at']).',
											"Updated_by":'.json_encode($redata['Updated_by']).',
											"StatusID":'.json_encode($redata['StatusID']).',
											"Status":'.json_encode($redata['Status']).'},';
								}
								$datares = substr($datares, 0, -1);
								$datares .= "]";
						$data = [
			   	            'result' => json_decode($datares), 
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
        
			return json_encode($data);
	        $this->db= null;
		}

        /** 
		 * Search all data page paginated
		 * @return result process in json encoded data
		 */
		public function searchPageAsPagination() {
			if (Auth::validToken($this->db,$this->token,$this->username)){
                $role = Auth::getRoleID($this->db,$this->token);
                if ($role == '1' || $role == '2'){
					$search = "%$this->search%";
					$sqlcountrow = "SELECT count(a.PageID) as TotalRow
							from data_page a
							inner join core_status b on a.StatusID=b.StatusID
							where a.PageID like :search
							or a.Title like :search
							or a.Tags like :search
							or a.Username like :search
							or b.Status like :search
							order by a.Created_at desc;";
						$stmt = $this->db->prepare($sqlcountrow);
						$stmt->bindValue(':search', $search, PDO::PARAM_STR);

					if ($stmt->execute()) {	
    	    	    	if ($stmt->rowCount() > 0){
							$single = $stmt->fetch();
						
							// Paginate won't work if page and items per page is negative.
							// So make sure that page and items per page is always return minimum zero number.
							$newpage = Validation::integerOnly($this->page);
							$newitemsperpage = Validation::integerOnly($this->itemsPerPage);
							$limits = (((($newpage-1)*$newitemsperpage) <= 0)?0:(($newpage-1)*$newitemsperpage));
							$offsets = (($newitemsperpage <= 0)?0:$newitemsperpage);

							
								// Query Data
							$sql = "SELECT a.PageID,a.Created_at,a.Title,a.Image,a.Description,a.Content,a.Tags,a.Viewer,a.Username,
									a.Updated_at,a.Updated_by,a.Last_updated,a.StatusID,b.`Status`
								from data_page a
								inner join core_status b on a.StatusID=b.StatusID
								where a.PageID like :search
								or a.Title like :search
								or a.Tags like :search
								or a.Username like :search
								or b.Status like :search
								order by a.Created_at desc LIMIT :limpage , :offpage;";
							$stmt2 = $this->db->prepare($sql);
							$stmt2->bindValue(':search', $search, PDO::PARAM_STR);
							$stmt2->bindValue(':limpage', (INT) $limits, PDO::PARAM_INT);
							$stmt2->bindValue(':offpage', (INT) $offsets, PDO::PARAM_INT);
						
							if ($stmt2->execute()){
								if ($stmt2->rowCount() > 0){
									$results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
									$pagination = new \classes\Pagination();
									$pagination->totalRow = $single['TotalRow'];
									$pagination->page = $this->page;
									$pagination->itemsPerPage = $this->itemsPerPage;
									$pagination->fetchAllAssoc = $results;
									$data = $pagination->toDataArray();
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
						'code' => 'RS404',
	        	    	'message' => CustomHandlers::getreSlimMessage('RS404')
					];
				}	
			} else {
				$data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
			}		
        
			return json_encode($data);
	        $this->db= null;
        }

        /** 
		 * Search all data page paginated public
		 * @return result process in json encoded data
		 */
		public function searchPageAsPaginationPublic() {
			$search = "%$this->search%";
			$sqlcountrow = "SELECT count(a.PageID) as TotalRow
					from data_page a
					inner join core_status b on a.StatusID=b.StatusID
					where a.StatusID='51' and a.PageID like :search
					or a.StatusID='51' and a.Title like :search
					or a.StatusID='51' and a.Tags like :search
					or a.StatusID='51' and a.Username like :search
					or a.StatusID='51' and b.Status like :search
					order by a.Created_at desc;";
				$stmt = $this->db->prepare($sqlcountrow);
				$stmt->bindValue(':search', $search, PDO::PARAM_STR);

			if ($stmt->execute()) {	
    	    	if ($stmt->rowCount() > 0){
					$single = $stmt->fetch();
						
					// Paginate won't work if page and items per page is negative.
					// So make sure that page and items per page is always return minimum zero number.
					$newpage = Validation::integerOnly($this->page);
					$newitemsperpage = Validation::integerOnly($this->itemsPerPage);
					$limits = (((($newpage-1)*$newitemsperpage) <= 0)?0:(($newpage-1)*$newitemsperpage));
					$offsets = (($newitemsperpage <= 0)?0:$newitemsperpage);

							
					// Query Data
					$sql = "SELECT a.PageID,a.Created_at,a.Title,a.Image,a.Description,a.Tags,a.Viewer,a.Username as 'User',
							a.Updated_at,a.Updated_by,a.StatusID,b.`Status`
						from data_page a
						inner join core_status b on a.StatusID=b.StatusID
						where a.StatusID='51' and a.PageID like :search
						or a.StatusID='51' and a.Title like :search
						or a.StatusID='51' and a.Tags like :search
						or a.StatusID='51' and a.Username like :search
						or a.StatusID='51' and b.Status like :search
						order by a.Created_at desc LIMIT :limpage , :offpage;";
					$stmt2 = $this->db->prepare($sql);
					$stmt2->bindValue(':search', $search, PDO::PARAM_STR);
					$stmt2->bindValue(':limpage', (INT) $limits, PDO::PARAM_INT);
					$stmt2->bindValue(':offpage', (INT) $offsets, PDO::PARAM_INT);
						
					if ($stmt2->execute()){
						if ($stmt2->rowCount() > 0){
                            $datares = "[";
					        while($redata = $stmt2->fetch()) {
        					    //Start Tags
								$return_arr = null;
								$names = $redata['Tags'];	
								$named = preg_split( "/[,]/", $names );
								foreach($named as $name){
									if ($name != null){$return_arr[] = trim($name);}
								}
                                //End Tags
                                
                                $datares .= '{"PageID":'.json_encode($redata['PageID']).',
									"Title":'.json_encode($redata['Title']).',
									"Image":'.json_encode($redata['Image']).',
									"Description":'.json_encode($redata['Description']).',
                                    "Tags_Inline":'.json_encode($redata['Tags']).',
                                    "Tags":'.json_encode($return_arr).',
                                    "Viewer":'.json_encode($redata['Viewer']).',
                                    "Created_at":'.json_encode($redata['Created_at']).',
                                    "User":'.json_encode($redata['User']).',
                                    "Updated_at":'.json_encode($redata['Updated_at']).',
                                    "Updated_by":'.json_encode($redata['Updated_by']).',
                                    "StatusID":'.json_encode($redata['StatusID']).',
                                    "Status":'.json_encode($redata['Status']).'},';
                            }
                            $datares = substr($datares, 0, -1);
                            $datares .= "]";
                            $pagination = new \classes\Pagination();
                            $pagination->totalRow = $single['TotalRow'];
                            $pagination->page = $this->page;
                            $pagination->itemsPerPage = $this->itemsPerPage;
                            $pagination->fetchAllAssoc = json_decode($datares);
                            $data = $pagination->toDataArray();
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
        
			return json_encode($data);
	        $this->db= null;
        }

        /** 
		 * Show all data published page paginated public
		 * @return result process in json encoded data
		 */
		public function showPublishPageAsPaginationPublic() {
            if (strtolower($this->sort) != 'asc'){
                $sort = 'desc';
            } else {
                $sort = $this->sort;
            }
			$sqlcountrow = "SELECT count(a.PageID) as TotalRow
					from data_page a
					inner join core_status b on a.StatusID=b.StatusID
					where a.StatusID='51'
					order by a.Created_at $sort;";
				$stmt = $this->db->prepare($sqlcountrow);

			if ($stmt->execute()) {	
    	    	if ($stmt->rowCount() > 0){
					$single = $stmt->fetch();
						
					// Paginate won't work if page and items per page is negative.
					// So make sure that page and items per page is always return minimum zero number.
					$newpage = Validation::integerOnly($this->page);
					$newitemsperpage = Validation::integerOnly($this->itemsPerPage);
					$limits = (((($newpage-1)*$newitemsperpage) <= 0)?0:(($newpage-1)*$newitemsperpage));
					$offsets = (($newitemsperpage <= 0)?0:$newitemsperpage);

					// Query Data
					$sql = "SELECT a.PageID,a.Created_at,a.Title,a.Image,a.Description,a.Tags,a.Viewer,a.Username as 'User',
							a.Updated_at,a.Updated_by,a.StatusID,b.`Status`
						from data_page a
						inner join core_status b on a.StatusID=b.StatusID
						where a.StatusID='51' 
						order by a.Created_at $sort LIMIT :limpage , :offpage;";
					$stmt2 = $this->db->prepare($sql);
					$stmt2->bindValue(':limpage', (INT) $limits, PDO::PARAM_INT);
					$stmt2->bindValue(':offpage', (INT) $offsets, PDO::PARAM_INT);
						
					if ($stmt2->execute()){
						if ($stmt2->rowCount() > 0){
                            $datares = "[";
					        while($redata = $stmt2->fetch()) {
        					    //Start Tags
								$return_arr = null;
								$names = $redata['Tags'];	
								$named = preg_split( "/[,]/", $names );
								foreach($named as $name){
									if ($name != null){$return_arr[] = trim($name);}
								}
                                //End Tags
                                
                                $datares .= '{"PageID":'.json_encode($redata['PageID']).',
									"Title":'.json_encode($redata['Title']).',
									"Image":'.json_encode($redata['Image']).',
									"Description":'.json_encode($redata['Description']).',
                                    "Tags_Inline":'.json_encode($redata['Tags']).',
                                    "Tags":'.json_encode($return_arr).',
                                    "Viewer":'.json_encode($redata['Viewer']).',
                                    "Created_at":'.json_encode($redata['Created_at']).',
                                    "User":'.json_encode($redata['User']).',
                                    "Updated_at":'.json_encode($redata['Updated_at']).',
                                    "Updated_by":'.json_encode($redata['Updated_by']).',
                                    "StatusID":'.json_encode($redata['StatusID']).',
                                    "Status":'.json_encode($redata['Status']).'},';
                            }
                            $datares = substr($datares, 0, -1);
                            $datares .= "]";
                            $pagination = new \classes\Pagination();
                            $pagination->totalRow = $single['TotalRow'];
                            $pagination->page = $this->page;
                            $pagination->itemsPerPage = $this->itemsPerPage;
                            $pagination->fetchAllAssoc = json_decode($datares);
                            $data = $pagination->toDataArray();
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
        
			return json_encode($data);
	        $this->db= null;
        }

        
        //STATUS=======================================


		/** 
		 * Get all data Status for Release
		 * @return result process in json encoded data
		 */
		public function showOptionRelease() {
			if (Auth::validToken($this->db,$this->token)){
				$sql = "SELECT a.StatusID,a.Status
					FROM core_status a
					WHERE a.StatusID = '51' OR a.StatusID = '52'
					ORDER BY a.Status ASC";
				
				$stmt = $this->db->prepare($sql);		
				$stmt->bindParam(':token', $this->token, PDO::PARAM_STR);

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
			} else {
				$data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401')
				];
			}		
        
			return json_encode($data);
	        $this->db= null;
		}

		/** 
		 * Get data statistic page
		 * @return result process in json encoded data
		 */
		public function statPageSummary() {
			if (Auth::validToken($this->db,$this->token)){
				$sql = "SELECT 
					(SELECT count(x.PageID) FROM data_page x WHERE x.StatusID='51') AS 'Publish',
					(SELECT count(x.PageID) FROM data_page x WHERE x.StatusID='52') AS 'Draft',
					(SELECT sum(x.Viewer) FROM data_page x) AS 'Viewer',
					(SELECT count(x.PageID) FROM data_page x) AS 'Total',
					round((((SELECT Total) - (SELECT Draft))/(SELECT Total))*100) AS 'Percent_Up',
					(100 - (SELECT Percent_Up)) AS 'Precent_Down'
				FROM data_page a LIMIT 1;";
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
					'code' => 'RS404',
	        	    'message' => CustomHandlers::getreSlimMessage('RS404')
				];
			}
			
        
			return json_encode($data);
	        $this->db= null;
		}
    }