<?php namespace iJobDesk\Models;

use iJobDesk\Http\Requests;

use Auth;
use Config;
use Session;
use Exception;
use Log;

use PDF;

class PdfManager {
	/**
	* Constructor
	*/
	public function __construct() {
	}

    public static function generate($view, $filename, $data) {
    	$user = Auth::user();

		$filesystem = Config::get("filesystems.default");
		$upload_dir = getRoot() . '/' . get_upload_dir($user->id, 'pdf', $filesystem);
		createDir($upload_dir);

		try {
			$filepath = $upload_dir . '/' . $filename;
			$pdf = PDF::loadView('pdf.' . $view, $data);
			$pdf->save($filepath);

			if ( file_exists($filepath) )
				return true;
		} catch ( Exception $e ) {
			Log::error('[PdfManager::generate()] ' . $e->getMessage());
		}

		return false;
    }	
}