<?php
/**
* @author paulz
* @created Mar 8, 2016
*/

if ( !defined('DS') ) {
	define('DS', DIRECTORY_SEPARATOR);
}

if ( !defined('SUPERADMIN_ID') ) {
	define('SUPERADMIN_ID', 1);
}

// Max milestone amount
if ( !defined('MAX_MILESTONE_AMOUNT') ) {
	define('MAX_MILESTONE_AMOUNT', 9999999);
}

// Max fixed price
if ( !defined('MAX_FIXED_PRICE') ) {
	define('MAX_FIXED_PRICE', 9999999);
}

// Max hourly price
if ( !defined('MAX_HOURLY_PRICE') ) {
	define('MAX_HOURLY_PRICE', 999);
}

if ( !function_exists('pr') ) {
	function pr($obj) {
		echo "<pre>";
		print_r($obj);
		echo "</pre>";
		exit;
	}
}

if ( !function_exists('get_ijobdesk_database_setting')) {
	function get_ijobdesk_database_setting()
	{
		$path = dirname(dirname(dirname(__FILE__))).DS."config".DS."database.ini";

		$ini = parse_ini_file($path, true);
		$active = $ini['env']['active'];
		if ( !isset($ini[$active]) ) {
			exit;
		}

		return $ini[$active];
	}
}

if ( !function_exists('isWindows') ) {
	function isWindows()
	{
		return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}
}

if ( !function_exists('getRoot') ) {
	function getRoot()
	{
		$root = dirname(dirname(dirname(__FILE__)));
		return $root;
	}
}

if ( !function_exists('getMimeType') ) {
	function getMimeType($filename)
	{
		preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);

		switch(strtolower($fileSuffix[1]))
		{
			case "js" :
			return "application/x-javascript";

			case "json" :
			return "application/json";

			case "jpg" :
			case "jpeg" :
			case "jpe" :
			return "image/jpg";

			case "png" :
			case "gif" :
			case "bmp" :
			case "tiff" :
			return "image/".strtolower($fileSuffix[1]);

			case "css" :
			return "text/css";

			case "xml" :
			return "application/xml";

			case "doc" :
			case "docx" :
			return "application/msword";

			case "xls" :
			case "xlsx" :
			case "xlt" :
			case "xlm" :
			case "xld" :
			case "xla" :
			case "xlc" :
			case "xlw" :
			case "xll" :
			return "application/vnd.ms-excel";

			case "ppt" :
			case "pps" :
			return "application/vnd.ms-powerpoint";

			case "rtf" :
			return "application/rtf";

			case "pdf" :
			return "application/pdf";

			case "html" :
			case "htm" :
			case "php" :
			return "text/html";

			case "txt" :
			return "text/plain";

			case "mpeg" :
			case "mpg" :
			case "mpe" :
			return "video/mpeg";

			case "mp3" :
			return "audio/mpeg3";

			case "wav" :
			return "audio/wav";

			case "aiff" :
			case "aif" :
			return "audio/aiff";

			case "avi" :
			return "video/msvideo";

			case "wmv" :
			return "video/x-ms-wmv";

			case "mov" :
			return "video/quicktime";

			case "zip" :
			return "application/zip";

			case "tar" :
			return "application/x-tar";

			case "swf" :
			return "application/x-shockwave-flash";

			default :

		}

		return "application/octet-stream";
	}
}

/**
 * Get FontAwesome Icon Class from filename.
 *
 * @param  string $filename
 * @return string
 */
if ( !function_exists("getFileIconClass") ) {
	function getFileIconClass($filename)
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$ext = strtolower($ext);

		switch ($ext) {
        // Code
			case "c": case "cpp": case "rb":
			$c = 'fa-file-code-o';
			break;

        // Word
			case "doc": case "docx":
			$c = 'fa-file-word-o';
			break;

        // Excel
			case "xls": case "xlsx":
			$c = 'fa-file-excel-o';
			break;

        // PowerPoint
			case "ppt": case "pptx":
			$c = 'fa-file-powerpoint-o';
			break;

        // PDF
			case "pdf":
			$c = 'fa-file-pdf-o';
			break;

        // Image
			case "jpg": case "png": case "bmp":
			case "jpeg": case "gif": case "psd":
			$c = 'fa-file-image-o';
			break;

        // Audio
			case "mp3": case "wma": case "wav":
			$c = 'fa-file-audio-o';
			break;

        // Video
			case "mp4": case "mpg": case "avi":
			case "vob":
            //$c = 'fa-file-video-o';
			$c = 'fa-video-camera';
			break;

        // Text
			case "txt": case "log":
			$c = 'fa-file-text-o';
			break;

        // Zip
			case "zip": case "7z": case "rar":
			$c = 'fa-file-zip-o';
			break;

			default:
			$c = 'fa-file-archive-o';
		}

		return $c;
	}
}


//////////////////////////////////////////////////////////////
if ( !function_exists('encodeChars') ) {
	function encodeChars($string) {
		return htmlentities($string);
	}
}

if ( !function_exists('validateEmail') ) {
	function validateEmail($email) {
		if ( !trim($email) ) {
			return false;
		}

		if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
			return false;
		}

		return true;
	}
}

if ( !function_exists('round2Decimal') ) {
	function round2Decimal($val) {
		return round((floatval($val) * 100) / 100, 2);
	}
}

/**
* Format currency like the following.
* $1000 to $1k
* @author Ro Un Nam
* @since May 21, 2017
*/
if ( !function_exists('formatEarned') ) {
	function formatEarned($val)
	{
		if ( $val < 1000 ) {
			return $val;
		}
		
		return number_format($val / 1000, 1, '.', ',') . 'k';
	}
}

/* Mar 16, 2016 - Ri Chol Min */
if ( !function_exists('formatCurrency') )
{
	function formatCurrency($val, $sign = false)
	{
		$val = ($val * 100) / 100;
		$val = number_format($val, 2, '.', ',');

		if ( $sign ) {
			$val = $sign . $val;
		}

		return $val;
	}
}

/* Apr 08, 2016 - Nada */
if ( !function_exists('priceRaw') )
{
	function priceRaw($amount)
	{
		$amount = str_replace(",", "", $amount);
		$amount = floatval($amount);
		$amount = round($amount, 2);

		return $amount;
	}
}

/* Mar 2, 2016 - paulz */
if ( !function_exists("siteProtocol") ) {
	function siteProtocol()
	{
		if ( (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 || strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == 'https' ) {
			$is_https = true;
		} else {
			$is_https = false;
		}

        //for CURL from mobile/ curl calls
		if (isset($_REQUEST['is_secure']))
		{
			$is_https = true;
		}
		return $is_https ? "https" : "http";
	}
}

/* Mar 2, 2016 - paulz */
if ( !function_exists("get_site_url") ) {
	function siteUrl($protocol = '')
	{
		if (!$protocol) {
			$protocol = siteProtocol();
		}

		return $protocol."://".$_SERVER['HTTP_HOST'];
	}
}

/**
 * @author KCG
 * @since July 13, 2017
 */
if ( !function_exists('getRedirectByRole') ) {
	function getRedirectByRole($user) {
		$redirect = 'admin.user.logout';

		if ($user->isFinancial())
			$redirect = 'admin.financial.dashboard';

		if ($user->isTicket())
			$redirect = 'admin.ticket.dashboard';
		
		if ($user->isSuper())
			$redirect = 'admin.super.dashboard';

		return $redirect;
	}
}