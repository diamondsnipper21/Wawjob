<?php namespace iJobDesk\Http\Controllers\Admin\Super;

use Illuminate\Http\Request;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;

use Config;
use Auth;

// Models
use iJobDesk\Models\ContactUs;
use iJobDesk\Models\User;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\EmailTemplate;

class ContactUsController extends BaseController {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->page_title = 'Contact Us';
        parent::__construct();
	}

	public function detail(Request $request, $id) {
		$contact_us = ContactUs::find($id);

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
        
		$messages 	= AdminMessage::where('message_type', AdminMessage::MESSAGE_TYPE_CONTACT)
								  ->where('target_id', $id)
								  ->get();

        return view('pages.admin.super.contact_us.detail', [
            'page' => 'super.contact_us.detail',
            'contact_us' => $contact_us,
            'messages' => $messages
        ]);  
	}

	public function send(Request $request, $id) {
		$user  = Auth::user();
		$contact_us = ContactUs::find($id);

		$message = $request->input('message');
		if ($message) {
			// Message From User.
			$prev_message = AdminMessage::where('target_id', $id)
										->whereNull('sender_id')
										->where('message_type', AdminMessage::MESSAGE_TYPE_CONTACT)
										->first();

			$admin_message = new AdminMessage();

			$admin_message->message_type 	= AdminMessage::MESSAGE_TYPE_CONTACT;
			$admin_message->target_id 		= $id;
			$admin_message->sender_id 		= $user->id;
			$admin_message->message 		= $message;
			$admin_message->save();

			$sent = EmailTemplate::send(null, 'CONTACT_ADMIN_REPLY', User::ROLE_USER_SUPER_ADMIN, [
				'CUSTOMER_NAME' 	=> $contact_us->fullname,
				'MESSAGE'  			=> strip_tags(nl2br($message), '<br>'),
				'SUBJECT'  			=> strip_tags($contact_us->subject),
				'RECEIVED_DATE'  	=> date('l, M j, Y', strtotime($prev_message->created_at)),
				'CUSTOMER_MAIL'  	=> $contact_us->email,
				'OLD_MESSAGE'  		=> strip_tags(nl2br($prev_message->message), '<br>'),
			], $contact_us->email, $contact_us->fullname);

			if ($sent)
				add_message('Your message have been sent successfully.', 'success');
			else
				add_message('Your message have not been sent. Please try again.', 'danger');
		}

		return $this->detail($request, $id);
	}
}