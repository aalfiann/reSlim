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
		 *
		 * @return string
		 */
        public static function encode($data,$withlog=false,$pretty=false){
			if($withlog && is_array($data)) $data['logger'] = ['timestamp' => date('Y-m-d H:i:s', time()),'uniqid'=>uniqid()];
			if ($pretty){
				return json_encode($data,JSON_PRETTY_PRINT);
			}
			return json_encode($data);
		}

		/**
		 * Safest way to encode Array string or value to json (safe but slower because conversion)
		 * When the time to use this function: If you want to display json data which is retrieve from database that maybe contains invalid utf8 chars.
		 *
		 * @var data is the array string or value
		 * @var withlog if set to true then will append logger data. Default is false.
		 * @var pretty is to make ouput json nice, clean and readable. Default is false for perfomance speed reason.
		 * @var ansii if set to true then conversion to utf8 is more reliable because will work for ANSII chars. Default is set to false for performance reason.
		 *
		 * @return string
		 */
        public static function safeEncode($data,$withlog=false,$pretty=false,$ansii=false){
			if($withlog && is_array($data)) $data['logger'] = ['timestamp' => date('Y-m-d H:i:s', time()),'uniqid'=>uniqid()];
			if ($pretty){
				return json_encode((($ansii)?self::safeConvertToUTF8($data):self::convertToUTF8($data)),JSON_PRETTY_PRINT);
			}
			return json_encode((($ansii)?self::safeConvertToUTF8($data):self::convertToUTF8($data)));
		}

		/**
		 * Decode json string (if fail will return null)
		 *
		 * @var json is the json string
		 *
		 * @return array stdClass
		 */
		public static function decode($json,$array=false){
			return json_decode($json,$array);
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
		 * @var simple if set to true will only show status and message. Default is false.
		 *
		 * @return string json output formatted
		 */
		public static function debug_encode($string,$simple=false){
			json_encode($string,JSON_UNESCAPED_UNICODE);

			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$msg = 'no errors found';
					if($simple){
						$data = ['status' => 'success','case' => 'json_error_none','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'success','case' => 'json_error_none','message' => $msg];
					}
					break;
				case JSON_ERROR_DEPTH:
					$msg = 'maximum stack depth exceeded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_depth','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_depth','message' => $msg];
					}
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$msg = 'underflow or the modes mismatch';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_state_mismatch','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_state_mismatch','message' => $msg];
					}
					break;
				case JSON_ERROR_CTRL_CHAR:
					$msg = 'unexpected control character found';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_ctrl_char','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_ctrl_char','message' => $msg];
					}
					break;
				case JSON_ERROR_SYNTAX:
					$msg = 'syntax error, malformed json';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_syntax','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_syntax','message' => $msg];
					}
					break;
				case JSON_ERROR_UTF8:
					$msg = 'malformed UTF-8 characters, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_utf8','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_utf8','message' => $msg];
					}
					break;
				case JSON_ERROR_RECURSION:
					$msg = 'malformed one or more recursive references, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_recursion','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_recursion','message' => $msg];
					}
					break;
				case JSON_ERROR_INF_OR_NAN:
					$msg = 'malformed NAN or INF, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_inf_or_nan','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_inf_or_nan','message' => $msg];
					}
					break;
				case JSON_ERROR_UNSUPPORTED_TYPE:
					$msg = 'a value of a type that cannot be encoded was given';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_unsupported_type','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_unsupported_type','message' => $msg];
					}
					break;
				case JSON_ERROR_INVALID_PROPERTY_NAME:
					$msg = 'a property name that cannot be encoded was given';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_invalid_property_name','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_invalid_property_name','message' => $msg];
					}
					break;
				case JSON_ERROR_UTF16:
					$msg = 'malformed UTF-16 characters, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_utf16','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'json_error_utf16','message' => $msg];
					}
					break;
				default:
					$msg = 'unknown error';
					if($simple){
						$data = ['status' => 'error','case' => 'unknown','message' => $msg];
					} else {
						$data = ['data'=> $string,'status' => 'error','case' => 'unknown','message' => $msg];
					}
					break;
			}
			return json_encode($data,JSON_PRETTY_PRINT);
		}
		
		/**
		 * Debugger to test json decode
		 *
		 * @var json is the json string
		 * @var simple if set to true will only show status and message. Default is false.
		 *
		 * @return string json output formatted
		 */
		public static function debug_decode($json,$simple=false){
			$test = json_decode($json);
			if (!empty($test)){
				$json = $test;
			}
			$data = array();
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$msg = 'no errors found';
					if($simple){
						$data = ['status' => 'success','case' => 'json_error_none','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'success','case' => 'json_error_none','message' => $msg];
					}
					break;
				case JSON_ERROR_DEPTH:
					$msg = 'maximum stack depth exceeded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_depth','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_depth','message' => $msg];
					}
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$msg = 'underflow or the modes mismatch';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_state_mismatch','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_state_mismatch','message' => $msg];
					}
					break;
				case JSON_ERROR_CTRL_CHAR:
					$msg = 'unexpected control character found';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_ctrl_char','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_ctrl_char','message' => $msg];
					}
					break;
				case JSON_ERROR_SYNTAX:
					$msg = 'syntax error, malformed json';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_syntax','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_syntax','message' => $msg];
					}
					break;
				case JSON_ERROR_UTF8:
					$msg = 'malformed UTF-8 characters, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_utf8','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_utf8','message' => $msg];
					}
					break;
				case JSON_ERROR_RECURSION:
					$msg = 'malformed one or more recursive references, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_recursion','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_recursion','message' => $msg];
					}
					break;
				case JSON_ERROR_INF_OR_NAN:
					$msg = 'malformed NAN or INF, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_inf_or_nan','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_inf_or_nan','message' => $msg];
					}
					break;
				case JSON_ERROR_UNSUPPORTED_TYPE:
					$msg = 'a value of a type that cannot be encoded was given';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_unsupported_type','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_unsupported_type','message' => $msg];
					}
					break;
				case JSON_ERROR_INVALID_PROPERTY_NAME:
					$msg = 'a property name that cannot be encoded was given';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_invalid_property_name','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_invalid_property_name','message' => $msg];
					}
					break;
				case JSON_ERROR_UTF16:
					$msg = 'malformed UTF-16 characters, possibly incorrectly encoded';
					if($simple){
						$data = ['status' => 'error','case' => 'json_error_utf16','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'json_error_utf16','message' => $msg];
					}
					break;
				default:
					$msg = 'unknown error';
					if($simple){
						$data = ['status' => 'error','case' => 'unknown','message' => $msg];
					} else {
						$data = ['data'=> $json,'status' => 'error','case' => 'unknown','message' => $msg];
					}
					break;
			}
			return json_encode($data,JSON_PRETTY_PRINT);
		}

    }