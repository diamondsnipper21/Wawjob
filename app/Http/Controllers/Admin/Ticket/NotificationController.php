<?php namespace iJobDesk\Http\Controllers\Admin\Ticket;
/**
 * @author KCG
 * @since June 28, 2017
 * Notifications Listing Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\Notification;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\Ticket;

class NotificationController extends BaseController {

    public function __construct() {
        $this->page_title = 'Notifications';
        parent::__construct();
    }

    /**
    * Show notifications.
    *
    * @return Response
    */
    public function index(Request $request) {

        $action = $request->input('_action');

        if (!empty($action)) {
            $ids    = $request->input('id');
            $status = $request->input('status');

            if ($status == 'DELETE') {
                UserNotification::whereIn('id', $ids)
                                ->delete();

                add_message(sprintf('The %d Notification(s) have been deleted successfully.', count($ids)), 'success');
            } elseif ($status == 'READ') {
                UserNotification::whereIn('id', $ids)
                                ->update(['read_at' => date('Y-m-d- H:i:s')]);

                add_message(sprintf('The %d Notification(s) have been marked as read.', count($ids)), 'success');
            } elseif ($status == 'UNREAD') {
                UserNotification::whereIn('id', $ids)
                                ->update(['read_at' => null]);

                add_message(sprintf('The %d Notification(s) have been marked as uread.', count($ids)), 'success');
            }

            $this->beforeAction($request);
        }

        $sort     = $request->input('sort', 'notified_at');
        $sort_dir = $request->input('sort_dir', 'desc');
        
        $notifications = UserNotification::getAll($this->auth_user->id, null, false, true);
        $notifications->orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        // By Priority
        if (isset($filter) && $filter['priority'] != '') {
            $notifications->where('priority', $filter['priority']);
        }

        // By Period
        if (!empty($filter['notified_at'])) {
            if (!empty($filter['notified_at']['from'])) {
                $notifications->where('notified_at', '>=', date('Y-m-d H:i:s', strtotime($filter['notified_at']['from'])));
            }

            if (!empty($filter['notified_at']['to'])) {
                $notifications->where('notified_at', '<=', date('Y-m-d H:i:s', strtotime($filter['notified_at']['to']) + 24* 3600));
            }
        }

        $request->flashOnly('filter');

        return view('pages.admin.ticket.notifications', [
            'page' => 'ticket.notifications',
            'notifications' => $notifications->paginate($this->per_page),
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
        ]);
    }

    public function read(Request $request, $id) {
        $notification = UserNotification::find($id);
        $notification->read_at = date('Y-m-d H:i:s');
        $notification->save();

        $notifications = UserNotification::getUnread($this->auth_user->id);;
        return json_encode(['count' => count($notifications)]);
    }

    public function delete(Request $request, $id) {
        $notification = UserNotification::where('id', $id)
                                        ->delete();

        $notifications = UserNotification::getUnread($this->auth_user->id);;
        return json_encode(['count' => count($notifications)]);
    }
}