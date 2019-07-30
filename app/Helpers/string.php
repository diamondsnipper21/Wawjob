<?php

use Illuminate\Support\Facades\Crypt;
use iJobDesk\Models\Country;

if ( !function_exists('array_search_include') ) {
	function array_search_include($str, $array) {
        foreach ($array as $key => $a) {
            if (in_array("$str", explode(',', $a)))
                return $key;
        }

        return '';
	}
}

if ( !function_exists('explode_bracket') ) {
	function explode_bracket($str) {
		preg_match_all("/(\\[[0-9]+\\])/", $str, $matches);

		$array = [];
		foreach ($matches[0] as $val) {
			$array[] = substr($val, 1, strlen($val) - 2);
		}

		return $array;
	}
}

if ( !function_exists('implode_bracket') ) {
	function implode_bracket($array) {
		$str = '';

		if ($array) {
			foreach ($array as $a) {
				$str .= "[$a]";
			}
		}

		return $str;
	}
}

if ( !function_exists('encrypt_string') ) {
	function encrypt_string($string) {
		// return $pure_string;
	    // $encrypted_string = Crypt::encryptString($pure_string);
	    
	    // $encrypted_string = base64_encode($encrypted_string);
	    // $encrypted_string = str_replace(array('+', '/'), array('-', '_'), $encrypted_string);

		$j = 0;
		$hash = '';

	    $key = sha1(config('app.key'));
	    $strLen = strlen($string);
	    $keyLen = strlen($key);
	    for ( $i = 0; $i < $strLen; $i++ ) {
	        $ordStr = ord(substr($string, $i, 1));
	        if ($j == $keyLen) {
	        	$j = 0;
	        }

	        $ordKey = ord(substr($key, $j, 1));

	        $j++;
	        $hash .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
	    }

	    return base64_encode($hash);
	}
}

if ( !function_exists('decrypt_string') ) {
	function decrypt_string($string) {
		// return $encrypted_string;
		// $encrypted_string = str_replace(array('-', '_'), array('+', '/'), $encrypted_string);
		// $encrypted_string = base64_decode($encrypted_string);
		
	 	//    $decrypted_string = Crypt::decryptString($encrypted_string);
		
		$j = 0;
		$hash = '';

		$string = base64_decode($string);

		$key = sha1(config('app.key'));
		$strLen = strlen($string);
		$keyLen = strlen($key);
		for ( $i = 0; $i < $strLen; $i+=2 ) {
		    $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)), 36, 16));
		    if ($j == $keyLen) {
		    	$j = 0;
		    }

		    $ordKey = ord(substr($key, $j, 1));
		    $j++;
		    $hash .= chr($ordStr - $ordKey);
		}

		return $hash;
	}
}

if ( !function_exists('render_more_less_desc') ) {
	function render_more_less_desc($desc, $less_length = 300, $strip_new_line = false, $cuttable = false) {
		$desc = strip_tags($desc);

		for ($i = 0; $i < 5; $i++)
			$desc = str_replace("\r\n\r\n\r\n", "\r\n", $desc); // remove multiple lines.

		if ($strip_new_line)
			$desc = str_replace(array("\r", "\n"), '', $desc);

        if (mb_strlen($desc) > $less_length) {
        	$html = nl2br(mb_substr($desc, 0, $less_length)) . '<span class="three-pointer"> ... </span>';
        	if ($cuttable)
        		return $html;

            return $html . '<a href="#" class="more-desc">More</a><span style="display: none">' . nl2br(mb_substr($desc, $less_length)) . ' <a href="#" class="less-desc">Less</a></span>';
        }
        else
            return nl2br($desc);
	}
}

if ( !function_exists('render_block_ui_default_html') ) {
	function render_block_ui_default_html() {
		return '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
	}
}

/**
 * @author KCG
 * @since July 03, 2017
 */
if ( !function_exists('render_pagination_desc') ) {
    function render_pagination_desc($trans, $pagination) {
        $start  = ($pagination->currentPage() - 1) * $pagination->perPage() + 1;
        $end    = min($pagination->total(), $pagination->currentPage() * $pagination->perPage());
        $totals = $pagination->total();

        if ( $start > $totals ) {
        	return '';
        }

        if ($totals == 0)
            return "";

        return '<div class="show-num">'.trans($trans, ['from' => $start, 'to' => $end, 'total' => $totals]).'</div>';
    }
}

/**
 * @author KCG
 * @since July 20, 2018
 */
if ( !function_exists('generate_unique_id') ) {
    function generate_unique_id($id) {
    	$unique_id = str_random(20);
    	$unique_id = substr($unique_id, 0, 13) . ($id + 71) . substr($unique_id, 13);

    	return $unique_id;
    }
}
/**
 * @author KCG
 * @since Aug 13, 2018
 */
if ( !function_exists('fullphone') ) {
    function fullphone($phone, $country_code) {
    	$country = Country::getCountryByCode($country_code);
    	$phone_prefix = $country->country_code;

    	return '+' . $phone_prefix . ' ' . $phone;
    }
}