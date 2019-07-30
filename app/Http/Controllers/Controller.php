<?php namespace iJobDesk\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;
use Storage;
use Config;
use App;
use Session;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\File;
use iJobDesk\Models\Contract;
use iJobDesk\Models\UserIgnoredWarning;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\Settings;
use iJobDesk\Http\Controllers\FileController;

// View Creators
use iJobDesk\Http\ViewCreators\MenuCreator;
use iJobDesk\Http\ViewCreators\SidebarCreator;
use iJobDesk\Http\ViewCreators\UserViewCreator;

class Controller extends BaseController {

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $auth_user;

    protected $user_timezone_name;
    protected $user_timezone_offset;
    protected $server_timezone_name;
    protected $server_timezone_offset;

    /**
    * Constructor
    */
    public function __construct() {
        $this->middleware(function (Request $request, $next) {
            $redirect = $this->beforeAction($request);
            
            if (!$redirect)
                return $next($request);

            return $redirect;
        });
    }

    public function beforeAction(Request $request = null) {
            
        // Do something with authenticated user.
        $user = Auth::user();

        $settings = Config::get('settings');

        // Set the server timezone name and offset
        $this->server_timezone_name = date_default_timezone_get();
        $this->server_timezone_offset = getTimezoneOffset('UTC', $this->server_timezone_name);

        // Share the global vars with view.
        if ( $user ) {
            if ( $user->isAdmin() ) {
                $unread_notifications = UserNotification::getUnread($user->id);
                $unread_cnt = count($unread_notifications);
            } else {
                $notifications = UserNotification::getUnread($user->id, true); // unlimited.
                $unread_notifications = isset($notifications[0]) ? $notifications[0] : [];
                $unread_cnt = isset($notifications[1]) ? $notifications[1] : 0;
            }

            session(['system_notifications' => UserNotification::getSystem($user->id)]);

            $unread_ticket_messages = TicketComment::unreadsCount();

            view()->share([
                'unread_notifications' => $unread_notifications,
                'unread_cnt' => $unread_cnt == 0 ? '' : $unread_cnt,
                'unread_ticket_messages' => $unread_ticket_messages > 0 ? $unread_ticket_messages : '',
                'use_account_both' => $settings['frontend']['use_account_both'],
            ]);

            // Locale 
            view()->share('format_date', 'M d');
            view()->share('format_date2', 'M d, Y');
            view()->share('format_time', 'g:i A');

            $locale = $user->getLocale();
            if ($locale) {
                App::setLocale($locale);

                if ( $locale != 'en' ) {
                    view()->share('format_date', 'm/d');
                    view()->share('format_date2', 'Y/m/d');
                    view()->share('format_time', 'G:i');
                }
            }

            // Set the user timezone name and offset. ex: America/New_York, -05:00
            $user_timezone = $user->getTimezoneInfo();
            $this->user_timezone_offset = $user_timezone[1];
            $this->user_timezone_name = $user_timezone[0];
        } else {
            $this->user_timezone_offset = $this->server_timezone_offset;
            $this->user_timezone_name = $this->server_timezone_name;
        }

        $this->auth_user = $user;

        // Role Identifier
        if ($this->auth_user)
            view()->share('role_id', $this->auth_user->role_identifier());
        else
            view()->share('role_id', 'guest');

        view()->share('current_user', $user ? $user : false);
        view()->share('user_timezone_name', $user ? $this->user_timezone_name : $this->server_timezone_name);
        view()->share('server_timezone_name', $this->server_timezone_name);
        view()->share('current_user', $user ? $user : false);
        view()->share('auth_user', $user ? $user : false);
        view()->share('res_version', $settings['res_version']['frontend']);

        // Currency Sign
        view()->share('currency_sign', Settings::get('CURRENCY_SIGN'));

        MenuCreator::create();
        SidebarCreator::create();
        UserViewCreator::create();

        UserIgnoredWarning::add_warnings();

        $full_url = url()->full();
        if ($user && $request) {
            $route_name = $request->route()->getName();
            $full_url = url()->full();

            if (str_is('file.*', $route_name) || 
                str_is('files.*', $route_name) || 
                str_is('avatar.*', $route_name) || 
                str_is('portfolio.*', $route_name) || 
                str_is('screenshot.*', $route_name) ||
                str_is('frontend.help*', $route_name) ||
                str_is('system.*', $route_name) ||
                $route_name == 'user.logout')
                return;

            if ($user->isLoginBlocked()) {
                // Ignore to block for sending message on ticket detail page.
                if ($route_name == 'message.send' && $request->isMethod('post')) {
                    $type = $request->input('_type', null);
                    if ($type == File::TYPE_TICKET_COMMENT || $type == File::TYPE_ID_VERIFICATION)
                        return;
                }

                // If user is blocked to login, the logged in user can go to only ticket page.
                if (!str_is('ticket.*', $route_name)) {
                    return redirect()->route('ticket.list');
                }
            } elseif ($user->isFreelancer() && !$user->isProfileCompleted()) {
                // If user didn't setup profile, Please go to page to setup.
                if ($user->profile_step >= 0 && $user->profile_step <= 7) {
                    // In about me page, action to allow to search skills.
                    if ($user->profile_step == 2 && ($route_name == 'job.search_skills.ajax' || $route_name == 'user.my_profile.remove_avatar') && $request->ajax())
                        return;

                    // In Portfolio, Certification, Employment History, Education, Other Experience
                    // Allow to actions on MyProfileController@add, @delete
                    if ($user->profile_step >= 3 && $user->profile_step <= 7) {
                        if (!$request->isMethod('get') && (
                                str_is('user.my_profile.*', $route_name) || 
                                str_is('profile.*', $route_name))) {
                            return;
                        }
                    }

                    if ( $full_url != route('profile.step', ['step' => $user->profile_step]) ) {
                        if ($user->profile_step == 0 && $full_url != route('profile.start'))
                            return redirect()->route('profile.start');
                        elseif ($user->profile_step != 0 && $full_url != route('profile.step', ['step' => $user->profile_step])) {
                            return redirect()->route('profile.step', ['step' => $user->profile_step]);
                        }
                    }
                }
            }
        }
    }

    /**
    * Return failure flag to ajax caller
    *
    * @author paulz
    * @created Mar 22, 2016
    */
    protected function failed($msg = '') {
        return response()->json([
            'success' => false,
            'msg' => $msg
        ]);
    }

    protected function protectSuspendedUserAction(Request $request) {
        $user = Auth::user();

        if ( $request->isMethod('post') ) {
            if ($user->isSuspended()) {
                abort(401);
            }
        }
    }

    public function create_message(Request $request, $target_id, $message = null) {
        $sender = Auth::user();

        if (!$sender)
            abort(404);

        $message    = $request->input('message');
        $type       = $request->input('type');
        $class      = $request->input('_class');

        if ($class == 'Frontend\\Contract\\Dispute') { // Please refer to Frontend/Contract/DisputeController@send_message function.
            $message    = $target_id; // contract_id
            $target_id  = null;       // ticket_id
        }

        if ($sender->isTicket())
            $class = 'iJobDesk\\Http\\Controllers\\Admin\\Ticket\\' . $class . 'Controller';
        elseif ($sender->isSuper())
            $class = 'iJobDesk\\Http\\Controllers\\Admin\\Super\\' . $class . 'Controller';
        else
            $class = 'iJobDesk\\Http\\Controllers\\' . $class . 'Controller';

        $controller = app($class);
        $controller->beforeAction($request);
        return $controller->send_message($request, $target_id, $message);
    }

    public function unread_message(Request $request, $id, $type) {
        $file_options = File::getOptions();

        if (!array_key_exists($type, $file_options))
            abort(404);

        $class = 'iJobDesk\\Models\\' . File::getOptions()[$type]['class'];

        $message = $class::find($id);

        if (!$message)
            abort(404);

        $message->markedAsRead();

        return ['success' => true];
    }

    /**
     * Set Page Title
     */
    public function setPageTitle($page_title) {
        view()->share('page_title', $page_title);
    }
}