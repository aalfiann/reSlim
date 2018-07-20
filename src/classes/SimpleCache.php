<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
	/**
     * A class for generate simple cache file the output response json in traditional way (static files)
     * 
     * Note:
     * - Simple Cache is for cache the output response from reSlim and better to use with public Api Keys
     * - Minimum expired cache file should be 300s (5 minutes). Longer is better but consider with your business requirement.
     * - Url contains param also will cached automatically, so this will created a thousand static files in your harddisk. You should setparam or blacklistparam to avoid this
     * - Better to use harddisk with SDD feature to make performance faster
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
	class SimpleCache {

        /**
         * Default folder is cache
         * Path folder is api/cache/ 
         */
        private static $filefolder = "cache";

        /**
         * Cache will run if you set variable runcache to true
         * If you set to false, this only disable the cache process and will not deleted the current cache files
         */
        private static $runcache = true;

        /**
         * Determine content string is valid json or not
         * 
         * @param string = string json
         * 
         * @return bool
         */
        private static function isJson($string) {
            if (empty($string)) return false;
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }

        /**
		 * Get filename cache
         * 
         * @param setparam = Cache the page according to the specified parameter. This is a prevention for undesirable parameters getting to cache. Ex: ["apikey","query"]
		 *
		 * @return string
		 */
        public static function fileName($setparam=null){
		    // Detect url file
            $url = trim($_SERVER['REQUEST_URI'] ,'/');
            $link_array = explode('/',$url);
            $file = "";
            foreach ($link_array as $key){
                $file .= $key.'-';
            }
            $file = substr($file, 0, -1);
            if (empty($setparam)){    
                $path = str_replace('?','_',str_replace(['=','&'],'-',$file)).'.cache';
            } else {
                $build = explode('?',$file);
                if (!empty($build[0])){
                    $rebuild = '';
                    if (is_array($setparam)){
                        foreach ($_GET as $key => $value) {
                            foreach ($setparam as $singleparam){
                                if ($key == $singleparam){
                                    $rebuild .= $key.'-'.str_replace(['\\','/','<','>',':','?','"','|','*'],'_',$value).'-';
                                }
                            }
                        }
                    } else {
                        foreach ($_GET as $key => $value) {
                            if ($key == $setparam){
                                $rebuild .= $key.'-'.str_replace(['\\','/','<','>',':','?','"','|','*'],'_',$value).'-';
                            }
                        }
                    }
                    $rebuild = substr($rebuild, 0, -1);
                    if (empty($build[1])){
                        $path = $build[0].$rebuild.'.cache';
                    } else {
                        $path = $build[0].'_'.$rebuild.'.cache';
                    }
                } else {
                    $path = str_replace('?','_',str_replace(['=','&'],'-',$file)).'.cache';
                }
            }
            return $path;
        }

        /**
		 * Get filepath cache
         * 
         * @param setparam = Cache the page according to the specified parameter. This is a prevention for undesirable parameters getting to cache. Ex: ["apikey","query"]
		 *
		 * @return string
		 */
        public static function filePath($setparam=null){
            if (!is_dir(self::$filefolder)) mkdir(self::$filefolder,0775,true);          
            return self::$filefolder.'/'.self::fileName($setparam);
        }

        /**
         * Determine is uri contains parameter to not cache?
         * 
         * @param blacklistparam = = Input parameter name to not getting cached. Ex: ["&_=","&query=","&search=","token","apikey","api_key","time","timestamp","time_stamp","etag","key","q","s","k","t"]
         * 
         * @return bool
         */
        private static function isBlacklisted($blacklistparam=null){
            if(!empty($blacklistparam)){
                if (is_array($blacklistparam)){
                    foreach($blacklistparam as $value){
                        if (strpos(self::filePath(),'-'.str_replace(['&','='],'',$value).'-') !== false) return true;
                    }
                } else {
                    if (strpos(self::filePath(),'-'.str_replace(['&','='],'',$blacklistparam).'-') !== false) return true;
                }
            }
            return false;
        }

        /**
         * Determine is current url already cached or not
         * 
         * @param cachetime = Set expired time in second. Default value is 300 seconds (5 minutes)
         * @param setparam = Cache the page according to the specified parameter. This is a prevention for undesirable parameters getting to cache. Ex: ["apikey","query"]
         * 
         * @return bool
         */
        public static function isCached($cachetime=300,$setparam=null) {
            if (self::$runcache){
                $file = self::filePath($setparam);
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
            } else {
                return false;
            }
            return true;
        }

        /**
         * Load cached file
         * 
         * @param setparam = Cache the page according to the specified parameter. This is a prevention for undesirable parameters getting to cache. Ex: ["apikey","query"]
         * 
         * @return string
         */
        public static function load($setparam=null) {
            $file = self::filePath($setparam);
            if (file_exists($file)) {
                return file_get_contents($file);
            }
            return "";
        }

        /**
         * Save content to static file cache
         * 
         * @param datajson = Save the content to cache file. (json string only)
         * @param setparam = Cache the page according to the specified parameter. This is a prevention for undesirable parameters getting to cache. Ex: ["apikey","query"]
         * @param blacklistparam = Input parameter to not getting cached. Ex: ["&_=","&query=","&search=","token","apikey","api_key","time","timestamp","time_stamp","etag","key","q","s","k","t"]
         * 
         * @return string
         */
        public static function save($datajson,$setparam=null,$blacklistparam=null) {
            $file = self::filePath($setparam);
            if (!empty($datajson) && self::isJson($datajson)) {
                if (self::$runcache && self::isBlacklisted($blacklistparam) == false) file_put_contents($file, $datajson, LOCK_EX);
            }
            return $datajson;
        }

        /**
         * Clear all cache files that have age more than 5 minutes old
         * 
         * @param wildcard = You can set whatever kind of pathname matching wildcard to be deleted. Default is *
         * @param agecache = Specify the age of cache file to be deleted. Default will delete file which is already have more 5 minutes old.
         */
        public static function clearAll($wildcard="*",$agecache=300){
            if (file_exists(self::$filefolder)) {
                //Auto delete useless cache
                $files = glob(self::$filefolder.'/'.$wildcard,GLOB_NOSORT);
                $now   = time();

                $total = 0;
                $deleted = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $total++;
                        if ($now - filemtime($file) >= $agecache) { // 5 minutes ago
                            unlink($file);
                            $deleted++;
                        }
                    }
                }
                $datajson = '{"status":"success","age":'.$agecache.',"total_files":'.$total.',"total_deleted":'.$deleted.',"execution_time":"'.(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]).'","message":"To prevent any error occured on the server, only cache files that have age more than '.$agecache.' seconds old, will be deleted."}';
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
         * @return mixed
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
         * @return mixed
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
            $files = -2;
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::$filefolder)) as $file){
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
            return [
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