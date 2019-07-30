<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\Paginator;

use Auth;
use Storage;
use Config;
use DB;
use Log;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Notification;
use iJobDesk\Models\File;
use iJobDesk\Models\Message;

class MessageController extends Controller {

    /**
    * Retrieve Message list
    *
    * @param  Request $request
    * @return Response
    */
    public function index(Request $request, $id = null, $sent_message = false) {
        $user = Auth::user();

        $user_id = $user->id;

        // Get the message thread list
        $keywords   = $request->input('keywords');
        $tab        = $request->input('tab', 'inbox');
        $action     = $request->input('action', 'LOAD_THREADS');
        $thread_id  = $request->input('thread_id', $id);

        if ($thread_id) {
            $thread = ProjectMessageThread::find($thread_id);

            if (!$user->isAdmin() && !$request->isMethod('post')) {
                $thread    = ProjectMessageThread::findByUnique($thread_id);
                $thread_id = $thread->id;
            }

            if (!$user->isAdmin()) {
                if ($thread->sender_id != $user_id && $thread->receiver_id != $user_id)
                    abort(404);

                if ($action == 'ARCHIVE') // Move to archive box
                    $thread->archived();

                if ($action == 'MOVE_TO_INBOX') // Move to inbox
                    $thread->unArchived();

                // recent_message_created_at will update. so this thread will be displayed first from /messages/{$id}
                if ($request->isMethod('GET')) {
                    //$thread->recent_message_created_at = date('Y-m-d H:i:s');
                    //$thread->save();
                }
            }
        }

        if (!$tab)
            $tab = 'inbox';

        // if ($action == 'ARCHIVE' && $tab == 'inbox') // if thread move to archive box, this thread will not be placed on inbox. In this case, current thread will be another first one on inbox.
        //     $thread_id = null;

        // if ($action == "SEARCH") // if try to search with keywords, ignore current thread. because this thread couldn't exist in search result.
        //     $thread_id = null;

        $message_threads = ProjectMessageThread::join('users', function($join) {
                                                    $join->on('project_message_threads.sender_id', '=', 'users.id')
                                                         ->orOn('project_message_threads.receiver_id', '=', 'users.id');
                                                })
                                                ->join('project_applications AS pa', 'project_message_threads.application_id', '=', 'pa.id')
                                                ->join('projects AS p', 'pa.project_id', '=', 'p.id')               
                                                ->leftJoin('project_messages AS pm', 'project_message_threads.id', '=', 'pm.thread_id')
                                                ->where(function($query) use ($user_id) {
                                                    $query->where('project_message_threads.sender_id', $user_id)
                                                          ->orWhere('project_message_threads.receiver_id', $user_id);
                                                })
                                                ->where('users.status', '<>', 0)
                                                ->where('users.id', '<>', $user_id)

                                                ->whereNull('p.deleted_at')
                                                ->whereNull('pa.deleted_at')
                                                ->whereNull('pm.deleted_at');

        if ( $keywords ) { // Search By fullname or subject of thread
            $message_threads->join('user_contacts', function($join) {
                                    $join->on('user_contacts.user_id', '=', 'users.id');
                              })
                              // ->where('user_contacts.id', '<>', $user_id)
                              ->where(function($query) use ($keywords) {
                                    $query->where('project_message_threads.subject', 'LIKE', '%' . $keywords .'%')
                                          ->orWhere('pm.message', 'LIKE', '%' . $keywords .'%')
                                          ->orWhereRaw('LOWER(CONCAT(user_contacts.first_name, " ", user_contacts.last_name)) LIKE "%' . strtolower($keywords) .'%"');
                              });
        }

        // The current thread will be place on top.
        if (!$request->isMethod('post') && $thread_id) {
            $message_threads->addSelect(DB::raw("IF(project_message_threads.id = $thread_id, 1, 0) AS order1"))
                            ->orderBy('order1', 'DESC');
        }

        $message_threads->groupBy('project_message_threads.id')
                        ->orderBy('project_message_threads.recent_message_created_at', 'DESC')
                        ->addSelect('project_message_threads.*');

        $threads = [];

        if (!$sent_message && $action != 'SEND_MESSAGE' && $action != 'LOAD_THREAD') {
            // Inbox
            $threads['inbox'] = clone $message_threads;
            $threads['inbox']->where(function($query) use ($user) {
                $query->where('project_message_threads.is_archived', 'NOT LIKE', "%[$user->id]%")
                      ->orWhereNull('project_message_threads.is_archived');
            });

            // Unread Box
            $threads['unread'] = clone $message_threads;
            $threads['unread']->whereRaw('(' . ProjectMessageThread::getUnreadColumn('project_message_threads.id') . ') <> 0');

            // Archived Box
            $threads['archive'] = clone $message_threads;
            $threads['archive']->where('project_message_threads.is_archived', 'LIKE', "%[$user->id]%");

            // All
            $threads['all'] = clone $message_threads;
            
            $page       = Paginator::resolveCurrentPage();
            foreach ($threads as $key => $v) {
                $perPage    = ProjectMessageThread::MESSAGE_THREAD_PER_PAGE;

                if ($key == $tab && $action == 'LOAD_THREADS')
                    $perPage += $request->input('per_page', 0);

                $threads[$key] = $threads[$key]->paginate($perPage, ['*'], 'page', 1);
            }

            // if there is no selected thread, first thread will be selected.
            if (!$thread_id) {
                $thread = $threads['inbox']->first();
                if (!$thread) {
                    $thread = $threads['all']->first();
                    $tab = 'all';
                }
            } else {
                if (!$threads[$tab]->contains('id', $thread_id))
                    $tab = 'inbox';

                if (!$threads[$tab]->contains('id', $thread_id))
                    $tab = 'all';
            }
        }

        $attachments = [];
        if ($thread) {
            // when loading message first, load count of unreaded message, not default message count per page.
            $_limit = $request->input('_limit', 0);
            if ($_limit == 0)
                view()->share(['first_load' => true]);
            else
                view()->share(['first_load' => false]);

            $unreads = $thread->unreadsIncludeMine();

            $_limit = $_limit == 0 && $unreads > Message::PER_PAGE?$unreads - Message::PER_PAGE:$_limit;
            $request->merge(['_limit' => $_limit]);

            $this->loadMessages($request, $thread->id, $sent_message);

            // Attachments
            $attachments = $thread->attachments();

            // Marked As Read for this thread.
            if ($action == 'LOAD_THREAD') {
                $thread->markedAsRead();
                $thread->unreads = 0;
            }
        } else {
            $thread = new ProjectMessageThread();
        }

        // Unreads Threads List
        if ($action == 'LOAD_THREAD') {
            // $unread_threads = ProjectMessage::join('project_message_threads AS pmt', 'project_messages.thread_id', '=', 'pmt.id')
            //                   ->where(function($query) use ($user_id) {
            //                         $query->where('pmt.sender_id', $user_id)
            //                               ->orWhere('pmt.receiver_id', $user_id);
            //                   })
            //                   ->where('project_messages.sender_id', '<>', $user_id)
            //                   ->whereRaw("(project_messages.reader_ids NOT LIKE '%[$user_id]%' OR project_messages.reader_ids IS NULL)")
            //                   ->addSelect('pmt.id AS thread_id')
            //                   ->addSelect(DB::raw('COUNT(project_messages.thread_id) AS unreads'))
            //                   ->groupBy('pmt.id')
            //                   ->get();

            // view()->share('unread_threads', $unread_threads);
        }

        if ($sent_message || $action == 'SEND_MESSAGE' || $action == 'LOAD_THREAD') {
            $view = 'pages.message.partials.message_container';

            view()->share([
                'per_page' => 0,
            ]);
        } else {
            $view = 'pages.message.threads';

            view()->share([
                'per_page' => $threads[$tab]->perPage(),
            ]);
        }

        return view($view, [
            'page'                  => 'message.threads',
            'threads'               => $threads,
            'attachments'           => $attachments,

            'thread_id'             => $thread->id,
            'thread'                => $thread,
            'keywords'              => $keywords,
            'tab'                   => $tab
        ]);
    }

    private function loadMessages(Request $request, $thread_id, $sent_message = false) {
        $user = Auth::user();

        $user_id = $user->id;
        $thread = ProjectMessageThread::find($thread_id);

        if ( !$thread )
            abort(404);

        // if this room is not own, 404 error
        if ( $thread->sender_id != $user_id && $thread->receiver_id != $user_id ) {
            abort(404);
        }

        $query_builder = ProjectMessage::where('thread_id', $thread_id)
                                       ->orderBy('created_at', 'DESC');

        Message::loadMessages($request, $query_builder, $sent_message);
    }

    public function send_message(Request $request, $thread_id, $message = null) {
        $me = Auth::user();
        $thread = ProjectMessageThread::find($thread_id);

        if (!$thread)
            abort(404);

        $application = $thread->application;

        if (!$thread->canSendMessage())
            return false;

        if (!$application)
            abort(404);

        $message_id = $application->sendMessage($message, $me->id);
        $me->updateLastActivity();
        
        $thread->unArchived(false);

        return $this->index($request, $thread_id, true);

        // $message_row = view('pages.partials.message.row', [
        //     'message' => ProjectMessage::find($message_id),
        //     'type' => File::TYPE_MESSAGE
        // ])->render();

        // $attachments_html = view('pages.message.partials.attachments.contents', [
        //     'attachments' => $thread->attachments()
        // ])->render();

        // return response()->json([
        //     'message_row' => $message_row,
        //     'attachments_html' => $attachments_html
        // ]);
    }
}