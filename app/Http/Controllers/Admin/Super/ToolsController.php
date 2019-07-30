<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author KCG
 * @since Apr 28, 2018
 * User Management for Super Manager
 */
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;

class ToolsController extends BaseController {

	/**
	 * Log viewer
	 */
	public function log_viewer(Request $request) {
		$viewer = new \Rap2hpoutre\LaravelLogViewer\LogViewerController();

		view()->share([
			'page' => 'super.tools.log_viewer'
		]);

		return $viewer->index();
	}
}