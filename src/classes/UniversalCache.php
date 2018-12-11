<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\helper\Scanner;
use Predis\Client;
    /**
     * A class for generate universal cache file as key-value in traditional way (static files)
     * 
     * Note:
     * - Standar cache format is json as Key-Value. Ex. {"key":"","value":"","timestamp":""}
     * - Minimum expired cache file should be 300s (5 minutes). Longer is better but consider with your business requirement.
     * - Better to use harddisk with SDD feature to make performance faster
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
    class UniversalCache {

        /**
         * Cache will run if you set variable runcache to true
         * If you set to false, this will only disable the cache process
         */
        private static $runcache = UNIVERSAL_CACHE;

        /**
         * Default folder is cache-universal
         * Path folder is api/cache-universal/ 
         */
        private static $filefolder = 'cache-universal';

        /**
         * MD5 Hash for filename cache
         */
        private static $hash = true;

        /**
         * If set to true then traditional filebased cache will change to use memory RAM with redis server.
         */
        private static $useredis = REDIS_ENABLE;

        /**
         * Open Redis Connection.
         */
        private static function openRedis(){
            try {
                return new Client(self::paramRedis(),self::optionRedis());
            } catch (Exception $e) {
                header("Content-type: application/json; charset=utf-8");
                $data = [
                    'status' => 'error',
                    'code' => $e->getCode(),
                    'message' => trim($e->getMessage())
                ];
                die(json_encode($data));
            }
        }

        /**
         * Set Redis parameter (This parameter can be set from config.php).
         */
        private static function paramRedis(){
            return json_decode(REDIS_PARAMETER);
        }

        /**
         * Set Redis option (This option can be set from config.php).
         */
        private static function optionRedis(){
            return json_decode(REDIS_OPTION);
        }

        /**
		 * Convert string to valid UTF8 chars (slower but support ANSII)
		 *
		 * @param string is the array string or value
		 *
		 * @return string
		 */
		private static function safeConvertToUTF8($string){
			if (is_array($string)) {
				foreach ($string as $k => $v) {
					$string[$k] = self::safeConvertToUTF8($v);
				}
			} else if (is_string ($string)) {
				return mb_convert_encoding($string, "UTF-8", "Windows-1252");
			}
			return $string;
        }
        
        /**
         * Verify the folder path for cache
         */
        public static function verifyFolderPath(){
            if (!is_dir(self::$filefolder)) {
                mkdir(self::$filefolder,0775,true);
                $newcontent = '<?php header(\'Content-type:application/json; charset=utf-8\');header("Access-Control-Allow-Origin: *");header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");header(\'HTTP/1.0 403 Forbidden\');echo \'{
                    "status": "error",
                    "code": "403",
                    "message": "This page is forbidden."
                  }\';?>';
                $ihandle = fopen(self::$filefolder.'/index.php','w+'); 
                fwrite($ihandle,$newcontent); 
                fclose($ihandle);
            }
        }

        /**
		 * Get filepath cache
         * 
         * @param key = The key name
		 *
		 * @return string
		 */
        public static function filePath($key){
            self::verifyFolderPath();
            if (self::$hash) {
                $hashed = md5($key);
                return self::$filefolder.'/'.self::virtualPath($hashed).$hashed.'.cache';
            }
            return self::$filefolder.'/'.self::virtualPath($key).$key.'.cache';
        }

        /**
         * Virtual path to scale the cache storage
         * 
         * @param key = The key name
         * @param depth = The deep of sub directory cache. Default is 2.
         * 
         * @return string part of a path 
         */
        public static function virtualPath($key,$depth=2){
            $vpath = '';
            for ($i=0;$i<$depth;$i++){
                if (!empty($key[$i])) $vpath .= $key[$i].'/';
            }
            if (!is_dir(self::$filefolder.'/'.$vpath)) mkdir(self::$filefolder.'/'.$vpath,0775,true);
            return $vpath;
        }

        /**
         * Determine is current key already cached or not
         * 
         * @param cachetime = Set expired time in second (only works for filebased cache). Default value is 300 seconds (5 minutes)
         * 
         * @return bool
         */
        public static function isCached($key,$cachetime=300) {
            if (self::$runcache){
                $file = self::filePath($key);
                if (self::$useredis){
                    $redis = self::openRedis();
                    if (empty($redis->get($file))){
                        return false;
                    }
                } else {
                    // check the expired file cache.
                    $mtime = 0;
                    if (file_exists($file)) {
                        $mtime = filemtime($file);
                    }
                    $filetimemod = $mtime + $cachetime;
                    // if the renewal date is smaller than now, return true; else false (no need for update)
                    if ($filetimemod < time()) {
                        return false;
                    }
                }
            } else {
                return false;
            }
            return true;
        }

        /**
         * Load cached file
         * 
         * @param key = The Key name
         * 
         * @return string
         */
        public static function loadCache($key) {
            $file = self::filePath($key);
            if (self::$useredis){
                $redis = self::openRedis();
                return $redis->get($file);
            } else {
                if (file_exists($file)) {
                    return file_get_contents($file);
                }
            }
            return "";
        }

        /**
         * Write key to static file cache
         * 
         * @param key = The Key name
         * @param value = input value to cache
         *  @param redis_agecache = Set expired time in second (only works for Redis). Default value is 300 seconds (5 minutes)
         * 
         */
        public static function writeCache($key,$value="",$redis_agecache=300) {
            if (!empty($key)) {
                $file = self::filePath($key);
                $content = '{"key":"'.$key.'","value":'.json_encode(self::safeConvertToUTF8($value)).',"timestamp":"'.date('Y-m-d h:i:s', time()).'"}';
                if (self::$runcache) {
                    if (self::$useredis){
                        $redis = self::openRedis();
                        $redis->setex($file,$redis_agecache,$content);
                    } else {
                        file_put_contents($file, $content, LOCK_EX);
                        self::transfer($content,$key);
                    }
                }
            }
        }

        /**
         * Listen to the new data cache from another server
         * 
         * @param secretkey is the data key to proctect from unknown request
         * @param filepath is the filepath of data cache
         * @param content the new data cache
         * 
         * @return array
         */
        public static function listen($secretkey,$filepath,$content){
            $data = [];
            if (CACHE_TRANSFER){
                if ($secretkey == CACHE_SECRET_KEY){
                    self::verifyFolderPath();
                    $key = basename($filepath, ".cache");
                    self::virtualPath($key);
                    file_put_contents($filepath, $content, LOCK_EX);
                    $data = [
                        'status' => 'success',
                        'message' => 'Successful to listen data.'
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'message' => 'Request rejected! Server doesn\'t have authority to listen.'
                    ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Request rejected! Failed to listen data.'
                ];
            }
            return $data;
        }

        /**
         * Transfer the data cache to another server
         * 
         * @param content is the data cache
         * @param key is the key name
         */
        public static function transfer($content,$key){
            if (CACHE_TRANSFER){
                if (!empty(CACHE_LISTENFROM)){
                    $server = json_decode(CACHE_LISTENFROM,true);
                    if (!empty($server)){
                        $request = array();
                        foreach($server as $value){
                            $request[] = [
                                'url' => $value.'/maintenance/cache/universal/listen',
                                'post' => [
                                    'filepath' => self::filePath($key),
                                    'content' => $content,
                                    'secretkey' => CACHE_SECRET_KEY
                                ]
                            ];
                        }
                        $req = new ParallelRequest;
                        $req->request = $request;
                        $req->encoded = true;
                        $req->options = [
                            CURLOPT_NOBODY => false,
                            CURLOPT_HEADER => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_RETURNTRANSFER => true,
                        ];
                        $req->send();
                    }
                }
            }
        }

        /**
         * Listen to delete the data cache from another server
         * 
         * @param secretkey is the data key to proctect from unknown request
         * @param pattern is the filename cache. Default is all files which is ended with .cache
         * @param agecache is to specify the age of cache file to be deleted. Default will delete file which is already have more 5 minutes old.
         * 
         * @return array
         */
        public static function listenToDelete($secretkey,$pattern=".cache",$agecache=300){
            $data = [];
            if (CACHE_TRANSFER){
                if ($secretkey == CACHE_SECRET_KEY){
                    self::verifyFolderPath();
                    $data = self::deleteCacheAll($pattern, $agecache, false);
                } else {
                    $data = [
                        'status' => 'error',
                        'message' => 'Request rejected! Server doesn\'t have authority to listen.'
                    ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Request rejected! Failed to listen data.'
                ];
            }
            return $data;
        }

        /**
         * Transfer request to delete the data cache to another server
         * 
         * @param pattern is the filename cache. Default is all files which is ended with .cache
         * @param agecache is to specify the age of cache file to be deleted. Default will delete file which is already have more 5 minutes old.
         */
        public static function transferToDelete($pattern=".cache",$agecache=300){
            if (CACHE_TRANSFER){
                if (!empty(CACHE_LISTENFROM)){
                    $server = json_decode(CACHE_LISTENFROM,true);
                    if (!empty($server)){
                        $request = array();
                        foreach($server as $value){
                            $request[] = [
                                'url' => $value.'/maintenance/cache/universal/listen/delete',
                                'post' => [
                                    'pattern' => $pattern,
                                    'agecache' => $agecache,
                                    'secretkey' => CACHE_SECRET_KEY
                                ]
                            ];
                        }
                        $req = new ParallelRequest;
                        $req->request = $request;
                        $req->encoded = true;
                        $req->options = [
                            CURLOPT_NOBODY => false,
                            CURLOPT_HEADER => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_RETURNTRANSFER => true,
                        ];
                        $req->send();
                    }
                }
            }
        }

        /**
         * Listen to delete the single key data cache from another server
         * 
         * @param secretkey is the data key to proctect from unknown request
         * @param key is the key of cache
         * @param agecache is to specify the age of cache file to be deleted. Default will delete file which is already have more 5 minutes old.
         * 
         * @return array
         */
        public static function listenToDeleteSingleKey($secretkey,$key,$agecache=300){
            $data = [];
            if (CACHE_TRANSFER){
                if ($secretkey == CACHE_SECRET_KEY){
                    self::verifyFolderPath();
                    self::deleteCache($key, $agecache, false);
                    $data = [
                        'status' => 'success',
                        'message' => 'Successfully to listen the incoming request.'
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'message' => 'Request rejected! Server doesn\'t have authority to listen.'
                    ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Request rejected! Failed to listen data.'
                ];
            }
            return $data;
        }

        /**
         * Transfer request to delete the single key data cache to another server
         * 
         * @param key is the key of cache.
         * @param agecache is to specify the age of cache file to be deleted. Default will delete file immediately.
         */
        public static function transferToDeleteSingleKey($key,$agecache=0){
            if (CACHE_TRANSFER){
                if (!empty(CACHE_LISTENFROM)){
                    $server = json_decode(CACHE_LISTENFROM,true);
                    if (!empty($server)){
                        $request = array();
                        foreach($server as $value){
                            $request[] = [
                                'url' => $value.'/maintenance/cache/universal/listen/delete/key',
                                'post' => [
                                    'keycache' => $key,
                                    'agecache' => $agecache,
                                    'secretkey' => CACHE_SECRET_KEY
                                ]
                            ];
                        }
                        $req = new ParallelRequest;
                        $req->request = $request;
                        $req->encoded = true;
                        $req->options = [
                            CURLOPT_NOBODY => false,
                            CURLOPT_HEADER => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_RETURNTRANSFER => true,
                        ];
                        $req->send();
                    }
                }
            }
        }

        /**
         * Delete static key file cache
         * 
         * @param key = The Key name
         * @param agecache = Specify the age of cache file to be deleted. Default will delete file immediately.
         * @param transfer = If set to true then will make request to delete the data cache on another server. Default is true.
         */
        public static function deleteCache($key,$agecache=0,$transfer=true) {
            if (!empty($key)) {
                $file = self::filePath($key);
                if (self::$useredis){
                    $redis = self::openRedis();
                    $redis->del($file);
                } else {
                    if (file_exists($file)){
                        if ($agecache=0){
                            unlink($file);
                        } else {
                            $now   = time();
                            if ($now - filemtime($file) >= $agecache) {
                                unlink($file);
                            }
                        }
                        if($transfer) self::transferToDeleteSingleKey($key,$agecache);
                    }
                }
            }
        }

        /**
         * Delete all static Key cache
         * 
         * @param pattern is the filename cache. Default is all files which is ended with .cache
         * @param agecache = Specify the age of cache file to be deleted. Default will delete cached files which is already have more 300 seconds old.
         * @param transfer = If set to true then will make request to delete the data cache on another server. Default is true.
         * 
         * @return json
         */
        public static function deleteCacheAll($pattern=".cache",$agecache=300,$transfer=true) {
            if (file_exists(self::$filefolder)) {
                //Build list cached files
                $files = Scanner::fileSearch(self::$filefolder.'/', $pattern);
                $now   = time();

                $total = 0;
                $deleted = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $total++;
                        if ($now - filemtime($file) >= $agecache) {
                            unlink($file);
                            $deleted++;
                        }
                    }
                }
                $datajson = '{"status":"success","age":'.$agecache.',"total_files":'.$total.',"total_deleted":'.$deleted.',"execution_time":"'.(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]).'","message":"To prevent any error occured on the server, only cached files that have age more than '.$agecache.' seconds old, will be deleted."}';
                if ($transfer) self::transferToDelete($pattern,$agecache);
            } else {
                $datajson = '{"status:"error","message":"Directory not found!"}';
            }
            return $datajson;
        }

        /**
         * Determine if cache is activated
         * 
         * @return bool
         */
        public static function isCacheActive() {
            return self::$runcache;
        }

        /**
         * Get total size of the cache folder
         * 
         * @param formatted if set to false will return bytes
         * 
         * @return string
         */
        public static function getCacheSize($formatted=true) {
            if (!is_dir(self::$filefolder)) mkdir(self::$filefolder,0775,true);
            $size = 0;
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::$filefolder)) as $file){
                $size += $file->getSize();
            }
            return (($formatted)?self::formatSize($size):$size);
        }

        /**
         * Get total available size on harddisk
         * 
         * @param formatted if set to false will return bytes
         * 
         * @return string
         */
        public static function getCacheAvailSize($formatted=true) {
            return (($formatted)?self::formatSize(disk_free_space(".")):disk_free_space("."));
        }

        /**
         * Get total available size on harddisk
         * 
         * @param formatted if set to false will return bytes
         * 
         * @return string
         */
        public static function getCacheHDDSize($formatted=true){
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $ds = disk_total_space("C:");
            } else {
                $ds = disk_total_space("/");
            }
            return (($formatted)?self::formatSize($ds):$ds);
        }

        /**
         * Get info folder name
         * 
         * @return string
         */
        public static function getCacheFolder() {
            return self::$filefolder;
        }

        /**
         * Get status cache
         * 
         * @return array
         */
        public static function getCacheStatus() {
            if(self::$runcache) {
                return ['status'=>'active','description'=>'Cache is running!'];
            }
            return ['status'=>'disabled','description'=>'Cache is disabled!'];
        }

        /**
         * Get info data cache
         * 
         * @return array
         */
        public static function getCacheInfo() {
            if (!is_dir(self::$filefolder)) mkdir(self::$filefolder,0775,true);
            $size = 0;
            $files = 0;
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::$filefolder, \RecursiveDirectoryIterator::SKIP_DOTS)) as $file){
                $size += $file->getSize();
                $files++;
            }
            $total = self::getCacheHDDSize(false);
            $free = self::getCacheAvailSize(false);
            $usehdd = $total-$free;
            $usecache = $size;
            $freehddpercent = sprintf('%1.2f',((($total-$usehdd)/$total)*100)).'%';
            $usehddpercent = sprintf('%1.2f',((($total-$free)/$total)*100)).'%';
            $usecachepercent = sprintf('%1.6f',((($total-($total-$usecache))/$total)*100)).'%';
            $data = self::getCacheStatus();
            $data['folder'] = self::$filefolder;
            $data['files'] = $files;
            $result = [
                'status'=>'success',
                'info'=>$data,
                'size'=>[
                    'cache'=>['use'=>self::formatSize($size),'free'=>self::formatSize($free)],
                    'hdd'=>['use'=>self::formatSize($usehdd),'free'=>self::formatSize($free),'total'=>self::formatSize($total)]
                ],
                'percent'=>[
                    'cache'=>['use'=>$usecachepercent,'free'=>$freehddpercent],
                    'hdd'=>['use'=>$usehddpercent,'free'=>$freehddpercent]
                ],
                'bytes'=>[
                    'cache'=>['use'=>$size,'free'=>$free],
                    'hdd'=>['use'=>$usehdd,'free'=>$free,'total'=>$total]
                ]
            ];
            if (self::$useredis) $result['redis'] = self::getRedisInfo(); 
            return $result;
        }

        /**
         * Get info Redis Server
         * 
         * @return array
         */
        public static function getRedisInfo() {
            $redis = self::openRedis();
            foreach ($redis as $node) {
                $info = $node->info();
            }
            return $info;
        }

        /**
         * Formatting bytes to human readable format
         * 
         * @param bytes is the value
         * 
         * @return string
         */
        private static function formatSize($bytes) {
            $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
            $base = 1024;
            $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
            return sprintf('%1.2f' ,$bytes / pow($base,$class)).' '.$si_prefix[$class];
        }

    }