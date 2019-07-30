<?php namespace iJobDesk\Http\Controllers\Admin\Super;

use Illuminate\Http\Request;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;

use Config;
use Auth;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\AdminMessageThread;
use iJobDesk\Models\EmailTemplate;

class ThreadController extends BaseController {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->page_title = 'Message Rooms';
        parent::__construct();
	}

	public function create(Request $request) {
		$me = Auth::user();

		$to      = $request->to;
		$subject = $request->subject;
		$message = $request->message;

		$thread = new AdminMessageThread();
		$thread->to 		= implode_bracket($to);
		$thread->creator_id = $me->id;
		$thread->subject 	= $subject;
		$thread->save();

		$admin_message = new AdminMessage();
		$admin_message->message_type = AdminMessage::MESSAGE_TYPE_THREAD;
		$admin_message->target_id = $thread->id;
		$admin_message->sender_id = $me->id;
		$admin_message->message   = mb_substr($message, 0, 5000);

		$admin_message->save();

		add_message('New message has been sent successfully.', 'success');

		return redirect()->route('admin.super.messages');
	}

	public function detail(Request $request, $id) {
		$this->setPageTitle('Messages');

		$thread = AdminMessageThread::find($id);

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
        
		$messages 	= AdminMessage::where('message_type', AdminMessage::MESSAGE_TYPE_THREAD)
								  ->where('target_id', $id)
								  ->get();

        return view('pages.admin.super.thread.detail', [
            'page' => 'super.thread.detail',
            'thread' => $thread,
            'messages' => $messages
        ]);  
	}

	public function send(Request $request, $id) {
		$user  = Auth::user();
		$thread = AdminMessageThread::find($id);

		$message = $request->input('message');
		if ($message) {
			$admin_message = new AdminMessage();

			$admin_message->message_type 	= AdminMessage::MESSAGE_TYPE_THREAD;
			$admin_message->target_id 		= $id;
			$admin_message->sender_id 		= $user->id;
			$admin_message->message 		= $message;
			$admin_message->save();

			$sent = 1;

			if ($sent)
				add_message('Your message have been sent successfully.', 'success');
			else
				add_message('Your message have not been sent. Please try again.', 'danger');
		}

		return $this->detail($request, $id);
	}
}