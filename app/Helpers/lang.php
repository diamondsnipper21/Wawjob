<?php

// use iJobDesk\Models\Notification;

if ( !function_exists('parse_multilang') ) {
	function parse_multilang($lang, $code = '') {
		if (empty($code)) {
			$code = App::getLocale();
		}

		$code = strtoupper($code);
		$lang = str_replace('<' . strtolower($code) . '>', '<' . $code . '>', $lang);
		$lang = str_replace('</' . strtolower($code) . '>', '</' . $code . '>', $lang);

		preg_match('/<' . $code . '>([^<>]+)<\/' . $code .'>/', $lang, $result );
		if (isset($result[1])){
			return $result[1];
		} else {
			$lang = str_replace('<en>', '<EN>', $lang);
			$lang = str_replace('</en>', '</EN>', $lang);
			preg_match('/<EN>([^<>]+)<\/EN>/', $lang, $en_result);

			if (isset($en_result[1])) {
			  	return $en_result[1];
			} else {
			  	return $lang;    
			}   
		}
	}
}

if ( !function_exists('encode_multilang') ) {
    function encode_multilang($data) {
        $encoded_data = '';
        foreach ($data as $lang => $value) {
            $encoded_data .= "<$lang>".htmlspecialchars($value)."</$lang>";
        }

        return $encoded_data;
    }
}

if ( !function_exists('parse_json_multilang') ) {
    function parse_json_multilang($data, $lang = null) {
        $decoded_data = json_decode($data, true);

        if (!$lang)
            $lang = App::getLocale();

         if (!empty($decoded_data[strtoupper($lang)]))
            return $decoded_data[strtoupper($lang)];
        elseif (!empty($decoded_data[strtolower($lang)]))
            return $decoded_data[strtolower($lang)];

        return '';
    }
}

if ( !function_exists('encode_json_multilang') ) {
    function encode_json_multilang($data) {
        return json_encode($data);
    }
}

/*
if ( !function_exists('get_all_lang_keys') ) {
	function get_all_lang_keys() {
		$lang_dir = getRoot() . '/resources/lang/en';
        $lang_files = scandir($lang_dir);

        $lang_text = '';

        if ( !empty($lang_files) ) {
        	$lang_files = array_diff($lang_files, array('..', '.'));
        	foreach ( $lang_files as $file ) {
        		$lang_keys = include $lang_dir . '/' . $file;

        		$lang_text .= "\n\n--------------- " . $file . " ----------------\n\n";

        		foreach ( $lang_keys as $value ) {
        			if ( is_array($value) ) {
        				foreach ( $value as $value2 ) {
		        			if ( is_array($value2) ) {
		        				foreach ( $value2 as $value3 ) {
				        			if ( is_array($value3) ) {
				        				foreach ( $value3 as $value4 ) {
						        			if ( is_array($value4) ) {
						        				foreach ( $value4 as $value5 ) {
					        						$lang_text .= $value5 . "\n";
					        					}
					        				} else {
					        					$lang_text .= $value4 . "\n";
					        				}
			        					}
			        				} else {
			        					$lang_text .= $value3 . "\n";
			        				}
	        					}
	        				} else {
	        					$lang_text .= $value2 . "\n";
	        				}
	        			}
        			} else {
        				$lang_text .= $value . "\n";
        			}
        		}

        		$lang_text .= "\n";
        	}
        }

        File::put(storage_path('/logs/lang.txt'), $lang_text);
	}

	function get_all_notification_keys() {
		$notifications = Notification::all();

		$lang_text = '';
		foreach ( $notifications as $notify ) {
			$lang_text .= parse_json_multilang($notify->content, 'en') . "\n";
		}

		File::put(storage_path('/logs/notifications.txt'), $lang_text);
	}
}
*/