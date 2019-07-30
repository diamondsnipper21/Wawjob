<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author KCG
 * @since July 28, 2017
 * Notification
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use Config;

use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Notification;

class NotificationController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Notifications';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Notifications');

        if ($request->method('post')) {

            $action = $request->input('_action');

            if ($action == 'CHANGE_STATUS') {
                $status = $request->input('select_action');
                $ids = $request->input('ids');
                
                if ($status == Notification::STATUS_DISABLE) {
                    Notification::whereIn('id', $ids)
                                 ->update(['status' => Notification::STATUS_DISABLE]);

                    add_message(sprintf('The %d Notification(s) has been disabled.', count($ids)), 'success');
                }
                elseif ($status == Notification::STATUS_ENABLE) {
                    Notification::whereIn('id', $ids)
                                 ->update(['status' => Notification::STATUS_ENABLE]);

                    add_message(sprintf('The %d Notification(s) has been enabled.', count($ids)), 'success');
                }
                elseif ($status == Notification::STATUS_DELETE) {
                    Notification::whereIn('id', $ids)
                                 ->delete();

                    add_message(sprintf('The %d Notification(s) has been deleted.', count($ids)), 'success');
                }
            }
        }

        $sort     = $request->input('sort', 'updated_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $notifications = Notification::orderBy($sort, $sort_dir)
                                        ->orderBy('slug', 'asc');

        // Filtering
        $filter = $request->input('filter');

        // By Slug
        if (!empty($filter['slug'])) {
            $notifications->where('slug', 'LIKE', '%'.trim($filter['slug']).'%');
        }

        // By Content
        if (!empty($filter['content'])) {
            $notifications->where('content', 'LIKE', '%'.trim($filter['content']).'%');
        }

        // By Status
        if ($filter['status'] != '') {
            $notifications->where('status', '=', $filter['status']);
        }

        // By Updated Date
        if (!empty($filter['updated_at'])) {
            if (!empty($filter['updated_at']['from'])) {
                $notifications->where('t.updated_at', '>=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['from'])));
            }

            if (!empty($filter['updated_at']['to'])) {
                $notifications->where('t.updated_at', '<=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['to']) + 24* 3600));
            }
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.notifications', [
            'page' => 'super.settings.notifications',
            'notifications' => $notifications->paginate($this->per_page),
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'config' => Config::get('settings'),
        ]);
    }

    public function edit(Request $request, $id = null) {

        $is_duplicate_slug = Notification::orderBy('slug', 'asc');

        if ($id) {
            $notification = Notification::find($id);
            $is_duplicate_slug = $is_duplicate_slug->where('id', '<>', $id);
        }
        else {
            $notification = new Notification();
        }

        $slug    = $request->input('slug');
        $content = $request->input('content');

        if ($request->isMethod('post') && $slug) {
            
            $is_duplicate_slug = $is_duplicate_slug->where('slug', $slug)
                                                    ->exists();

            if ( $is_duplicate_slug ) {
                add_message('The same slug is already exist.', 'danger');
            } else {
                $notification->slug    = $slug;
                $notification->content = encode_json_multilang($content);

                $notification->save();

                if (empty($id))
                    add_message('The new notification has been added successfully.', 'success');
                else
                    add_message('This notification has been updated successfully.', 'success');
            }
        }

        return response()->json(['alerts' => show_messages(true)]);
    }
}