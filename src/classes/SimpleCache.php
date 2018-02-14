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
     * Warning: Url contains param also will cached automatically, so this will created a thousand static files in your harddisk
     * Better to use harddisk with SDD feature to make performance faster
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
         * Determine content string is valid json or not
         * 
         * @param string = string json
         * 
         * @return bool
         */
        private static function isJson($string) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }

        /**
		 * Get filename cache
		 *
		 * @return string
		 */
        public static function fileName(){
		    // Detect url file
            $url = trim($_SERVER['REQUEST_URI'] ,'/');
            $link_array = explode('/',$url);
            $file = "";
            foreach ($link_array as $key){
                $file .= $key.'-';
            }
            $file = substr($file, 0, -1);
            $path = str_replace('?','_',str_replace(['=','&'],'-',$file)).'.cache';
            return $path;
        }

        /**
		 * Get filepath cache
		 *
		 * @return string
		 */
        public static function filePath(){
            if (!is_dir(self::$filefolder)) {
                mkdir(self::$filefolder,0775,true);
            }            
            return self::$filefolder.'/'.self::fileName();
        }

        /**
         * Determine is current url already cached or not
         * 
         * @param cachetime = Set expired time in second. Default value is 300 seconds (5 minutes)
         * 
         * @return bool
         */
        public static function isCached($cachetime=300) {
            $file = self::filePath();
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
            return true;
        }

        /**
         * Load cached file
         * 
         * @return string
         */
        public static function load() {
            $file = self::filePath();
            if (file_exists($file)) {
                return file_get_contents($file);
            }
            return "";
        }

        /**
         * Save content to static file cache
         * 
         * @param datajson = Save the content to cache file. (json string only)
         * @param cachetime = Set expired time in second. Default value is 300 seconds (5 minutes)
         * 
         * @return string
         */
        public static function save($datajson,$cachetime=300) {
            $file = self::filePath();
            if (!empty($datajson) && self::isJson($datajson)) {
                file_put_contents($file, $datajson);
            }
            return $datajson;
        }

    }