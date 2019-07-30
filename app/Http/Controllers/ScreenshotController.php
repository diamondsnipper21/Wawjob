<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;

use Auth;
use Config;
use Storage;
use Log;

use iJobDesk\Models\Contract;

class ScreenshotController extends Controller {
	
	public function get(Request $request, $hash) {
		$me = Auth::user();

		if (!$me)
			abort(404);

		try {
			$decrypted_hash = decrypt_string($hash);

			if (!$decrypted_hash)
				abort(404);

			$info = explode_bracket($decrypted_hash);

			if (!$info || count($info) != 3)
				abort(404);

			$contract_id = $info[0];
			$datetime    = $info[1];
			$is_thumbnail= $info[2];

			if (!is_numeric($contract_id) || !is_numeric($datetime) || !is_numeric($is_thumbnail))
				abort(404);

			if (strlen($datetime) != 12)
				abort(404);

			$contract = Contract::find($contract_id);

			if (!$contract)
				abort(404);

			if (!$me->isSuper() && ($contract->buyer_id != $me->id || ($contract->isClosed() && $contract->buyer_id == $me->id)) && $contract->contractor_id != $me->id)
				abort(404);

			$type = $is_thumbnail == 1?'thumbnail':'full';

			$path = get_screenshot_path($contract_id, $datetime, $type);

			if (!file_exists($path))
				abort(404);

			header('Cache-Control: max-age=86400');
			header('Content-Type: image/jpeg');
			header('Content-Length: ' . filesize($path));

			readfile($path);
		} catch ( Exception $e ) {
			abort(404);
		}
		exit;
	}

	public function upload(Request $request) {
	}

	/**
	 * Delete uploaded screenshots.
	 */
	public function delete(Request $request, $hash) {
	}
}