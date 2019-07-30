<?php namespace iJobDesk\Http\Controllers\Admin\Ticket;
/**
 * @author KCG
 * @since June 30, 2017
 * ToDo Listing Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use iJobDesk\Models\User;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\Todo;
use iJobDesk\Models\Views\ViewTodo;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\TodoFile;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\File;
use iJobDesk\Models\Message;

class TodoController extends BaseController {

    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);
        
        $this->page_title = 'TODOs';

        $user = Auth::user();

        // admin names
        $admins = [];
        foreach (User::getAdminUsers() as $admin) {
            $fullname = $admin->fullname();
            
            if (empty($fullname))
                $fullname = $admin->username;

            if (!$user->isSuper() && $user->id == $admin->id)
                continue;

            $admins[] = ['name' => $fullname, 'role' => $admin->role, 'id' => $admin->id, 'user' => $admin];
        }

        view()->share([
            'admins' => $admins
        ]);
    }

    /**
    * Show todo listing.
    *
    * @return Response
    */
    public function index(Request $request, $tab = 'opening') {

        // Add Breadcrumb
        add_breadcrumb('TODOs');

        $user = Auth::user();

        // sort
        $sort     = $request->input('sort', 'unread_message_count');
        $sort_dir = $request->input('sort_dir', 'desc');
        
        $todos = ViewTodo::getAvailable()
                         ->addSelect('*')
                         ->addSelect(DB::raw("(".AdminMessage::getColumnNewCount().") AS unread_message_count"))
                         ->orderBy($sort, $sort_dir)
                         ->orderBy('id', 'desc');

        $filter = $request->input('filter');

        // By Type
        if (!empty($filter['id'])) {
            $todos->where('id', $filter['id']);
        }

        // By Type
        if (!empty($filter['type'])) {
            $todos->where('type', $filter['type']);
        }

        // By Priority
        if (!empty($filter['priority'])) {
            $todos->where('priority', $filter['priority']);
        }

        // By Subject
        if (!empty($filter['subject'])) {
            $todos->where('subject', 'LIKE', '%'.trim($filter['subject']).'%');
        }

        // By creators
        if (!empty($filter['creator'])) {
            $todos->where('creator_id', 'LIKE', '%'.trim($filter['creator']).'%');
        }

        // By Assigners
        if (!empty($filter['assigner_names'])) {
            $todos->where('assigner_ids', 'LIKE', '%['.trim($filter['assigner_names']).']%');
        }

        // By Status
        if (!empty($filter['status'])) {
            $todos->where('status', $filter['status']);
        }

        // By Due Date
        if (!empty($filter['due_date'])) {
            if (!empty($filter['due_date']['from'])) {
                $todos->where('due_date', '>=', $filter['due_date']['from']);
            }

            if (!empty($filter['due_date']['to'])) {
                $todos->where('due_date', '<=', $filter['due_date']['to']);
            }
        }

        // By Create Time
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $todos->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $todos->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // Show only my todos
        if (!empty($filter['show_only_mine'])) {
            $todos->where(function($query) use ($user) {
                $query->orWhere('assigner_ids', 'LIKE', '%['.$user->id.']%');
                $query->orWhere('creator_id', $user->id);
            });
        }

        $action = $request->input('_action');
        // change status
        if ($action == 'CHANGE_STATUS') {
            $status = $request->input('status');
            $ids = $request->input('ids');

            foreach ($ids as $id) {
                $todo = Todo::find($id);
                $todo->status = $status;

                $todo->save();
            }
            
            if ($status == Todo::STATUS_COMPLETE) {
                add_message(sprintf('The %d Todo(s) has been completed.', count($ids)), 'success');
            } elseif ($status == Todo::STATUS_CANCEL) {
                add_message(sprintf('The %d Todo(s) has been cancelled.', count($ids)), 'success');
            } elseif ($status == Todo::STATUS_OPEN) {
                add_message(sprintf('The %d Todo(s) has been re-opended.', count($ids)), 'success');
            }
        }

        if ($tab == 'opening') {
            $todos->whereIn('status', [Todo::STATUS_OPEN]);
        } else {
            $todos->whereNotIn('status', [Todo::STATUS_OPEN]);
        }

        // assing to filtering options name 
        $all_todos    = $todos->get();
        $creator_ids  = [];
        $assinger_ids = [];
        $creators     = [];
        $assigners    = [];

        foreach ($all_todos as $key => $todo) {

            foreach ($todo->assigners as $assinger) {
                if (empty($assinger))
                    continue;

                if (!in_array($assinger->id, $assinger_ids)) {
                    $assinger_ids[] = $assinger->id;
                    $assigners[] = $assinger;
                } 
            }

            if (!in_array($todo->creator_id, $creator_ids)) {
                $creator_ids[] = $todo->creator_id;
                $creators[] = $todo->creator;
            } 
        }

        $request->flashOnly('filter');

        return view('pages.admin.ticket.todos', [
            'page'   => 'ticket.todos',
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'tab'  => $tab,
            'todos' => $todos->paginate($this->per_page),
            'creators' => $creators,
            'assigners' => $assigners,
            // 'todos' => $todos->paginate(2),
            'todo'  => new Todo(),
        ]);
    }

    public function tickets(Request $request) {
        $term = $request->input('term');
        $id   = $request->input('id');

        if (empty($term) && !empty($id)) {
            return response()->json(Ticket::findOrFail($id));
        } else {
            $tickets = Ticket::where('id', 'LIKE', '%' . trim($term) . '%')
                             ->orWhere('subject', 'LIKE', '%' . trim($term) . '%')
                             ->get();   
        }

        return response()->json(['tickets' => $tickets]);
    }

    /**
     * @author KCG
     * @since July 3, 2017
     * Add new todo or edit one. 
     */
    public function edit(Request $request, $id = null) {
        $user = Auth::user();

        // Save new todo
        $todo = (empty($id)?new Todo():Todo::find($id));

        $todo->subject              = $request->input('subject');
        $todo->type                 = $request->input('type');
        $todo->creator_id           = $user->id;
        $todo->assigner_ids         = implode_bracket($request->input('assigner_ids'));
        $todo->priority             = $request->input('priority');
        $todo->due_date             = date('Y-m-d', strtotime($request->input('due_date')));
        $todo->related_ticket_id    = $request->input('related_ticket_id');
        $todo->description          = $request->input('description');
        $todo->status               = $request->input('status');

        $todo->save();

        if (empty($id))
            add_message('Todo has been added successfully.', 'success');
        else
            add_message('Todo has been updated successfully.', 'success');

        if ($id)
            return redirect()->route('admin.' . $this->role_id . '.todo.detail', ['id' => $id]);
        else
            return redirect()->route('admin.' . $this->role_id . '.todo.list');
    }

    /**
     * @author KCG
     * @since July 4, 2017
     */
    public function detail(Request $request, $id, $sent_message = false) {
        $todo = Todo::find($id);

        if (empty($todo->id))
            abort(404);

        $query_builder = AdminMessage::where('target_id', $id)
                                     ->where('message_type', AdminMessage::MESSAGE_TYPE_TODO)
                                     ->orderBy('created_at', 'DESC');

        $message_limit = $request->input('_limit', 0);

        // if created new message, show more record.
        if ($sent_message)
            $message_limit = Message::PER_PAGE;
        else
            $message_limit += Message::PER_PAGE;

        $messages = clone $query_builder;
        $messages = $messages->paginate($message_limit);

        $message_count = $query_builder->count();

        // Marked as read
        $msg_id = $request->input('msg_id');
        if (!empty($msg_id)) {
            $admin_message = AdminMessage::find($msg_id);
            if ($admin_message && $admin_message->isUnread()) {
                $admin_message->markedAsRead();

                // Update notification bar.
                $this->beforeAction($request);
            }
        }

        // Add Breadcrumb
        add_breadcrumb('TODOs', route('admin.'.$this->role_id.'.todo.list'));
        add_breadcrumb('ToDo Detail');

        return view('pages.admin.ticket.todo.detail', [
            'page'   => 'ticket.todo.detail',
            'todo' => $todo,

            'message_count' => $message_count,
            'message_limit' => $message_limit,
            'messages'      => $messages,
            'sent_message'  => $sent_message,
        ]);
    }

    /**
    * Send new message in TODO Room
    *
    * @return Response
    */
    public function send_message(Request $request, $todo_id, $message) {
        if (!$request->ajax()) {
            abort(405);
        }

        $user = Auth::user();

        $sender     = $user->id;

        $todoMsg = new AdminMessage();
        $todoMsg->message_type  = AdminMessage::MESSAGE_TYPE_TODO;
        $todoMsg->target_id     = $todo_id;
        $todoMsg->sender_id     = $sender;
        $todoMsg->message       = $message;

        $todoMsg->save();

        // if another send message, he will attend this todo room.
        $todo = Todo::find($todo_id);
        if (strpos($todo->assigner_ids, "[$user->id]") === FALSE) {
            $todo->assigner_ids .= "[$user->id]";
            $todo->save();
        }

        return $this->detail($request, $todo_id);
    }
}