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
     * A class for validation in reSlim
     *
     * @package    Core reSlim
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2016 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/reSlim/blob/master/license.md  MIT License
     */
	class Validation {

        /**
		 * Sanitizer string and only accept integer
		 *
		 * @var string is the value
		 *
		 * @return string
		 */
        public static function integerOnly($string){
			return preg_replace("/[^0-9]/", "", $string);
		}
		
		/**
		 * Sanitizer string and only accept numeric
		 *
		 * @var string is the value
		 *
		 * @return string
		 */
		public static function numericOnly($string){
			return preg_replace("/[^0-9.-]/", "", $string);
		}
		
		/**
		 * Sanitizer string and only accept numeric abs
		 *
		 * @var string is the value
		 *
		 * @return string
		 */
		public static function numericAbsOnly($string){
			return preg_replace("/[^0-9.]/", "", $string);
		}

		/**
		 * Determine if string is numeric
		 *
		 * @var string is the value
		 *
		 * @return bool
		 */
		public static function isNumeric($string){
			return is_numeric($string);
		}

		/**
		 * Determine if string is decimal
		 *
		 * @var string is the value
		 *
		 * @return bool
		 */
		public static function isDecimal($string){
			if (is_numeric($string)) return 1;
			return is_numeric($string) && floor($string) != $string;
		}

		/**
		 * Determine if string is decimal only
		 *
		 * @var string is the value
		 *
		 * @return bool
		 */
		public static function isDecimalOnly($string){
			return is_numeric($string) && floor($string) != $string;
		}

		/**
		 * To determine if string is blank
		 * @param string is the value
		 * @return bool
		 */
		public static function isBlank($string){
			return (empty($string) || ctype_space($string));
		}

    }