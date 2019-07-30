<?php namespace iJobDesk\Http\Controllers\Admin;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;

use Config;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use Auth;

class AccountController extends AdminController {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request) {
        $user = Auth::user();

        if ( !$user ) {
            return redirect()->route('admin.user.login');
        }

        $action = $request->input('_action');
        if ( $action == 'SAVE' ) {
            $user->email = $request->email;

            $old_password = $request->input('old_password');
            $new_password = $request->input('new_password');

            $credential = [
                'email' => $user->email,
                'password' => $old_password
            ];
            if (($new_password && $old_password && Auth::validate($credential)) || (!$new_password && !$old_password)) {
                // Save contact information
                $user->contact->first_name  = $request->first_name;
                $user->contact->last_name   = $request->last_name;
                $user->contact->timezone_id = $request->timezone;
                $user->contact->save();

                if ($new_password && $old_password)
                    $user->password = bcrypt($request->input('new_password'));
                
                $user->save();

                $user->updateLastActivity();
                add_message('Your account information have been changed successfully.', 'success' );
            } else {
                $user->try_password = $user->try_password + 1;
                if ( $user->try_password >= User::TOTAL_TRY_LOGINS ) {
                    $user->login_blocked = 1;
                }

                $user->save();

                if ( $user->try_password >= User::TOTAL_TRY_LOGINS ) {
                    Auth::logout();
                    add_message( trans('user.login.error_blocked_with_password'), 'danger' );
                    return redirect()->route('admin.user.login');
                }

                add_message( trans('user.change_password.error_mismatch_old_password'), 'danger' );                
            }
        }

        $request->flash();

        return view('pages.admin.commons.account.view', [
            'page' => 'commons.account.view',
            'user' => ViewUser::find($user->id),
            'error' => isset($error) ? $error : null
        ]);
    }
}