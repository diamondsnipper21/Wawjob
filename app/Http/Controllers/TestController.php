<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use Config;
use DB;
use Validator;

class TestController extends Controller {

	public function index(Request $request) {
		return view('pages.test.index', [
		]);
	}
}