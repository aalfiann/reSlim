<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\SimpleCache as SimpleCache;
	/**
     * A class for generate simple cache file response json in traditional way (static files)
     * 
     * Note:
     * - App Cache is better to use for api with public Api Keys
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
                                if (strpos($key,$singleparam) !== false){
                                    $rebuild .= $key.'-'.$value.'-';
                                }
                            }
                        }
                    } else {
                        foreach ($_GET as $key => $value) {
                            if (strpos($key,$setparam) !== false){
                                $rebuild .= $key.'-'.$value.'-';
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
            if (!is_dir(self::$filefolder)) {
                mkdir(self::$filefolder,0775,true);
            }            
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
         */
        public static function clearAll(){
            if (file_exists(self::$filefolder)) {
                //Auto delete useless cache
                $files = glob(self::$filefolder.'/*');
                $now   = time();

                $total = 0;
                $deleted = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $total++;
                        if ($now - filemtime($file) >= 60 * 5) { // 5 minutes ago
                            unlink($file);
                            $deleted++;
                        }
                    }
                }
                $datajson = '{"status":"success","total_files":'.$total.',"total_deleted":'.$deleted.',"execution_time":"'.(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]).'","message":"To prevent any error occured on the server, only cache files that have age more than 5 minutes old, will be deleted."}';
            } else {
                $datajson = '{"status:"error","message":"Directory not found!"}';
            }
            return $datajson;
        }

    }