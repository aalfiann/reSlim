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
		 * Convert string to valid UTF8 chars (slower but support ANSII)
		 *
		 * @var string is the array string or value
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
		 * Get filepath cache
         * 
         * @param key = The key name
		 *
		 * @return string
		 */
        public static function filePath($key){
            if (!is_dir(self::$filefolder)) mkdir(self::$filefolder,0775,true);           
            return self::$filefolder.'/'.$key.'.cache';
        }

        /**
         * Determine is current key already cached or not
         * 
         * @param cachetime = Set expired time in second. Default value is 300 seconds (5 minutes)
         * 
         * @return bool
         */
        public static function isCached($key,$cachetime=300) {
            if (self::$runcache){
                $file = self::filePath($key);
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
         * @param key = The Key name
         * 
         * @return string
         */
        public static function loadCache($key) {
            $file = self::filePath($key);
            if (file_exists($file)) {
                return file_get_contents($file);
            }
            return "";
        }

        /**
         * Write key to static file cache
         * 
         * @param key = The Key name
         * @param value = input value to cache
         * 
         */
        public static function writeCache($key,$value="") {
            if (!empty($key)) {
                $file = self::filePath($key);
                $content = '{"key":"'.$key.'","value":'.json_encode(self::safeConvertToUTF8($value)).',"timestamp":"'.date('Y-m-d h:i:s', time()).'"}';
                if (self::$runcache) file_put_contents($file, $content, LOCK_EX);
            }
        }

        /**
         * Delete static key file cache
         * 
         * @param key = The Key name
         * @param agecache = Specify the age of cache file to be deleted. Default will delete file immediately.
         * 
         */
        public static function deleteCache($key,$agecache=0) {
            if (!empty($key)) {
                $file = self::filePath($key);
                if (file_exists($file)){
                    if ($agecache=0){
                        unlink($file);
                    } else {
                        $now   = time();
                        if ($now - filemtime($file) >= $agecache) {
                            unlink($file);
                        }
                    }
                }
            }
        }

        /**
         * Delete all static Key cache
         * 
         * @param wildcard = You can set whatever kind of pathname matching wildcard to be deleted. Default is *
         * @param agecache = Specify the age of cache file to be deleted. Default will delete cached files which is already have more 300 seconds old.
         */
        public static function deleteCacheAll($wildcard="*",$agecache=300) {
            if (file_exists(self::$filefolder)) {
                //Build list cached files
                $files = glob(self::$filefolder.'/'.$wildcard,GLOB_NOSORT);
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