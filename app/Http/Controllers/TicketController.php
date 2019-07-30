<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Auth;
use Storage;
use Config;
use Validator;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;
use iJobDesk\Models\Notification;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\File;
use iJobDesk\Models\Message;

//DB
use DB;

class TicketController extends Controller {

    const FIRST_COMMENT_LOADING_COUNT = 20;
    /**
    * Constructor
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Ticket Message list
    *
    * @param  Request $request
    * @return Response
    */
    public function all(Request $request, $tab = 'opening') {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('user.login');
        }

        $user_id = $user->id;

        try {

            $tickets = null;

            $subject = $request->input("search_title");
            $sort = $request->input("sort");

            if ($request->isMethod('post')) {
                $tab = $request->input("tab");
            }

            $tickets = Ticket::leftJoin('users AS u', 'user_id', '=', 'u.id')
                            ->leftJoin('ticket_comments AS tc', 'tc.ticket_id', '=', 'tickets.id')
                            ->where(function($query) use ($user_id) {
                                $query->where('tickets.user_id', $user_id)
                                    ->orWhere('receiver_id', $user_id);
                            })
                            ->where('u.status', '<>', User::STATUS_NOT_AVAILABLE)
                            /*
                            ->where(function($query) use ($user_id) {
                                $query->whereNull('tc.sender_id')
                                        ->orWhere('tc.sender_id', '<>', $user_id);
                            })
                            */
                            // ->where(function($query) use ($user_id) {
                            //     $query->whereNull('tc.reader_ids')
                            //             ->orWhere('tc.reader_ids', 'NOT LIKE', '%[' . $user_id. ']%');
                            // })
                            ->orderBy('tickets.created_at','DESC');


            if ($tab == 'opening')
                $tickets = $tickets->whereIn('tickets.status', [Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED]);
            else
                $tickets = $tickets->whereIn('tickets.status', [Ticket::STATUS_SOLVED, Ticket::STATUS_CLOSED]);

            if ($request->isMethod('post')) {

                $postType = $request->input('postType');
                if ( $postType == "close" ) {

                    $user_id = $user->id;
                    $ticket_id = $request->input('postTicketId');

                    $ticket = Ticket::find($ticket_id);
                    if ($ticket->type != Ticket::TYPE_DISPUTE && $ticket->type != Ticket::TYPE_ID_VERIFICATION) {
                        $ticket->status       = Ticket::STATUS_SOLVED;
                        $ticket->archive_type = Ticket::RESULT_SOLVED_SUCCESS;
                        $ticket->reason       = trans('ticket.is_closed', ['name' => $user->fullname()]);

                        try {
                            if ($ticket->save()) {
                                add_message( trans('ticket.ticket_closed_successfully'), 'success' );
                            }
                        } catch (Exception $e) {
                        }
                    }
                
                    return redirect()->route('ticket.list');
                }
            }

            if ( $subject ) {
                $tickets->where(function($query) use ($subject) {
                    $query->where('tickets.subject', 'LIKE', '%' . $subject .'%')
                          ->orWhere('tickets.content', 'LIKE', '%' . $subject .'%');
                });
            }

            if ( $sort ) {
                if ($sort == Ticket::TYPE_ACCOUNT) {
                    $tickets->where('tickets.type', Ticket::TYPE_ACCOUNT);
                } 
                else if ($sort == Ticket::TYPE_PAYMENT) {
                    $tickets->where('tickets.type', Ticket::TYPE_PAYMENT);
                }
                else if ($sort == Ticket::TYPE_SUSPENSION) {
                    $tickets->where('tickets.type', Ticket::TYPE_SUSPENSION);
                }
                else if ($sort == Ticket::TYPE_OTHER) {
                    $tickets->where('tickets.type', Ticket::TYPE_OTHER);
                }
            }

            $tickets = $tickets->groupBy('tickets.id')
                               ->select(DB::raw('tickets.*, COUNT(tc.ticket_id) AS unreads'))
                               ->paginate(Ticket::FIRST_TICKET_LOADING_COUNT);

            $tickets->appends(['search_title' => $subject, 'sort' => $sort]);

            $optionTypeArry = Ticket::getOptions("create_type");

            $request->flash();

        } catch (Exception $e) {
            error_log('[all() in TicketController.php] ' . $e->getMessage());
        }

        return view('pages.ticket.list', [
            'page'           => 'ticket.list',
            'tickets'        => $tickets,
            'tab'            => $tab,
            'userId'         => $user_id,
            'optionTypeArry' => $optionTypeArry,
            'search_title'   => $subject,
            'sort'           => $sort,
            'config'         => Config::get('settings'),
            'j_trans'        => [
                'btn_yes' => trans('ticket.btn_yes'),
                'btn_no' => trans('ticket.btn_no'),
                'validation_required' => trans('common.validation.required'),
                'new' => $request->input('_action') == 'new' ? 1 : 0,
            ],
        ]);
    }

    public function detail(Request $request, $ticket_id, $sent_message = false) {
        $user = Auth::user();
        $action = $request->input('_action', '');

        if (!$user) {
            return redirect()->route('user.login');
        }

        $user_id = $user->id;
        $ticket = Ticket::find($ticket_id);
        if (!$request->isMethod('post') || ($action == 'LOAD_MESSAGE') )
            $ticket = Ticket::findByUnique($ticket_id);

        $ticket_id = $ticket->id;
        $request->merge(['id' => $ticket_id]);
        
        if ($ticket->user_id != $user_id && $ticket->receiver_id != $user_id)
            return redirect()->route('ticket.list');

        // Marked As Read
        foreach ($ticket->messages as $message) {
            $message->markedAsRead();
        }

        $content = nl2br($ticket->content);

        // Infinite Loading
        $query_builder = TicketComment::where('ticket_id', $ticket_id)
                                      ->orderBy('created_at', 'DESC');

        Message::loadMessages($request, $query_builder, $sent_message);

        return view('pages.ticket.detail', [
            'page'                  => 'ticket.detail',
            'ticket'                => $ticket,
            'content'               => $content,
            'userId'                => $user_id,
            'user'                  => $user,
            'config'                => Config::get('settings'),
            'j_trans' => [
                'validation_required' => trans('common.validation.required'),
                'no_more' => trans('ticket.no_more'),
            ]
        ]);
    }

    /**
    * Ticket create
    *
    * @param  Request $request
    * @return Response
    */
    public function create(Request $request) {
        $me = Auth::user();

        if ($request->input('type') != Ticket::TYPE_DISPUTE) {

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

                return redirect()->route('ticket.list');
            }

            $ticket = new Ticket();

            $ticket->subject = $request->input('subject');
            $ticket->content = strip_tags($request->input('content'));
            $ticket->user_id = $me->id;
            $ticket->priority = Ticket::PRIORITY_MEDIUM;
            $ticket->type = $request->input('type');

            if ($ticket->type == Ticket::TYPE_ID_VERIFICATION)
                return redirect()->route('ticket.list');

            $ticket->save();
        }

        return redirect()->route('ticket.list');
    }

    public function send_message(Request $request, $ticket_id, $message) {
        $me = Auth::user();

        $ticket = Ticket::find($ticket_id);

        if (!$ticket || $ticket->isClosed())
            abort(404);

        if (!$me->isAdmin() && $ticket->user_id != $me->id && $ticket->receiver_id != $me->id)
            abort(404);

        $ticket_comment = new TicketComment;
        $ticket_comment->ticket_id  = $ticket_id;
        $ticket_comment->sender_id  = $me->id;
        $ticket_comment->message    = mb_substr($message, 0, 5000);

        $ticket_comment->save();

        $me->updateLastActivity();

        return $this->detail($request, $ticket_id, true);
    }
}   