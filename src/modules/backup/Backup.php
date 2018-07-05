<?php
namespace modules\backup;                          //Make sure namespace is same structure with parent directory

use \classes\Auth as Auth;                          //For authentication internal user
use \classes\JSON as JSON;                          //For handling JSON in better way
use \classes\CustomHandlers as CustomHandlers;      //To get default response message
use PDO;                                            //To connect with database

	/**
     * For handle database backup in reSlim
     *
     * @package    modules/backup
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim-modules-backup/blob/master/LICENSE.md MIT License
     */
    class Backup {

        // database var
        protected $db;
        
        // base var
        protected $basepath,$baseurl,$basemod;

        //master var
        var $username,$token;
        
        //folder backup
        var $folderbackupdb = 'backup-db';

        //multi language
        var $lang;
        
        //construct database object
        function __construct($db=null) {
			if (!empty($db)) $this->db = $db;
            $this->baseurl = (($this->isHttps())?'https://':'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
            $this->basepath = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);
            $this->basemod = dirname(__FILE__);
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


        //BACKUP===========================================

        /* backup the db OR just a table */
        public function table($tables = '*'){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                return $this->makeDump($tables);
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401',$this->lang)
				];
            }
            return JSON::encode($data,true);
        }

        /* Show all backup files */
        public function showAllFiles(){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                $data = $this->scan_dir('backup-db');
                $data += [
	    			'status' => 'success',
					'code' => 'RS501',
        	    	'message' => CustomHandlers::getreSlimMessage('RS501',$this->lang)
				]; 
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401',$this->lang)
				];
            }
            return JSON::encode($data,true);
        }

        public function deleteFile($filename){
            if (Auth::validToken($this->db,$this->token,$this->username)){
                if (file_exists($this->folderbackupdb.'/'.$filename)){
                    if (unlink($this->folderbackupdb.'/'.$filename)){
                        $data = [
                            'status' => 'success',
                            'code' => 'RS104',
                            'message' => CustomHandlers::getreSlimMessage('RS104',$this->lang)
                        ];
                    } else {
                        $data = [
                            'status' => 'error',
                            'code' => 'RS204',
                            'message' => CustomHandlers::getreSlimMessage('RS204',$this->lang)
                        ]; 
                    }
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 'RS601',
                        'message' => CustomHandlers::getreSlimMessage('RS601',$this->lang)
                    ];
                }
            } else {
                $data = [
	    			'status' => 'error',
					'code' => 'RS401',
        	    	'message' => CustomHandlers::getreSlimMessage('RS401',$this->lang)
				];
            }
            return JSON::encode($data,true);
        }

        public function deleteAll($wildcard="*"){
            $wildcard = (empty($wildcard)?'*':$wildcard);
            if (file_exists($this->folderbackupdb)) {
                //Auto delete useless cache
                $files = glob($this->folderbackupdb.'/'.$wildcard,GLOB_NOSORT);
                $deleted = -1;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $deleted++;
                    }
                }
                $data = [
                    'status' => 'success',
                    'code' => 'RS104',
                    'message' => CustomHandlers::getreSlimMessage('RS104',$this->lang),
                    'total_deleted' => $deleted,
                    'execution_time' => (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"])
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 'RS601',
                    'message' => CustomHandlers::getreSlimMessage('RS601',$this->lang)
                ];
            }
            return JSON::encode($data,true);
        }

        private function makeDump($tables = '*'){
            $tablename = (($tables=="*")?'all':$tables);
            $data = "/*"."\n";
            $data .= "reSlim Backup"."\n";
            $data .= "File Encoding: UTF-8"."\n";
            $data .= "Date: ".date('Y-m-d H:i:s',time())."\n";
            $data .= "*/"."\n\n";
            $data .= "SET FOREIGN_KEY_CHECKS=0;"."\n\n";
            //get all of the tables
            if($tables == '*')
            {
                $tables = array();
                $result = $this->db->prepare('SHOW TABLES'); 
                $result->execute();                         
                while($row = $result->fetch(PDO::FETCH_NUM)) 
                { 
                    $tables[] = $row[0]; 
                }
            }
            else
            {
                $tables = is_array($tables) ? $tables : explode(',',$tables);
            }
            //cycle through
            foreach($tables as $table)
            {
                $resultcount = $this->db->prepare('SELECT count(*) FROM '.$table);
                $resultcount->execute();
                $num_fields = $resultcount->fetch(PDO::FETCH_NUM);
                $num_fields = $num_fields[0];

                $result = $this->db->prepare('SELECT * FROM '.$table);
                $result->execute();
                $col_fields = $result->columnCount();
                $data .= "-- ----------------------------"."\n";
                $data .= "-- Table structure for ".$table."\n";
                $data .= "-- ----------------------------"."\n";
                $data.= 'DROP TABLE IF EXISTS '.$table.';';

                $result2 = $this->db->prepare('SHOW CREATE TABLE '.$table);    
                $result2->execute();                            
                $row2 = $result2->fetch(PDO::FETCH_NUM);
                $data.= "\n".$row2[1].";\n\n";

                if ($num_fields > 0) {
                    $data.= 'INSERT INTO '.$table.' VALUES';
                    for ($i = 0; $i < $num_fields; $i++) {
                        while($row = $result->fetch(PDO::FETCH_NUM)) {
                            $data.= '(';
                            for($j=0; $j<$col_fields; $j++) {
                                if (!empty($row[$j])){
                                    $row[$j] = str_replace("\n","\\n",$row[$j]);
                                    $row[$j] = addslashes($row[$j]);
                                    $data.= '"'.$row[$j].'",' ;
                                } else {
                                    $data.= '"",';
                                }
                            }
                            $data = substr($data, 0, -1);
                            $data.= "),\n";
                        }
                    }
                    $data = substr($data, 0, -2);
                    $data.=";\n\n\n";
                }
            }
            $data.="SET FOREIGN_KEY_CHECKS=1;";
             
            if (!is_dir($this->folderbackupdb)) mkdir($this->folderbackupdb,0775,true);
            $newcontent = '<?php header(\'Content-type:application/json; charset=utf-8\');header("Access-Control-Allow-Origin: *");header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");header(\'HTTP/1.0 403 Forbidden\');echo \'{"status": "error","code": "403","message": "This page is forbidden."}\';?>';
            if(!file_exists($this->folderbackupdb.'/index.php')){
                $ihandle = fopen($this->folderbackupdb.'/index.php','w+'); 
                fwrite($ihandle,$newcontent); 
                fclose($ihandle);
            }
            //save filename
            $filename = $this->folderbackupdb.'/backup_'.$tablename.'_'.date('Y-m-d_H.i.s',time()).'.sql';
            if($this->writeUTF8filename($filename,$data)){
                $jdata = [
                    'status' => 'success',
                    'code' => 'RS101',
                    'message' => CustomHandlers::getreSlimMessage('RS101',$this->lang)
                ];
            } else {
                $jdata = [
                    'status' => 'error',
                    'code' => 'RS201',
                    'message' => CustomHandlers::getreSlimMessage('RS201',$this->lang)
                ];
            }
            return JSON::encode($jdata,true);
            $this->db=null;
        }

        private function writeUTF8filename($filenamename,$content){
            $f=fopen($filenamename,"w+"); 
            # Now UTF-8 - Add byte order mark 
            fwrite($f, pack("CCC",0xef,0xbb,0xbf)); 
            $result = fwrite($f,$content);
            fclose($f);
            return $result;
        }

        private function scan_dir($dir) {
            $list = array(); //main array
            if (!is_dir($this->folderbackupdb)) mkdir($this->folderbackupdb,0775,true);
            if(is_dir($dir)){
                if($dh = opendir($dir)){
                    while(($file = readdir($dh)) != false){
                        if($file == "." or $file == ".." or $file == "index.php"){
                            //...
                        } else { //create object with two fields
                            $list3 = array(
                                'name' => $file, 
                                'type' => pathinfo($file, PATHINFO_EXTENSION),
                                'bytes' => filesize($dir.'/'.$file),
                                'size' => $this->formatSize(filesize($dir.'/'.$file)),
                                'date' => date('Y-m-d H:i:s', filemtime($dir.'/'.$file)),
                                'url' => $this->baseurl.'/'.$dir.'/'.$file
                            );
                            array_push($list, $list3);
                        }
                    }
                }
                rsort($list);
                $return_array = array('files'=> $list);
                return $return_array;
            }
            return array();
        }

        private function formatSize($bytes) {
            $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
            $base = 1024;
            $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
            return sprintf('%1.2f' ,$bytes / pow($base,$class)).' '.$si_prefix[$class];
        }
    }    