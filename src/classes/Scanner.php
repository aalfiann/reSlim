<?php
namespace classes;

class Scanner {

    /**
     * Last char checker (alternative to preg_match)
     * 
     * @param match = is the text to match
     * @param string = is the source text
     * 
     * @return bool 
     */
    public static function isMatchLast($match,$string){
        if (substr($string, (-1 * abs(strlen($match)))) == $match) return true;
        return false;
    }

    /**
     * fileSearch is using opendir (very fast)
     * 
     * @param dir = is the full path of directory
     * @param ext = is the extension of file. Default is php extension. Example Regex: $pattern = "/\\.{$ext}$/"; or $pattern="/\.router.php$/";
     * @param extIsRegex = if set to true then the $ext variable will be executed as regex way. Default is false.
     * 
     * @return array
     */
    public static function fileSearch($dir, $ext='php',$extIsRegex=false) {
        $files = [];
        $fh = opendir($dir);

        while (($file = readdir($fh)) !== false) {
            if($file == '.' || $file == '..')
                continue;

            $filepath = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filepath))
                $files = array_merge($files, self::fileSearch($filepath, $ext));
            else {
                if($extIsRegex){
                    if(preg_match($ext, $file)) array_push($files, $filepath);
                } else {
                    if(self::isMatchLast($ext,$file)) array_push($files, $filepath);
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
                if($current->isFile() && self::isMatchLast($ext, $current->getFilename())) return true;
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
            $files = array_merge($files, self::recursiveGlob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

}