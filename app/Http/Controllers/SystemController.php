<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;
use Log;

class SystemController extends Controller {

	/**
	* Constructor
	*/
	public function __construct() {
		parent::__construct();
	}


	public function index(Request $request) {
		$user = Auth::user();

		$json = [
			'unread_ticket_messages' => view()->shared('unread_ticket_messages'),
			'unread_cnt' 		     => view()->shared('unread_cnt'),
			'unread_msg_count'       => view()->shared('unread_msg_count')
		];

		return response()->json($json);
	}
}