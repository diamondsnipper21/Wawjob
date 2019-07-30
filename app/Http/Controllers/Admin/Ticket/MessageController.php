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
use iJobDesk\Models\AdminMessage;

class MessageController extends BaseController {

    public function __construct() {
        $this->page_title = 'Messages';
        parent::__construct();
    }

    /**
    * Show messages.
    *
    * @return Response
    */
    public function index(Request $request) {

        $action = $request->input('_action');

        if (!empty($action)) {
            if ($action == 'DELETE') {
                $ids = $request->input('id');
                AdminMessage::whereIn('id', $ids)
                            ->delete();

                add_message(sprintf('The %d messages(s) have been deleted successfully.', count($ids)), 'success');
            }
        }

        $sort     = $request->input('sort', 'admin_messages.created_at');
        $sort_dir = $request->input('sort_dir', 'desc');
        
        $messages = AdminMessage::getQueryBuilder()
                                ->orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        // By Sender or ID
        if (!empty($filter['sender'])) {
            $messages->where(function($query) use ($filter) {
                $query->orWhere('sender.fullname', 'LIKE', '%'.trim($filter['sender']).'%')
                      ->orWhere('sender.id', '=', $filter['sender']);
            });
        }

        // By Message
        if (!empty($filter['message'])) {
            $messages->where('admin_messages.message', 'LIKE', '%' . $filter['message'] . '%');
        }

        // By Type
        if (!empty($filter['type'])) {
            $messages->where('admin_messages.message_type', $filter['type']);
        }

        // By New
        // if (!empty($filter['is_new']) || $filter['is_new'] == "0") {
        //     $messages->whereRaw(AdminMessage::getColumnIsNew() . '=' . $filter['is_new']);
        // }

        // By Period
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $messages->where('admin_messages.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $messages->where('admin_messages.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        $request->flashOnly('filter');

        return view('pages.admin.ticket.messages', [
            'page' => 'ticket.messages',
            'messages' => $messages->paginate($this->per_page),
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
        ]);
    }

    public function read(Request $request, $id) {
        $message = AdminMessage::find($id);
        $message->markedAsRead();

        $messages = AdminMessage::getUnread();
        return json_encode(['count' => count($messages)]);
    }

    public function delete(Request $request, $id) {
        $message = AdminMessage::find($id);
        $message->delete();

        $messages = AdminMessage::getUnread();
        return json_encode(['count' => count($messages)]);
    }
}