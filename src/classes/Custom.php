<?php
/**
 * This is just example how to create Your own simple library class
 * This library is not used in everywhere so You can change or delete this class
 *
 */

namespace classes;

class Custom {

	/**
	 * File get contents in curl way
	 *
	 * @param $url = Address to get contents 
	 */
	function curl_get_contents($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}	

	/**
	* Replace any string and allow integer only without change the string itself
	*
	* @param $string = input string
	*/
	function integeronly($string)
	{
		$nn = preg_replace("/[^0-9]/", "", $string );
		return $nn;
	}

	/**
	* Determine limit for request more than 1000 row
	*
	* @param $itemsperpage = input how much items per page to show
	*/
	function IsBigData($itemsperpage)
	{
		$hasil = false;
		if($itemsperpage > 1000)
		{
			$hasil = true;
		}
		return $hasil;
	}

	/**
	* Convert encoded json data to prettyPrint in old way
	* This function is support for PHP below 5.4 as JSON_PRETTY_PRINT is not supported at all
	*
	* @param $json = input encoded data json here
	*/
	function prettyPrint( $json )
	{
    	$result = '';
    	$level = 0;
	    $in_quotes = false;
    	$in_escape = false;
	    $ends_line_level = NULL;
    	$json_length = strlen( $json );

	    for( $i = 0; $i < $json_length; $i++ ) {
    	    $char = $json[$i];
        	$new_line_level = NULL;
	        $post = "";
    	    if( $ends_line_level !== NULL ) {
        	    $new_line_level = $ends_line_level;
            	$ends_line_level = NULL;
	        }
    	    if ( $in_escape ) {
        	    $in_escape = false;
	        } else if( $char === '"' ) {
    	        $in_quotes = !$in_quotes;
        	} else if( ! $in_quotes ) {
	            switch( $char ) {
    	            case '}': case ']':
        	            $level--;
            	        $ends_line_level = NULL;
                	    $new_line_level = $level;
                    	break;

	                case '{': case '[':
    	                $level++;
        	        case ',':
            	        $ends_line_level = $level;
                	    break;

	                case ':':
    	                $post = " ";
        	            break;

            	    case " ": case "\t": case "\n": case "\r":
                	    $char = "";
                    	$ends_line_level = $new_line_level;
	                    $new_line_level = NULL;
    	                break;
        	    }
	        } else if ( $char === '\\' ) {
    	        $in_escape = true;
	        }
    	    if( $new_line_level !== NULL ) {
	            $result .= "\n".str_repeat( "\t", $new_line_level );
    	    }
	        $result .= $char.$post;
    	}

	    return $result;
	}

}