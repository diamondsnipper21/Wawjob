<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Auth;
use Storage;
use Config;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Notification;
use iJobDesk\Models\UserNotification;

//DB
use DB;

class NotificationController extends Controller {

    /**
    * Constructor
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Retrieve Notification list
    * @author Brice
    * @since March 23, 2016
    * @version 1.0
    * @param  Request $request
    * @return Response
    */
    public function all(Request $request)
    {
        $user = Auth::user();

        $notification_list = UserNotification::getAll($user->id);
        return view('pages.notification.list', [
            'page'              => 'notification.list',
            'notification_list' => $notification_list,
        ]);
    }

    /**
    * Read a notification
    *
    * @author Brice
    * @since March 22, 2016
    * @version 1.0
    * @param  Request $request
    * @return Response
    */
    public function read(Request $request, $id)
    {
        $user = Auth::user();

        try {
            $app = new UserNotification();
            $result = $app->read($id);
            return response()->json([
                'status' => $result == false ? 'fail' : 'success',
                'notification_id' => $id,
            ]);
        }
        catch(ModelNotFoundException $e) {
            return response()->json([
                'status' => 'success',
                'notification_id' => $id,
            ]);
        }
    }

    /**
    * Delete a notification
    *
    * @author Brice
    * @since March 23, 2016
    * @version 1.0
    * @param  Request $request
    * @return Response
    */
    public function delete(Request $request, $id) {
    	$user = Auth::user();

        try {
            $app = new UserNotification();
            $result = $app->del($id);
        } catch(ModelNotFoundException $e) {
        }

        return redirect()->route('notification.list');
    }
}