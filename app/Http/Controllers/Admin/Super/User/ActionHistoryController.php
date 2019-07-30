<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 11, 2017
 * User Message Page
 */
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\ActionHistory;

class ActionHistoryController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Action History';

        add_breadcrumb('Users', route('admin.super.users.list'));

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * User Detail Overview
    * @param $id The identifier of User
    *
    * @return Response
    */
    public function index(Request $request, $id) {
        
        add_breadcrumb('Action History');

        $user = ViewUser::find($id);

        if (!$user)
            abort(404);

        $sort     = $request->input('sort', 'action_histories.created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $histories = ActionHistory::addSelect('action_histories.*')
                                  ->addSelect('du.fullname AS doer_fullname')
                                  ->addSelect('du.id AS doer_id')
                                  ->addSelect('tu.fullname AS user_fullname')
                                  ->addSelect('tu.id AS user_id')
                                  ->join('view_users AS du', 'du.id', '=', 'action_histories.doer_id')
                                  ->join('view_users AS tu', 'tu.id', '=', 'action_histories.target_id')
                                  ->where('tu.id', $id)
                                  ->where('action_histories.type', ActionHistory::TYPE_USER)
                                  ->orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        // By Action Type
        if (!empty($filter['type'])) {
            $histories->whereRaw("LOWER(action_type) LIKE '%" . strtolower(trim($filter['type'])) . "%'");
        }

        // By Message
        if (!empty($filter['description'])) {
            $histories->whereRaw("LOWER(description) LIKE '%" . strtolower(trim($filter['description'])) . "%'");
        }

        // By Doer
        if (!empty($filter['doer_fullname'])) {
            $histories->where(function($query) use ($filter) {
                if ( is_numeric($filter['doer_fullname']) ) {
                    $query->where('du.id', intval($filter['doer_fullname']));
                } else {
                    $query->whereRaw('LOWER(du.username) LIKE "%' . trim(strtolower($filter['doer_fullname'])) . '%"')
                            ->orWhereRaw('LOWER(du.fullname) LIKE "%' . trim(strtolower($filter['doer_fullname'])) . '%"');
                }
            });
        }

        // By Create At
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $histories->where('action_histories.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $histories->where('action_histories.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.user.commons.action_history', [
            'page' => 'super.user.commons.action_history',
            'user' => $user,
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,

            'histories'   => $histories->paginate($this->per_page)
        ]);
    }
}