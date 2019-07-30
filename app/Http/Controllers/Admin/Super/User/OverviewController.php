<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 11, 2017
 * User Overview Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use iJobDesk\Models\User;

use iJobDesk\Models\UserSecurityQuestion;

use iJobDesk\Models\Views\ViewUser;

class OverviewController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Overview';

        add_breadcrumb('Users', route('admin.super.users.list'));
        add_breadcrumb('Overview');

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * User Detail Overview
    * @param $user_id The identifier of User
    *
    * @return Response
    */
    public function index(Request $request, $user_id) {
        $user = ViewUser::find($user_id);

        if (!$user)
            abort(404);
        
        return view('pages.admin.super.user.'.strtolower(array_search($user->role, User::getOptions('role'))).'.overview', [
            'page' => 'super.user.commons.overview',
            'user' => $user
        ]);  
    }

    public function change_status(Request $request, $user_id) {
        $status = $request->input('status');

        $user = User::find($user_id);

        if (!$user)
            abort(404);

        $message = '';
        if ($status == User::STATUS_SUSPENDED) {
            $user->status               = User::STATUS_SUSPENDED;

            $message = 'User have been suspended successfully.';
        } else if ($status == User::STATUS_FINANCIAL_SUSPENDED) {
            $user->status               = User::STATUS_FINANCIAL_SUSPENDED;

            $message = 'User have been suspended financial successfully.';
        } elseif ($status == User::STATUS_AVAILABLE && ($user->status == User::STATUS_DELETED || $user->status == User::STATUS_SUSPENDED || $user->status == User::STATUS_FINANCIAL_SUSPENDED)) {
            if ($user->status == User::STATUS_DELETED) // if this user was deleted before, restore
                $user->restore();
            

            $user->status = User::STATUS_AVAILABLE;
            $message = 'User have been activated successfully.';
        } elseif ($status == User::STATUS_DELETED && $user->status != User::STATUS_DELETED) {
            $user->status = User::STATUS_DELETED;
            $message = 'User have been removed successfully.';
        } elseif ($status == 'RESET_SECURITY_ANSWER') {
            $security_question = UserSecurityQuestion::getUserSecurityQueston($user->id);

            if ($security_question)
                $security_question->delete();

            // $user->login_blocked = 0; // unblock

            $message = 'User have been reset security question successfully';
        }

        if ($status == User::STATUS_SUSPENDED || $status == User::STATUS_FINANCIAL_SUSPENDED)
            $user->is_auto_suspended = 0;
        else
            $user->is_auto_suspended = null;

        $user->save();

        if ($user->status == User::STATUS_DELETED) {
            $user->delete();
        }

        view()->share([
            'action' => 'change_user_status',
            'message' => $message
        ]);

        add_message($message, 'success');

        return $this->index($request, $user_id);
    }
}