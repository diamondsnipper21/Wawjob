<?php namespace iJobDesk\Http\Controllers\Admin\Ticket;
/**
 * @author KCG
 * @since June 28, 2017
 * Tickets Listing Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use Validator;

use iJobDesk\Models\User;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\Notification;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\File;
use iJobDesk\Models\Message;

class TicketController extends BaseController {

    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);

        $this->page_title = 'Tickets';

        $user = Auth::user();

        // admin names
        $admins = [];
        $ticket_admins = [];

        $admin_users = User::getAdminUsers();
        foreach ($admin_users as $admin) {
            $fullname = $admin->fullname();
            
            if (empty($fullname))
                $fullname = $admin->username;

            $user_info = ['name' => $user->id == $admin->id?'Me':$fullname, 'role' => $admin->role, 'user' => $admin, 'id' => $admin->id, 'username' => $admin->username];
            // if ($admin->role == User::ROLE_USER_TICKET_MANAGER)
            //     $ticket_admins[] = $user_info;
            // if ($user->id == $admin->id)
            //     continue;

            $admins[] = $user_info;
            $ticket_admins[] = $user_info;
        }

        view()->share([
            'admins' => $admins,
            'ticket_managers' => $ticket_admins,
        ]);
    }

    /**
    * Show ticket listing.
    *
    * @return Response
    */
    public function index(Request $request, $tab = 'mine', $user_id = null) {
        // Add Breadcrumb
        if (empty($user_id)) {
            add_breadcrumb('Tickets');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('Tickets');
        }
        
        $user = null;
        $me = Auth::user();
        if (!empty($user_id)) {
            $user = ViewUser::find($user_id);

            if (!$user)
                abort(404);
        }

        // sort
        $sort     = $request->input('sort');
        $sort_dir = $request->input('sort_dir');

        $column_unread_admin_messages   = "(".Ticket::getColumnUnreadAdminMessage('tickets.id').")";
        $column_unread_messages         = "(".Ticket::getColumnUnreadMessage('tickets.id').")";
        $column_is_new                  = "(".Ticket::getColumnIsNew('tickets').")";
        
        $tickets = Ticket::addSelect('*')
                         ->addSelect(DB::raw("IF(".($me->isSuper()?1:0)." = 1, 1, 0) AS is_super"))
                         ->addSelect(DB::raw("$column_unread_admin_messages AS unread_admin_messages"))
                         ->addSelect(DB::raw("$column_unread_messages AS unread_messages"))
                         ->addSelect(DB::raw("$column_is_new AS is_new"))
                         ->addSelect(DB::raw("IF(admin_id = {$me->id}, 1, 0) AS mine"))
                         ->addSelect(DB::raw("IF(assigner_id = {$me->id}, 1, 0) AS by_me"));

        if (!$sort) {
            $tickets->orderBy('created_at', 'DESC')//->orderBy('is_super', 'DESC')
                    ->orderByRaw('IF(unread_admin_messages != 0, 1, 0) DESC')
                    ->orderByRaw('IF(unread_messages != 0, 1, 0) DESC')
                    // ->orderByRaw('is_new DESC')
                    ->orderBy('mine', 'DESC')
                    ->orderBy('is_new', 'DESC')
                    ->orderBy('by_me', 'DESC');

            $sort       = '';
            $sort_dir   = '';
        }

        if ($sort == 'assigner')
            $tickets->orderBy('admin_id', $sort_dir)
                    ->orderBy('tickets.id', $sort_dir);
        elseif ($sort && $sort_dir)
            $tickets->orderBy($sort, $sort_dir)
                    ->orderBy('tickets.id', $sort_dir);

        $filter = $request->input('filter');

        // Action Required
        if (!empty($filter['action_required'])) {
            $tickets->where(function($query) use ($column_is_new, $column_unread_messages, $column_unread_admin_messages) {
                $query->orWhereRaw("$column_unread_messages > 0")
                      ->orWhereRaw("$column_unread_admin_messages > 0")
                      ->orWhereRaw("$column_is_new = 1");
            });
        }
        
        // By New
        if (!empty($filter['new'])) {
            if ($filter['new'] == 'is_unread_msg')
                $tickets->whereRaw("$column_unread_messages > 0");
            
            if ($filter['new'] == 'is_unread_admin_msg')
                $tickets->whereRaw("$column_unread_admin_messages > 0");
            
            if ($filter['new'] == 'is_new')
                $tickets->whereRaw("$column_is_new != 0");
        }

        // DON'T DISPLAY TICKET FOR ID VERIFICATION FOR TICKET MANAGER
        if (!$me->isSuper())
            $tickets->where('type', '<>', Ticket::TYPE_ID_VERIFICATION);

        $action = $request->input('_action');

        // change status
        if ($action == 'CHANGE_STATUS') {
            $status = $request->input('status');
            $ids = $request->input('ids');

            Ticket::whereIn('id', $ids)
                  ->update(['status' => $status, 'ended_at' => date('Y-m-d H:i:s')]);

            add_message(sprintf('The status of %d Ticket(s) has been changed.', count($ids)), 'success');
        } elseif ($action == 'ASSIGN_TO') {
            $ids = $request->input('ids');
            $admin_id = $request->input('assigner');
            $admin    = User::find($admin_id);

            $tks = Ticket::whereIn('id', $ids)->get();
            foreach ($tks as $ticket) {
                $ticket->admin_id       = $admin_id;
                $ticket->status         = Ticket::STATUS_ASSIGNED;
                $ticket->assigner_id    = $me->id;

                $ticket->save();
            }

            add_message(sprintf('%d Ticket(s) has been assigned to %s.', count($ids), $admin->fullname()), 'success');
        }

        if (!empty($user_id)) {
            $tickets->where(function($query) use ($user_id) {
                $query->where('user_id', $user_id)
                        ->orWhere('receiver_id', $user_id);
            });
        }

        if ($tab == 'opening')
            $tickets->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED]);
        elseif ($tab == 'mine')
            $tickets->whereIn('status', [Ticket::STATUS_ASSIGNED])
                    ->where(function($query) use ($me) {
                        $query->orWhere('admin_id', $me->id);

                        // If current user is super admin, the tickets with unread private messages will include in my tickets tab.
                        if ($me->isSuper())
                            $query->orWhereRaw("(".Ticket::getColumnUnreadAdminMessage('tickets.id').") != 0");

                    });
        else
            $tickets->whereIn('status', [Ticket::STATUS_SOLVED]);

        $opens_count = Ticket::whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED])
                             ->where(function($query) use ($user_id, $me) {
                                if (!empty($user_id))
                                    $query->where('user_id', $user_id);

                                if (!$me->isSuper())
                                    $query->where('type', '<>', Ticket::TYPE_ID_VERIFICATION);
                             })
                             ->count();

        $mys_count = Ticket::where(function($query) use ($me) {
                                $query->orWhere('admin_id', $me->id);

                                // If current user is super admin, the tickets with unread private messages will include in my tickets tab.
                                if ($me->isSuper())
                                    $query->orWhereRaw("(".Ticket::getColumnUnreadAdminMessage('tickets.id').") != 0");

                                if (!$me->isSuper())
                                    $query->where('type', '<>', Ticket::TYPE_ID_VERIFICATION);

                           })
                           ->whereIn('status', [Ticket::STATUS_ASSIGNED])
                           ->where(function($query) use ($user_id) {
                                if (!empty($user_id))
                                    $query->where('user_id', $user_id);
                           })
                           ->count();

        $archived_count = Ticket::where(function($query) use ($me) {
                                    $query->where('admin_id', $me->id);
                                })
                                ->where('status', Ticket::STATUS_SOLVED)
                                ->where(function($query) use ($user_id, $me) {
                                    if (!empty($user_id))
                                        $query->where('user_id', $user_id);

                                    if (!$me->isSuper())
                                        $query->where('type', '<>', Ticket::TYPE_ID_VERIFICATION);
                                })
                                ->count();

        // By ID
        if (!empty($filter['id'])) {
            $tickets->where('id', $filter['id']);
        }

        // By Type
        if (!empty($filter['type'])) {
            $tickets->where('type', $filter['type']);
        }

        // By Priority
        if (!empty($filter['priority'])) {
            $tickets->where('priority', $filter['priority']);
        }

        // By Subject
        if (!empty($filter['subject'])) {
            $tickets->where('subject', 'LIKE', '%'.trim($filter['subject']).'%');
        }

        // By Creator
        if (strval($filter['assigner']) != '') {
            if(strval($filter['assigner']) == '-1')
                $tickets->whereNull('admin_id');
            else
                $tickets->where('admin_id', intval($filter['assigner']));
        }

        // By Create Time
        if (!empty($filter['updated_at'])) {
            if (!empty($filter['updated_at']['from'])) {
                $tickets->where('updated_at', '>=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['from'])));
            }

            if (!empty($filter['updated_at']['to'])) {
                $tickets->where('updated_at', '<=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['to']) + 24* 3600));
            }
        }

        $request->flashOnly('filter');

        $new_tickets        = Ticket::getNewCount(); // Count of new tickets
        $unassigned_tickets = Ticket::getUnassignedCount(); // Count of unassigned tickets

        $optionTypeArry = Ticket::getOptions("create_type");

        return view('pages.admin.ticket.tickets', [
            // 'page'   => 'ticket.tickets',
            'page' => empty($user_id)?'ticket.tickets':'super.user.commons.tickets',         
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'tab'  => $tab,
            'tickets' => $tickets->paginate($this->per_page),
            // 'tickets' => $tickets->paginate(2),
            'opens_count' => $opens_count,
            'mys_count' => $mys_count,
            'archived_count' => $archived_count,
            'me' => $me,
            'user' => $user,

            'new_tickets'        => $new_tickets,
            'unassigned_tickets' => $unassigned_tickets,
            'optionTypeArry' => $optionTypeArry,
        ]);
    }

    /**
     * @author KCG
     * @since July 4, 2017
     */
    public function detail(Request $request, $id, $user_id = null, $sent_message = false) {
        $me = Auth::user();
        $user = null;
        if (!empty($user_id)) {
            $user = ViewUser::find($user_id);

            if (!$user)
                abort(404);
        }

        $ticket = Ticket::find($id);

        if (empty($ticket->id))
            abort(404);

        if (!$me->isSuper() && $ticket->type == Ticket::TYPE_ID_VERIFICATION)
            abort(404);

        $messages = $ticket->messages();
        $user_types = [];
        if ($ticket->user_id) {
            if ($ticket->contract_id)
            {
                $user_types[] = ['id' => $ticket->contract->buyer->id, 
                'icon' => 'fa-credit-card', 
                'name' => 'Buyer'];

                $user_types[] = ['id' => $ticket->contract->contractor->id, 
                'icon' => 'fa-user', 
                'name' => 'Freelancer'];
            }
            else
            {
                $user_types[] = ['id' => $ticket->user->id, 
                'icon' => 'fa-user', 
                'name' => 'User'];
            }
        }

        // If ticket manager is read this ticket.
        if ($ticket->isUnread()) {
            $ticket->markedAsRead();

            $this->beforeAction($request);
        }
        
        if ($request->isMethod('post')) {
            $action = $request->input('_action');
            if ($action == 'SAVE_MEMO') {
                $ticket->memo = $request->input('memo');
                $ticket->save();

                add_message("The ticket memo have been saved successfully.", 'success');
            } else if($action == 'CHANGE_PRIORITY') {
                if ($ticket->status != Ticket::STATUS_SOLVED) {
                    $priority = $request->input('priority');
                    $ticket->priority = $priority;

                    if($ticket->save())
                        add_message("Ticket's priority has been changed.", 'success');
                    else
                        add_message("Ticket's priority couldn't be changed.", 'danger');
                }
            }
            else if ($action == 'REOPEN') {
                $ticket->status = Ticket::STATUS_OPEN;
                $ticket->save();
            } else if ($action == 'CHANGE_ASSIGNEE') {
                if ($ticket->status != Ticket::STATUS_SOLVED)
                {
                    $assignee = $request->input('assignee');

                    $old_admin_id = $ticket->admin_id;

                    $ticket->admin_id = $assignee;
                    $ticket->status = Ticket::STATUS_ASSIGNED;
                    $ticket->assigner_id = $me->id;

                    if ($ticket->save())
                        add_message(sprintf('Ticket is assigned to %s', $ticket->admin->fullname()), 'success');
                    else
                        add_message("Ticket couldn't be assigned", 'danger');
                }
            } else if($action == 'CHANGE_COMMENT_FILTER') {
                $filter = $request->input('filter_comments');

                if($filter == 1)//Ticket Manager Only
                {
                    $messages = $messages->leftJoin('users', 'sender_id', '=', 'users.id')
                                         ->where('users.role', User::ROLE_USER_TICKET_MANAGER); 
                }
                else if($filter == 2)//Freelancer Only
                {
                    if($ticket->contract_id) {
                        $freelancer_id = $ticket->contract->contractor_id;
                        $messages = $messages->leftJoin('users', 'sender_id', '=', 'users.id')
                                             ->where('users.id', $freelancer_id);
                    } else {
                        $messages = $messages->leftJoin('users', 'sender_id', '=', 'users.id')->where('users.role', '!=', User::ROLE_USER_TICKET_MANAGER);
                    }
                }
                else if($filter == 3) { // Buyer Only
                    $buyer_id = $ticket->contract->buyer_id;
                    $messages = $messages->leftJoin('users', 'sender_id', '=', 'users.id')
                                         ->where('users.id', $buyer_id);
                }
            } else if ($action == 'ASSIGN_TO_ME') {
                $ticket->admin_id       = $me->id;
                $ticket->status         = Ticket::STATUS_ASSIGNED;
                $ticket->assigner_id    = $me->id;

                $ticket->save();

                add_message('This ticket has been assigned to me.', 'success');
            }
        }

        // If this ticket doesn't have assigner, notify message
        if (!$ticket->admin_id && !$request->isMethod('post'))
            add_message('Please assign this ticket to you before sending any messages.<div class="alert-assign-to-me margin-top-15 text-right"><button class="btn btn-danger">Assign to me</button>', 'info', true, ['position-class' => 'toast-top-center', 'time-out' => '100000']);

        // Infinite Loading
        $query_builder = $messages->orderBy('created_at', 'DESC')
                                  ->select('ticket_comments.*');

        Message::loadMessages($request, $query_builder, $sent_message);
        
        $request->flashOnly('filter_comments');

        // Add Breadcrumb
        $tab = 'opening';
        if ($ticket->status == Ticket::STATUS_SOLVED)
            $tab = 'archived';
        
        if(!empty($user)) {
            add_breadcrumb('Users', route('admin.super.users.list'));
        }

        add_breadcrumb('Tickets', route('admin.'.$this->role_id.(!empty($user)?'.user':'').'.ticket.list', [$tab => $tab, 'user_id' => !empty($user)?$user->id:null]));
        add_breadcrumb('Ticket Detail');

        return view('pages.admin.ticket.ticket.detail', [
            'page'   => 'ticket.ticket.detail',
            'ticket' => $ticket,
            'me' => $me,
            'user' => $user,
            'user_types' => $user_types
        ]);
    }

    /**
    * Ticket create
    *
    * @param  Request $request
    * @return Response
    */
    public function create(Request $request, $user_id) {
    	$me = Auth::user();

        // Validator
        $validator = Validator::make($request->all(), [
            'subject'       => 'required|max:200',
            'content'       => 'required|max:5000',
            'type'          => 'required'
        ]);

        if ( $validator->fails() ) {
            $errors = $validator->messages();
            if ( $errors->all() )
                foreach ( $errors->all() as $error )
                    add_message($error, 'danger');

            return redirect()->route('admin.super.user.ticket.list', ['user_id' => $user_id, 'tab' => 'opening']);
        } else {
        	try {
		        $ticket = new Ticket();

		        $ticket->subject = $request->input('subject');
		        $ticket->content = strip_tags($request->input('content'));
		        $ticket->admin_id = $me->id;
		        $ticket->assigner_id = $me->id;
		        $ticket->user_id = $me->id;
		        $ticket->receiver_id = $user_id;
		        $ticket->reader_ids = '[' . $me->id . '][' . $user_id . ']';
		        $ticket->priority = Ticket::PRIORITY_MEDIUM;
		        $ticket->status = Ticket::STATUS_ASSIGNED;
		        $ticket->type = $request->input('type');
		        
		        if ( $ticket->save() ) {
		            add_message('New ticket has been created successfully.', 'success');
		        }
		    } catch (Exception $e) {
		    }
		}

        return redirect()->route('admin.super.user.ticket.list', ['user_id' => $user_id, 'tab' => 'opening']);
    }

    /**
     * @author KCG
     * @since July 4, 2017
     * add comment to ticket
     */
    public function send(Request $request, $id, $user_id = null) {
        $me = Auth::user();
        $user = null;
        
        if (!empty($user_id)) {
            $user = ViewUser::find($user_id);

            if (!$user)
                abort(404);
        }

        $ticket = Ticket::find($id);

        if ($ticket->isClosed() || !$ticket->isAssigned())
            abort(404);

        if ($request->isMethod('post') && !$ticket->isClosed()) {

            $comment = new TicketComment;

            $comment->message = $request->input('message');
            $comment->ticket_id = $id;
            $comment->sender_id = $me->id;

            try {
                $comment->save();
            } catch (Exception $e) {
            }
        }

        return $this->detail($request, $id, $user_id, true);
    }

    public function send_message(Request $request, $id, $message) {
        $type = $request->input('_type');

        if ($type == File::TYPE_ADMIN_MESSAGE)
            return $this->send_admin_message($request, $id, $message);
        else
            return $this->send($request, $id, null);
    }

    public function send_admin_message(Request $request, $id, $message) {
        $ticket = Ticket::find($id);
        $me = Auth::user();

        $msg = new AdminMessage;

        $msg->message_type = AdminMessage::MESSAGE_TYPE_TICKET;
        $msg->target_id = $ticket->id;
        $msg->sender_id = $me->id;
        $msg->message = $message;
        $msg->save();

        return $this->msg_admin($request, $id, null, true);
    }

    /**
     * @author KCG
     * @since July 4, 2017
     * solve ticket
     */
    public function solve(Request $request, $user_id = null, $id = null) {
        $me = Auth::user();
        $user = null;
        if (!empty($user_id)) {
            $user = ViewUser::find($user_id);

            if (!$user)
                abort(404);
        }

        $ticket_ids = $request->input('ticket_id');
        $tickets = Ticket::whereIn('id', $ticket_ids)
                         ->where('type', '<>', Ticket::TYPE_DISPUTE);

        if (!$me->isSuper()) {
            $tickets->where('admin_id', $me->id);
        }
        
        $archive_type   = $request->input('archived');
        $reason         = $request->input('reason');
        $return_page    = $request->input('return_page');

        $tickets = $tickets->get();

        foreach ($tickets as $ticket) {
            
            // case of solving myself without assigner, assigner will be me.
            if (!$ticket->admin_id) {
                $ticket->admin_id = $me->id;
            }

            if (!$ticket->assigner_id) {
                $ticket->assigner_id = $me->id;
            }

            // If this ticket is DISPUTE.
            if ($ticket->contract_id && $ticket->type == Ticket::TYPE_DISPUTE)
                continue;

            $ticket->status             = Ticket::STATUS_SOLVED;
            $ticket->archive_type       = $archive_type;
            $ticket->reason             = $reason;
            $ticket->save();
        }

        add_message(sprintf('The %d tickets have been solved successfully.', count($tickets)), 'success');

        if ($return_page == "detail") {
            return $this->detail($request, $tickets[0]->id);
        } else {
            return response()->json(['success' => true]);
        }
        
    }

    public function msg_admin(Request $request, $id, $user_id = null, $sent_message = false) {
        $me = Auth::user();
        $user = null;
        if (!empty($user_id)) {
            $user = ViewUser::find($user_id);

            if (!$user)
                abort(404);
        }

        add_breadcrumb('Tickets', route('admin.super.ticket.list'));
        add_breadcrumb('Ticket Detail', route('admin.super.ticket.detail', ['id' => $id]));

        add_breadcrumb('View Private Messages');

        $ticket = Ticket::findOrFail($id);

        if (!$ticket->isAssigned())
            abort(404);

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

        // Infinite Loading
        $query_builder = $ticket->admin_messages()
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

        return view('pages.admin.ticket.ticket.msg_admin', [
            'page'   => 'ticket.ticket.msg_admin',
            'ticket' => $ticket,

            'message_count' => $message_count,
            'message_limit' => $message_limit,
            'messages'      => $messages,
            'sent_message'  => $sent_message,

            'user' => $user
        ]);
    }

    /**
     * Marked As Read for comment
     */
    public function read_comment(Request $request, $id) {
        $message = TicketComment::find($id);
        $message->markedAsRead();

        return json_encode(['success' => true]);
    }
}