<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;

use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\UserAnalytic;

class AccessHistoryController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Access History';

        add_breadcrumb('Users', route('admin.super.users.list'));

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request, $id) {
        
        add_breadcrumb('Access History');

        $user = ViewUser::find($id);

        if (!$user)
            abort(404);

        $histories = UserAnalytic::where('user_id', $id)
        						->orderBy('logged_at', 'desc');

        return view('pages.admin.super.user.commons.access_history', [
            'page' => 'super.user.commons.access_history',
            'user' => $user,
            'histories'   => $histories->paginate($this->per_page)
        ]);
    }
}