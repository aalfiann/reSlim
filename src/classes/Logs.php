<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\JSON as JSON;
use PDO;
    /**
     * A class for handle log in reSlim
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2017 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
	class Logs {
        protected $db;

        var $username,$token,$content;

        var $filename = '../logs/app.log';

        function __construct($db=null) {
			if (!empty($db)) 
	        {
    	        $this->db = $db;
        	}
        }
        
        /** 
		 * Update Logs
		 * @return result process in json encoded data
		 */
        public function updateLog(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                if (is_writable($this->filename)) {
                    $fp = fopen($this->filename, 'w');
                    if(fwrite($fp, $this->content) == TRUE){
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
                    fclose($fp);
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 'RS201',
                        'message' => CustomHandlers::getreSlimMessage('RS201')
                    ];    
                }
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 'RS401',
                    'message' => CustomHandlers::getreSlimMessage('RS401')
                ];
            }
            return JSON::encode($data);
        }

        /** 
		 * Clear Logs
		 * @return result process in json encoded data
		 */
        public function clearLog(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                if (is_writable($this->filename)) {
                    $fp = fopen($this->filename, 'w');
                    fwrite($fp, '');
                    $data = [
                        'status' => 'success',
                        'code' => 'RS103',
                        'message' => CustomHandlers::getreSlimMessage('RS103')
                    ];
                    fclose($fp);
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 'RS201',
                        'message' => CustomHandlers::getreSlimMessage('RS201')
                    ];    
                }
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 'RS401',
                    'message' => CustomHandlers::getreSlimMessage('RS401')
                ];
            }
            return JSON::encode($data);
        }

    }