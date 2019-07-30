<?php namespace iJobDesk\Models;

use Auth;
use DB;
use Log;

use Illuminate\Http\Request;

class Message extends Model {
    const PER_PAGE = 10;

    /**
     * The parameters for infinite loading messages.
     */
    public static function loadMessages(Request $request, $query_builder, $sent = false, $var = null) {
		$message_limit = $request->input('_limit', 0);

        // if created new message, show more record.
        if ($sent)
            $message_limit++;
        else
            $message_limit += Message::PER_PAGE;

        $messages = clone $query_builder;
        $messages = $messages->paginate($message_limit);

        $message_count = $query_builder->count();

        $params = [
        	'message_count' => $message_count,
            'message_limit' => $message_limit,
            'messages'      => $messages,
            'sent_message'  => $sent
        ];

        if ($var)
        	$returns = [
        		$var => $params
        	];
        else
        	$returns = $params;

        view()->share($returns);
    }
}
