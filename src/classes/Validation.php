<?php
/**
 * This class is a part of reSlim project
 * @author M ABD AZIZ ALFIAN <github.com/aalfiann>
 *
 * Don't remove this class unless You know what to do
 *
 */
namespace classes;
use \classes\Validation as Validation;
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
		 * @var $string
		 *
		 * @return string
		 */
        public static function integerOnly($string)
	    {
		    $nn = preg_replace("/[^0-9]/", "", $string );
    		return $nn;
	    }

    }