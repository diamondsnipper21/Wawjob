<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since July 11, 2017
 * User Message Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;

use iJobDesk\Models\User;
use iJobDesk\Models\Message;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;
use iJobDesk\Models\Reason;

use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Views\ViewProjectMessage;

class MessageController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Messages';

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
        
        add_breadcrumb('Messages');

        $user = ViewUser::find($id);

        if (!$user)
            abort(404);

        $action = $request->input('_action');
        if (!empty($action)) {
            $ids = $request->input('ids');
            
            $threads = ProjectMessageThread::whereIn('id', $ids);

            if ($action =='DELETE') {
                $threads->delete($ids);

                add_message(sprintf('The %d Message(s) has been deleted.', count($ids)), 'success');
            }

            $admin = Auth::user();
            // Add Reason
            foreach ($ids as $_id) {
                $reason = new Reason();
                $reason->message    = $request->input('reason');
                $reason->admin_id   = $admin->id;
                $reason->type       = Reason::TYPE_MESSAGE_THREAD;
                $reason->affected_id= $_id;
                $reason->action     = Reason::ACTION_DELETE;

                $reason->save();
            }
        }

        $sort     = $request->input('sort', 'thread_created_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $threads = ViewProjectMessage::addSelect('*')
                                      ->addSelect('thread_created_at')
                                      ->addSelect(DB::raw('MAX(created_at) AS last_reply_date'))

                                      ->whereRaw("(freelancer_id = $id OR buyer_id = $id)")
                                      ->whereRaw("sender_id = $id")

                                      ->groupBy('thread_id')
                                      ->orderBy($sort, $sort_dir);
        // Filtering
        $filter = $request->input('filter');

        // By Freelancer
        if (!empty($filter['freelancer_name'])) {
            $threads->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(freelancer_name) LIKE "%'.trim(strtolower($filter['freelancer_name'])).'%"')
                      ->orWhere('freelancer_id', '=', $filter['freelancer_name']);
            });
        }

        // By Buyer
        if (!empty($filter['buyer_name'])) {
            $threads->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(buyer_name) LIKE "%'.trim(strtolower($filter['buyer_name'])).'%"')
                      ->orWhere('buyer_id', '=', $filter['buyer_name']);
            });
        }

        // By Job Posting
        if (!empty($filter['job_posting'])) {
            $threads->where('job_posting', 'LIKE', '%'.$filter['job_posting'].'%');
        }

        // By Related Job
        if (!empty($filter['related_job'])) {
            $threads->where('related_job', 'LIKE','%'.$filter['related_job'].'%');
        }

        // By Created At
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $threads->where('thread_created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $threads->where('thread_created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // By Project Title
        if (!empty($filter['last_reply_date'])) {
            if (!empty($filter['last_reply_date']['from'])) {
                $threads->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['last_reply_date']['from'])));
            }

            if (!empty($filter['last_reply_date']['to'])) {
                $threads->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['last_reply_date']['to']) + 24* 3600));
            }
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.user.commons.messages', [
            'page' => 'super.user.commons.messages',
            'user' => $user,
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,

            'threads'   => $threads->paginate($this->per_page)
        ]);
    }

    public function thread(Request $request, $user_id, $thread_id, $sent_message = false) {
        
        add_breadcrumb('Messages', route('admin.super.user.messages', ['id' => $user_id]));
        add_breadcrumb('Message');

        $user = ViewUser::find($user_id);

        if (!$user)
            abort(404);

        $thread = ViewProjectMessage::where('thread_id', '=', $thread_id)
                                    ->groupBy('thread_id')
                                    ->first();

        if (!$thread)
            abort(404);

        $query_builder = ProjectMessage::where('thread_id', $thread_id)
                                       ->orderBy('created_at', 'DESC');

        $message_limit = $request->input('_limit', 0);

        // if created new message, show more record.
        if ($sent_message)
            $message_limit++;
        else
            $message_limit += Message::PER_PAGE;

        $messages = clone $query_builder;
        $messages = $messages->paginate($message_limit);

        $message_count = $query_builder->count();
        
        return view('pages.admin.super.user.commons.message.thread', [
            'page' => 'super.user.commons.message.thread',
            'user' => $user,
            'thread'    => $thread,

            'message_count' => $message_count,
            'message_limit' => $message_limit,
            'messages'      => $messages,
            'sent_message'  => $sent_message,
        ]);
    }
}