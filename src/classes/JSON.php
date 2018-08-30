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
     * A class for handle json in a better way
	 * 
	 * Note:
	 *  - You don't have to use this class inside reslim framework because everything in reslim will return JSON Standard: Unicode UTF-8
	 *  - But sometimes chars returned from input, submit or database contains unicode characters, so is best to use this class for prevent any error on data json
	 *  - This class is good to handle json from other resource or for experimental purpose
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2018 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
	class JSON {

		/**
		 * Convert string to valid UTF8 chars (Faster but not support for ANSII)
		 *
		 * @var string is the array string or value
		 *
		 * @return string
		 */
		private static function convertToUTF8($string){
			if (is_array($string)) {
				foreach ($string as $k => $v) {
					$string[$k] = self::convertToUTF8($v);
				}
			} else if (is_string ($string)) {
				return utf8_encode($string);
			}
			return $string;
		}

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
		 * Encode Array string or value to json (faster with no any conversion to utf8)
		 *
		 * @var data is the array string or value
		 * @var withlog if set to true then will append logger data. Default is false.
		 * @var pretty is to make ouput json nice, clean and readable. Default is false for perfomance speed reason.
		 * @var options is to set the options of json_encode. Ex: JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS
		 * @var depth is to set the recursion depth. Default is 512.
		 *
		 * @return string
		 */
        public static function encode($data,$withlog=false,$pretty=false,$options=0,$depth=512){
			if($withlog && is_array($data)) $data['logger'] = ['timestamp' => date('Y-m-d H:i:s', time()),'uniqid'=>uniqid()];
			if ($pretty && $options==0){
				return json_encode($data,JSON_PRETTY_PRINT,$depth);
			}
			return json_encode($data,$options,$depth);
		}

		/**
		 * Safest way to encode Array string or value to json (safe but slower because conversion)
		 * When the time to use this function: If you want to display json data which is retrieve from database that maybe contains invalid utf8 chars.
		 *
		 * @var data is the array string or value
		 * @var withlog if set to true then will append logger data. Default is false.
		 * @var pretty is to make ouput json nice, clean and readable. Default is false for perfomance speed reason.
		 * @var ansii if set to true then conversion to utf8 is more reliable because will work for ANSII chars. Default is set to false for performance reason.
		 * @var options is to set the options of json_encode. Ex: JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS
		 * @var depth is to set the recursion depth. Default is 512.
		 *
		 * @return string
		 */
        public static function safeEncode($data,$withlog=false,$pretty=false,$ansii=false,$options=0,$depth=512){
			if($withlog && is_array($data)) $data['logger'] = ['timestamp' => date('Y-m-d H:i:s', time()),'uniqid'=>uniqid()];
			if ($pretty && $options==0){
				return json_encode((($ansii)?self::safeConvertToUTF8($data):self::convertToUTF8($data)),JSON_PRETTY_PRINT,$depth);
			}
			return json_encode((($ansii)?self::safeConvertToUTF8($data):self::convertToUTF8($data)),$options,$depth);
		}

		/**
		 * Decode json string (if fail will return null)
		 *
		 * @var json is the json string
		 * @var assoc if set to true then will return as array()
		 * @var depth is to set the recursion depth. Default is 512.
		 * @var options is to set the options of json_decode. Ex: JSON_BIGINT_AS_STRING|JSON_OBJECT_AS_ARRAY
		 *
		 * @return mixed stdClass/array
		 */
		public static function decode($json,$assoc=false,$depth=512,$options=0){
			return json_decode($json,$assoc,$depth,$options);
		}

		/**
         * Modify json data string in some field array to be nice json data structure
		 * 
		 * Note:
		 * - When you put json into database, then you load it with using PDO:fetch() or PDO::fetchAll() it will be served as string inside some field array.
		 * - So this function is make you easier to modify the json string to become nice json data structure automatically.
		 * - This function is well tested at here >> https://3v4l.org/G8Jaa
         * 
         * @var data is the data array
         * @var jsonfield is the field which is contains json string
         * @var setnewfield is to put the result of modified json string in new field
         * @return mixed array or string (if the $data is string then will return string)
         */
		public static function modifyJsonStringInArray($data,$jsonfield,$setnewfield=""){
			if (is_array($data)){
				if (count($data) == count($data, COUNT_RECURSIVE)) {
					foreach($data as $value){
						if(!empty($setnewfield)){
							if (is_array($jsonfield)){
								for ($i=0;$i<count($jsonfield);$i++){
									if (isset($data[$jsonfield[$i]])){
										$data[$setnewfield[$i]] = json_decode($data[$jsonfield[$i]]);
									}
								}
							} else {
								if (isset($data[$jsonfield])){
									$data[$setnewfield] = json_decode($data[$jsonfield]);
								}
							}
						} else {
							if (is_array($jsonfield)){
								for ($i=0;$i<count($jsonfield);$i++){
									if (isset($data[$jsonfield[$i]])){
										if (is_string($data[$jsonfield[$i]])) $data[$jsonfield[$i]] = json_decode($data[$jsonfield[$i]]);
									}
								}
							} else {
								if (isset($data[$jsonfield])){
									$data[$jsonfield] = json_decode($data[$jsonfield]);
								}
							}
						}
					}
				} else {
					foreach($data as $key => $value){
						$data[$key] = self::modifyJsonStringInArray($data[$key],$jsonfield,$setnewfield);
					}
				}
			}
			return $data;
		}


		/**
		 * Determine is valid json or not
		 *
		 * @var data is the array string or value
		 *
		 * @return bool
		 */
		public static function isValid($data=null) {
			if (empty($data) || ctype_space($data)) return false;
			json_decode($data);
			return (json_last_error() === JSON_ERROR_NONE);
		}

		/**
		 * Debugger to test json encode
		 * Note: 
		 * - Actualy this is hard to test json_encode in php way, you have to encode the string to another utf8 chars by yourself.
		 * - This is because if you do in php, json_encode function will auto giving backslash in your string if it contains invalid chars.
		 * - You have to create regex to convert char to invalid utf-8 for test in array
		 *
		 * @var string is the array string or value
		 * @var lite if set to true will hide the additional data about error in json. Default is false.
		 *
		 * @return string json output formatted
		 */
		public static function debug_encode($string,$lite=false){
			json_encode($string,JSON_UNESCAPED_UNICODE);
			$data = self::errorMessage(json_last_error(),$string,$lite);
			return json_encode($data,JSON_PRETTY_PRINT);
		}
		
		/**
		 * Debugger to test json decode
		 *
		 * @var json is the json string
		 * @var lite if set to true will hide the additional data about error in json. Default is false.
		 *
		 * @return string json output formatted
		 */
		public static function debug_decode($json,$lite=false){
			$test = json_decode($json);
			if (!empty($test)){
				$json = $test;
			}
			$data = self::errorMessage(json_last_error(),$json,$lite);
			return json_encode($data,JSON_PRETTY_PRINT);
		}

		/**
		 * Case error message about json
		 * 
		 * @var jsonlasterror is the json_last_error() function
		 * @var content is the additional data about error in json
		 * @var lite if set to true will hide the additional data about error in json. Default is false.
		 * 
		 * @return array
		 */
		public static function errorMessage($jsonlasterror,$content,$lite=false){
			$data = array();
			switch ($jsonlasterror) {
				case JSON_ERROR_NONE:
					$msg = 'no errors found';
					if($lite){
						$data = ['status' => 'success','case' => 'json_error_none','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'success','case' => 'json_error_none','message' => $msg];
					}
					break;
				case JSON_ERROR_DEPTH:
					$msg = 'maximum stack depth exceeded';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_depth','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_depth','message' => $msg];
					}
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$msg = 'underflow or the modes mismatch';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_state_mismatch','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_state_mismatch','message' => $msg];
					}
					break;
				case JSON_ERROR_CTRL_CHAR:
					$msg = 'unexpected control character found';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_ctrl_char','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_ctrl_char','message' => $msg];
					}
					break;
				case JSON_ERROR_SYNTAX:
					$msg = 'syntax error, malformed json';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_syntax','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_syntax','message' => $msg];
					}
					break;
				case JSON_ERROR_UTF8:
					$msg = 'malformed UTF-8 characters, possibly incorrectly encoded';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_utf8','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_utf8','message' => $msg];
					}
					break;
				case JSON_ERROR_RECURSION:
					$msg = 'malformed one or more recursive references, possibly incorrectly encoded';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_recursion','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_recursion','message' => $msg];
					}
					break;
				case JSON_ERROR_INF_OR_NAN:
					$msg = 'malformed NAN or INF, possibly incorrectly encoded';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_inf_or_nan','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_inf_or_nan','message' => $msg];
					}
					break;
				case JSON_ERROR_UNSUPPORTED_TYPE:
					$msg = 'a value of a type that cannot be encoded was given';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_unsupported_type','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_unsupported_type','message' => $msg];
					}
					break;
				case JSON_ERROR_INVALID_PROPERTY_NAME:
					$msg = 'a property name that cannot be encoded was given';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_invalid_property_name','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_invalid_property_name','message' => $msg];
					}
					break;
				case JSON_ERROR_UTF16:
					$msg = 'malformed UTF-16 characters, possibly incorrectly encoded';
					if($lite){
						$data = ['status' => 'error','case' => 'json_error_utf16','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'json_error_utf16','message' => $msg];
					}
					break;
				default:
					$msg = 'unknown error';
					if($lite){
						$data = ['status' => 'error','case' => 'unknown','message' => $msg];
					} else {
						$data = ['data'=> $content,'status' => 'error','case' => 'unknown','message' => $msg];
					}
					break;
			}
			return $data;
		}

    }