<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author PYH
 * @since December 26, 2017
 * Settings Fees Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use Config;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Cronjob;
use iJobDesk\Models\Settings;

class UserPointController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'User Points';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Show Settings Fees page.
    *
    * @return Response
    */
    public function index(Request $request) {
        add_breadcrumb('User Points');

        $user = Auth::user();
    	if ( !$user->isSuper() ) {
    		abort(404);
    	}
    	
        if ( $request->method('post') ) {
            $action = $request->input('_action');

            if ( $action == 'SAVE' ) {
                $keys = [
                    'point_portrait',
                    'point_portrait_enabled',
                    'point_portfolio',
                    'point_portfolio_enabled',
                    'point_certification',
                    'point_certification_enabled',
                    'point_employment_history',
                    'point_employment_history_enabled',
                    'point_education',
                    'point_education_enabled',
                    'point_id_verified',
                    'point_id_verified_enabled',
                    'point_new_freelancer',
                    'point_new_freelancer_enabled',
                    'point_open_jobs',
                    'point_open_jobs_enabled',
                    'point_last_12months',
                    'point_last_12months_enabled',
                    'point_lifetime',
                    'point_lifetime_enabled',
                    'point_activity',
                    'point_activity_enabled',
                    'point_dispute',
                    'point_dispute_enabled',
                    'point_score_per_dollar',
                    'point_score_non_review',
                ];

                $updated = true;
                foreach ( $keys as $key ) {
                    if ( !Settings::updateSetting(strtoupper($key), $request->input($key)) ) {
                        $updated = false;
                    }
                }

                if ( $updated ) {
                    add_message('Your settings has been updated successfully.', 'success');

                    Cronjob::where('type', Cronjob::TYPE_PROCESS_USER_POINTS)
                            ->update(['status' => Cronjob::STATUS_READY]);

                    return redirect()->route('admin.super.settings.user_points');
                } else {
                    add_message('Your settings has not been updated successfully.', 'danger');
                }
            }
        }

        return view('pages.admin.super.settings.user_points', [
            'page' => 'super.settings.user_points'
        ]);
    }
}