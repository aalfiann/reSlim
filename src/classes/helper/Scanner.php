<?php
namespace classes\helper;
use \classes\helper\StringUtils;

class Scanner {

    /**
     * fileSearch is using opendir (very fast)
     * 
     * @param dir = is the full path of directory
     * @param ext = is the extension of file. Default is php extension. Example Regex: $pattern = "/\\.{$ext}$/"; or $pattern="/\.router.php$/";
     * @param extIsRegex = if set to true then the $ext variable will be executed as regex way. Default is false.
     * @param excludedir = is to stop recursive into sub folder. Default is empty means will recursive all sub directories.
     * 
     * @return array
     */
    public static function fileSearch($dir, $ext='php',$extIsRegex=false,$excludedir='') {
        $files = [];
        $fh = opendir($dir);
        if($excludedir !== ''){
            if(is_string($excludedir)){
                if (!StringUtils::isMatchAny($excludedir,$dir)){
                    while (($file = readdir($fh)) !== false) {
                        if($file == '.' || $file == '..') continue;
                        $filepath = $dir . DIRECTORY_SEPARATOR . $file;
                        if (is_dir($filepath))
                            $files = array_merge($files, self::fileSearch($filepath, $ext, $extIsRegex, $excludedir));
                        else {
                            if($extIsRegex){
                                if(preg_match($ext, $file)) array_push($files, $filepath);
                            } else {
                                if(StringUtils::isMatchLast($ext,$file)) array_push($files, $filepath);
                            }
                        }
                    }
                }
            } else {
                foreach($excludedir as $dirs){
                    if (!StringUtils::isMatchAny($dirs,$dir)){
                        while (($file = readdir($fh)) !== false) {
                            if($file == '.' || $file == '..') continue;
                            $filepath = $dirs . DIRECTORY_SEPARATOR . $file;
                            if (is_dir($filepath))
                                $files = array_merge($files, self::fileSearch($filepath, $ext, $extIsRegex, $excludedir));
                            else {
                                if($extIsRegex){
                                    if(preg_match($ext, $file)) array_push($files, $filepath);
                                } else {
                                    if(StringUtils::isMatchLast($ext,$file)) array_push($files, $filepath);
                                }
                            }
                        }   
                    }
                }
            }
        } else {
            while (($file = readdir($fh)) !== false) {
                if($file == '.' || $file == '..') continue;
                $filepath = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filepath))
                    $files = array_merge($files, self::fileSearch($filepath, $ext, $extIsRegex, $excludedir));
                else {
                    if($extIsRegex){
                        if(preg_match($ext, $file)) array_push($files, $filepath);
                    } else {
                        if(StringUtils::isMatchLast($ext,$file)) array_push($files, $filepath);
                    }
                }
            }
        }
        closedir($fh);
        return $files;
    }

    /**
     * filesystemIterator
     * 
     * @param dir = is the full path of directory
     * @param ext = is the extension of file. Default is php extension. Example: $pattern = "/\\.{$ext}$/"; or $pattern="/\.router.php$/";
     * @param extIsRegex = if set to true then the $ext variable will be executed as regex way. Default is false.
     * 
     * @return array RegexIterator
     */
    public static function filesystemIterator($dir,$ext='php',$extIsRegex=false){
        if($extIsRegex) {
            $pattern = $ext;
        } else {
            $pattern = '/\.'.$ext.'$/';
        }
        $filesystemIterator = new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS);
        return new \RegexIterator($filesystemIterator, $pattern);
    }

    /**
     * regexIterator
     * 
     * @param dir = is the full path of directory
     * @param ext = is the extension of file. Default is php extension. Example Regex: $pattern = "/\\.{$ext}$/"; or $pattern="/\.router.php$/";
     * @param extIsRegex = if set to true then the $ext variable will be executed as regex way. Default is false.
     * 
     * @return array RegexIterator
     */
    public static function regexIterator($dir,$ext='php',$extIsRegex=false){
        if($extIsRegex) {
            $pattern = $ext;
        } else {
            $pattern = '/\.'.$ext.'$/';
        }
        $dirs = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($dirs);
        return new \RegexIterator($iterator, $pattern);
    }

    /**
     * recursiveCallbackIterator
     * 
     * @param dir = is the full path of directory
     * @param ext = is the extension of file. Default is php extension. Example Regex: $pattern = "/\\.{$ext}$/"; or $pattern="/\.router.php$/";
     * @param extIsRegex = if set to true then the $ext variable will be executed as regex way. Default is false.
     * 
     * @return array RecursiveIteratorIterator
     */
    public static function recursiveCallbackIterator($dir,$ext='php',$extIsRegex=false){
        $dirs = new \RecursiveDirectoryIterator($dir);
        $filter = new \RecursiveCallbackFilterIterator($dirs, function($current, $key, $iterator) {
            if ($iterator->hasChildren()) return true;
            if($extIsRegex){
                if($current->isFile() && preg_match($pattern, $current->getFilename())) return true;
            } else {
                if($current->isFile() && StringUtils::isMatchLast($ext, $current->getFilename())) return true;
            }
        });
        return new \RecursiveIteratorIterator($filter);
    }

    /**
     * recursiveGlob (support wilcard but very not recommended for big directory with million files)
     * 
     * @param pattern = is the pattern. No tilde expansion or parameter substitution is done.
     * @param flags = is the options for glob.
     * 
     * @return array
     */
    public static function recursiveGlob($pattern, $flags = 0){
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
            $files = array_merge($files, self::recursiveGlob($dir.DIRECTORY_SEPARATOR.basename($pattern), $flags));
        }
        return $files;
    }

}