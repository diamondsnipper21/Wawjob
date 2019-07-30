<?php namespace iJobDesk\Http\Controllers\Admin\Super;

use iJobDesk\Http\Requests;
use Illuminate\Http\Request;
use iJobDesk\Http\Controllers\Admin\AccountController as BaseController;

use Config;

// Models
use iJobDesk\Models\User;
use Auth;

class AccountController extends BaseController {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->page_title = 'Settings';
        parent::__construct();
	}
}