<?php 
/**
 * This class is a part of reSlim project for pagination data array
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use PDO;
	/**
     * A class for pagination data array
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class Pagination {
		/**
		 * @property $FetchAllAssoc : Is the result of query fetchAll(PDO::FETCH_ASSOC) as array 
		 *  
		 * This property is required or paginate won't work
		 */
        var $fetchAllAssoc = array();
        
		/**
		 * @property $totalRow 		: Set the total row of your data to be paginated
		 * @property $page 			: Set the page to be show in paginated data
		 * @property $itemsPerPage 	: Set the items per page to be show in paginated data
		 * @property $limitData 	: Set the limit data for paginate. Default is 1000.
		 *							  The paginate won't work if more than 1000.
		 *  
		 * This property is required or paginate won't work
		 */
		var $totalRow,$page,$itemsPerPage,$limitData=1000;

		/**
		 * Sanitizer string and only accept integer
		 *
		 * @var $string
		 *
		 * @return string
		 */
        private function integerOnly($string)
	    {
		    $nn = preg_replace("/[^0-9]/", "", $string );
    		return $nn;
	    }

		/**
		 * Determine request show data is big or not
		 *
		 * @return boolean true / false 
		 */
    	private function isBigData()
	    {
		    $result = false;
    		if($this->itemsPerPage > $this->limitData)
	    	{
		    	$result = true;
    		}
	    	return $result;
    	}

		/**
		 * Pagination data to array format
		 *
		 * @return string array
		 */
        public function toDataArray(){
            if (!empty($this->itemsPerPage) && !empty($this->page))
    		{
	    		//Check Big Data 
		    	if ($this->isBigData($this->itemsPerPage) == false)
			    {
				    //Convert integer
    				$itemsperpage = $this->integerOnly($this->itemsPerPage);
	    			$page = $this->integerOnly($this->page);

		    		//Hitung total page
			    	$totalpages = ceil($this->totalRow/$itemsperpage);
			
    				//Check parameter halaman
	    			if($page<=$totalpages && $page>0)
		    		{
			    		//Check data
				      	if (!empty($this->fetchAllAssoc))
					    {
                            $alldata = [
                                'results' => $this->fetchAllAssoc,
                                'status' => 'success',
                                'code' => 'RS501',
			    				'message' =>  CustomHandlers::getreSlimMessage('RS501'),
                                'metadata' => [
                                    'records_total' => (int)$this->totalRow,
                                    'records_count' => (int)count($this->fetchAllAssoc),
                                    'number_item_first' => (int)((($page-1)*$itemsperpage)+1),
                                    'number_item_last' => (int)((($page-1)*$itemsperpage)+count($this->fetchAllAssoc)),
                                    'items_per_page' => (int)$itemsperpage,
                                    'page_now' => (int)$page,
                                    'page_total' => (int)$totalpages
                                ]
                            ];
    					}
	    				else
		    			{
			    			$alldata = [
                                'status' => 'error',
                                'code' => 'RS601',
					    		'message' => CustomHandlers::getreSlimMessage('RS601')
                            ];
    					}
	    			}
		    		else
			    	{
				    	$alldata = [
                            'status' => 'error',
                            'code' => 'RS601',
						    'message' => CustomHandlers::getreSlimMessage('RS601')
                        ];
	    			}
		    	}
			    else
    			{
                    $alldata = [
                        'status' => 'error',
                        'code' => 'RS602',
			    		'message' => CustomHandlers::getreSlimMessage('RS602').' Max items per page : '.$this->LimitData.'.'
                    ];
	    		}
		    }
    		else
	    	{
		    	$alldata = [
                    'status' => 'error',
                    'code' => 'RS801',
				    'message' =>  CustomHandlers::getreSlimMessage('RS801')
                ];
	    	}
            return $alldata;
        }
    }