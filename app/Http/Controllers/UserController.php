<?php 
namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Auth;
use Config;
use Cookie;
use DB;
use Session;
use Storage;
use Exception;
use Validator;
use Log;

// Models
use iJobDesk\Models\Category;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Country;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\Language;
use iJobDesk\Models\ProfileViewHistory;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\ProjectInvitation;
use iJobDesk\Models\ProjectOffer;
use iJobDesk\Models\Role;

use iJobDesk\Models\SecurityQuestion;
use iJobDesk\Models\Settings;
use iJobDesk\Models\Skill;

use iJobDesk\Models\Timezone;
use iJobDesk\Models\TransactionLocal;

use iJobDesk\Models\User;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\UserCompany;
use iJobDesk\Models\UserCompanyContact;
use iJobDesk\Models\UserNotificationSetting;
use iJobDesk\Models\UserPortfolio;
use iJobDesk\Models\UserProfile;
use iJobDesk\Models\UserSecurityQuestion;
use iJobDesk\Models\UserStat;
use iJobDesk\Models\UserToken;
use iJobDesk\Models\Wallet;

// ViewModels
use iJobDesk\Models\Views\ViewUser;

// Controllers
use iJobDesk\Http\Controllers\Frontend\User\ProfileController;

class UserController extends Controller {

    /**
    * Constructor
    */

    public function __construct()
    {
        view()->share([
            'countries' => Country::all(),
            'languages' => Language::all(),
        ]);

        parent::__construct();
    }
    
    /**
    * user/change-password
    *
    * @author Ri Chol Min
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */

    public function change_password(Request $request)
    {
        $user = Auth::user();

        if ( !$user ) {
            return redirect()->route('user.login');
        }

        // If user input info
        if ( $request->isMethod('post') ) {
			if ( $user->isSuspended() ) {
				return redirect()->route('user.change_password');
			}

            $credential = [
                'email' => $user->email,
                'password' => $request->input('old_password')
            ];

            if ( Auth::validate($credential) ) {
                $user->password = bcrypt($request->input('new_password'));
                $user->save();

                $user->updateLastActivity();

                add_message( trans('user.change_password.success'), 'success' );
            } else {
                $user->try_password = $user->try_password + 1;
                if ( $user->try_password >= User::TOTAL_TRY_SECURITY_ANSWER ) {
                    $user->login_blocked = 1;
                    $_POST['_reason'] = 'Auto Login Blocked by trying with wrong password on the change password';
                }

                $user->save();

                if ( $user->try_password >= User::TOTAL_TRY_SECURITY_ANSWER ) {
                    unset($_POST['_reason']);

                    Auth::logout();

                    add_message( trans('user.login.error_blocked_with_password'), 'danger' );

                    return redirect()->route('user.login');
                }

                add_message( trans('user.change_password.error_mismatch_old_password'), 'danger' );
            }
        }

        return view('pages.user.change_password', [
            'page' => 'user.change_password',
            'user' => $user,
            'error' => isset($error) ? $error : null,
        ]);
    }

    /**
    * user/security-question
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function security_question(Request $request)
    {
        $user = Auth::user();

        if ( !$user ) {
            return redirect()->route('user.login');
        }

        // Get user security question
        $userSecurityQuestion = UserSecurityQuestion::getUserSecurityQueston($user->id);

        if ( $userSecurityQuestion ) {

            $remember_answer = Cookie::get('remember_answer');

            if ( $request->isMethod('post') ) {

                if ( md5($request->input('answer')) == $userSecurityQuestion->answer ) {
                    Session::put('user_secured', $user->id);

                    $user->updateLastActivity();

                    // Redirect to correct route
                    $request_route = Session::get('request_route');
                    if ( $request_route ) {
                        Session::forget('request_route');
                    } else {
                        $request_route = 'user.contact_info';
                    }

                    // Set cookie with remember
                    if ( $request->input('remember') ) {
                        $minutes = 14 * 24 * 60; // for 2 weeks = 14 days
                        Cookie::forget('remember_answer');
                        $remember_answer = cookie('remember_answer', $request->input('answer'), $minutes);

                        return redirect()->route($request_route)->withCookie($remember_answer);
                    }

                    return redirect()->route($request_route);
                } else {
                    $user->try_question = $user->try_question + 1;
                    if ( $user->try_question >= User::TOTAL_TRY_SECURITY_ANSWER ) {
                        $_POST['_reason'] = 'Auto Login Blocked by trying with wrong security question';
                        $user->login_blocked = 1;
                    }

                    $user->save();
                    unset($_POST['_reason']);

                    if ( $user->try_question >= User::TOTAL_TRY_SECURITY_ANSWER ) {
                        Auth::logout();

                        add_message( trans('user.login.error_blocked'), 'danger' );

                        return redirect()->route('user.login');
                    }

                    add_message( trans('user.change_security_question.error_mismatch_old_answer'), 'danger' );
                }
            }

            if ($user->try_question == 0) {
                $back_url = redirect()->getUrlGenerator()->previous();
            } else {
                $back_url = $request->input('back_url');
                if ($back_url == '') {
                    $back_url = redirect()->getUrlGenerator()->previous();
                }
            }

			return view('pages.user.security_question', [
				'page' => 'user.security_question',
				'user' => $user,
				'user_security_question' => $userSecurityQuestion,
				'error' => isset($error) ? $error : null,
				'remember_answer' => $remember_answer,
				'back_url' => $back_url,
			]);

        } else {

            return redirect()->route('user.change_security_question');

        }

    }

    /**
    * user/change-security-question
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function change_security_question(Request $request)
    {
        $user = Auth::user();

        if ( !$user ) {
            return redirect()->route('user.login');
        }

        // Get user security question
        $userSecurityQuestion = UserSecurityQuestion::getUserSecurityQueston($user->id);
        
        // If user has set the security question and not authorized
        if ( $userSecurityQuestion && !Session::get('user_secured') && !Cookie::get('remember_answer') ) {
        	return redirect()->route('user.security_question');
        }

        // Get all security questions
        $securityQuestions = SecurityQuestion::where('is_active', 1)
                                             ->whereNull('deleted_at')
                                             ->orderby('question', 'asc')
                                             ->select(['id', 'question', 'category_id'])
                                             ->get();

        // If user input info
        if ( $request->isMethod('post') ) {
            if ( $user->isSuspended() ) {
				return redirect()->route('user.change_security_question');
			}

            if ( $request->input('_action') == 'create' ) {
                $userSecurityQuestion = new UserSecurityQuestion;
                $userSecurityQuestion->user_id = $user->id;
                $userSecurityQuestion->question_id = $request->input('question_id');
                $userSecurityQuestion->answer = md5($request->input('answer'));

                if ( $userSecurityQuestion->save() ) {
                    Session::put('user_secured', $user->id);

                    add_message( trans('user.change_security_question.success_create_security_question'), 'success' );

                    $user->updateLastActivity();
                } else {
                    $request->flashOnly('question_id', 'answer');
                    add_message( trans('user.change_security_question.error_create_security_question'), 'danger' );
                }
            } else {
                // Match old answer
                if ( md5($request->input('old_answer')) == $userSecurityQuestion->answer ) {
                    $userSecurityQuestion->question_id = $request->input('question_id');
                    $userSecurityQuestion->answer = md5($request->input('answer'));
                    if ( $userSecurityQuestion->save() ) {
                        Session::put('user_secured', $user->id);
                        add_message( trans('user.change_security_question.success_update_security_question'), 'success' );

                        $user->updateLastActivity();

                        // Send email for changing security question.
                        EmailTemplate::send($user, 'SECURITY_QUESTION_CHANGED', 0, [
                            'USER' => $user->fullname(),
                            'CONTACT_US_URL' => route('frontend.contact_us')
                        ]);
                    } else {
                        $request->flashOnly('question_id', 'answer');
                        add_message( trans('user.change_security_question.error_update_security_question'), 'danger' );
                    }
                } else {
                    $request->flashOnly('question_id', 'answer');

                    $user->try_question = $user->try_question + 1;
                    if ( $user->try_question >= User::TOTAL_TRY_SECURITY_ANSWER ) {
                        $_POST['_reason'] = 'Auto Login Blocked by trying with wrong security question';
                        $user->login_blocked = 1;
                    }

                    $user->save();
                    unset($_POST['_reason']);

                    if ( $user->try_question >= User::TOTAL_TRY_SECURITY_ANSWER ) {
                        Auth::logout();

                        // Send email to super admins
                        EmailTemplate::sendToSuperAdmin('ACCOUNT_BLOCKED_BY_SECURITY_QUESTION', User::ROLE_USER_SUPER_ADMIN);
						EmailTemplate::send($user, 'ACCOUNT_BLOCKED_BY_SECURITY_QUESTION', 0, [
							'USER' => $user->fullname()
						]);

                        add_message( trans('user.login.error_blocked'), 'danger' );

                        return redirect()->route('user.login');
                    }
                    
                    add_message( trans('user.change_security_question.error_mismatch_old_answer'), 'danger' );
                }
            }

            if ( $request->input('remember') ) {
                $minutes = 14 * 24 * 60; // for 2 weeks = 14 days
                Cookie::forget('remember_answer');
                $remember_answer = cookie('remember_answer', $request->input('answer'), $minutes);

                return redirect()->route('user.change_security_question')->withCookie($remember_answer);
            }
	        
        }

        $hasSecurityQuestion = false;
        if ( $userSecurityQuestion ) {
            $hasSecurityQuestion = true;
        }

        return view('pages.user.change_security_question', [
            'page' => 'user.change_security_question',
            'user' => $user,
            'security_questions' => $securityQuestions,
            'user_security_question' => $userSecurityQuestion,
            'has_security_question' => $hasSecurityQuestion,
        ]);
    }    

    /**
    * user/notification-settings
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function notification_settings(Request $request, $user = null) {

        if (empty($user))
            $user = Auth::user();

        if ( !$user ) {
            return redirect()->route('user.login');
        }

        // Get notification settings from config
        $config_settings = Config::get('notification_settings');

        // Get user notification settings
        $user_notification_settings = UserNotificationSetting::getUserNotificationSettings($user->id);

        // If user input info
        if ( $request->isMethod('post') ) {
			if ( $user->isSuspended() ) {
				return redirect()->route('user.notification_settings');
			}

            if ( !$user_notification_settings ) {
                $user_notification_settings = new UserNotificationSetting;
                $user_notification_settings->user_id = $user->id;
            }

            foreach ( $config_settings as $category => $settings ) {
                foreach ( $settings as $setting ) {
                    if ( isset($request->notification_settings[$setting]) ) {
                        $user_notification_settings->$setting = 1;
                    } else {
                        $user_notification_settings->$setting = 0;
                    }
                }
            }

            if ( $user_notification_settings->save() ) {
                add_message( trans('user.notification_settings.message_success_update_notification_settings'), 'success');

                $user->updateLastActivity();
            } else {
                add_message( trans('user.notification_settings.message_error_update_notification_settings'), 'danger' );
            }
        }

        $notification_settings = [];

        foreach ( $config_settings as $category => $settings ) {
            foreach ( $settings as $setting ) {
                if ( !$user_notification_settings || $user_notification_settings->$setting ) {
                    $notification_settings[$category][$setting] = 1;
                } else {
                    $notification_settings[$category][$setting] = 0;
                }
            }
        }

        return view('pages.user.notification_settings', [
            'page'        => (Auth::user()->isAdmin()?'super.user.commons.notification_settings':'user.notification_settings'),
            'user' => $user,
            'notification_settings' => $notification_settings,
        ]);
    }

    /**
    * Contact Info Page (user/contact-info)
    *
    * @author nada
    * @since Jan 18, 2016
    * @version 1.0
    * @param  Request $request
    * @return Response
    */
    public function contact_info(Request $request, $section = 'all')
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('user.login');
        }

        $timezones = Timezone::orderBy('gmt_offset', 'asc')->get();
        $countries = Country::all();

        if ( $section == 'account' ) {    

            if ( $request->isMethod('post') ) {
				if ( $user->isSuspended() ) {
					return redirect()->route('user.contact_info');
				}

                $validator = Validator::make($request->all(), [
                    'email'          => 'required|email',
                    'firstName'      => 'required|max:50',
                    'lastName'       => 'required|max:50',
                    'phone'          => 'required',
                    'is_company'     => 'required',
                    'timezoneId'     => [
                        'required',
                        Rule::in(array_pluck($timezones, 'id'))
                    ],
                    'countryCode'    => [
                        'required',
                        Rule::in(array_pluck($countries, 'charcode'))
                    ],
                    'address'        => 'required',
                    'city'           => 'required',
                    'state'          => 'required'
                ]);

                if ( $validator->fails() ) {
                    $errors = $validator->messages();
                    if ( $errors->all() ) {
                        foreach ( $errors->all() as $error )
                            add_message($error, 'danger');
                    }
                } else {
                    // Check email
                    if ( $user->email != $request->input('email') ) {
                    	// Need to verify email address
                    	$_POST['_reason'] = 'Auto Login Blocked by changing email address';
                        $user->email = $request->input('email');
                    	$user->login_blocked = 1;
                    	$user->save();

                    	// Send verification email
						$token = hash_hmac('sha256', str_random(40), config('auth.password.key'));

						UserToken::where('user_id', $user->id)->where('type', UserToken::TYPE_VERIFY_ACCOUNT)->delete();

						$user_token = new UserToken;
						$user_token->user_id = $user->id;
						$user_token->type = UserToken::TYPE_VERIFY_ACCOUNT;
						$user_token->token = $token;
						$user_token->save();

						EmailTemplate::send($user, 'VERIFY_EMAIL', 0, [
							'USER' => $user->fullname(),
							'URL'  => route('user.signup.verify', ['token' => $token])
						]);

                        Auth::logout();

                        add_message( trans('page.auth.signup.verify.verify_your_email_address'), 'danger' );
                        
                        return redirect()->route('user.login');
                    }

                    $user->email      = $request->input('email');
                    $user->is_company = $request->input('is_company');
                    $user->save();

                    if ( !$user->isCompany() ) {
	                    $first_name = $request->input('firstName');
	                    $last_name  = $request->input('lastName');
	                    $user->contact->first_name      = $request->input('firstName');
	                    $user->contact->last_name       = $request->input('lastName');

	                    if (((!$user->isBuyer() && $user->id_verified != 1) || ($user->isBuyer() && $user->myBalance() == 0)) && ($user->contact->isDirty('first_name') || $user->contact->isDirty('last_name'))) {
	                        $user->requireIDVerification();
	                    }
	                }

                    $user->contact->timezone_id     = $request->input('timezoneId');
                    $user->contact->country_code    = $request->input('countryCode');
                    $user->contact->city            = $request->input('city');
                    $user->contact->address         = $request->input('address');
                    $user->contact->address2        = $request->input('address2');
                    $user->contact->state           = $request->input('state');
                    $user->contact->phone           = $request->input('phone');

                    $user->contact->save();

                    $user->updateLastActivity();
                }
            }

            return redirect()->route('user.contact_info');
        }

        if ( $section == 'company' ) { // Company Detail
            if ( $request->isMethod('post') ) {
                $company = new UserCompany();
                if ( $user->company != null )
                    $company = $user->company;

                $validator = Validator::make($request->all(), [
                    'name'            => 'required',
                    'website'         => 'required|url',
                    'address1'        => 'required',
                    'phone'           => 'required'
                ]);

                if ( $validator->fails() ) {
                    $errors = $validator->messages();
                    if ( $errors->all() ) {
                        foreach ( $errors->all() as $error )
                            add_message($error, 'danger');
                    }
                } else {
                    $old_name = $company->name;

                    $company->name            = $request->input('name');
                    $company->phone           = $request->input('phone');

                    if ( $old_name && $company->isDirty('name') ) {
	                    $user->requireIDVerification();
	                }

                    $company->website         = $request->input('website');
                    $company->tagline         = $request->input('tagline');                    
                    $company->address1        = $request->input('address1');
                    $company->address2        = $request->input('address2');
                    $company->user_id         = $user->id; 

                    $company->save();

                    $user->updateLastActivity();
                }
            }

            return redirect()->route('user.contact_info');
        }

        if ( $section == 'location' ) { // Company Contact
            if ( $request->isMethod('post') ) {
                $company_contact = new UserCompanyContact();
                if ($user->company_contact)
                    $company_contact = $user->company_contact;

                $validator = Validator::make($request->all(), [
                    'timezone_id'     => [
                        'required',
                        Rule::in(array_pluck($timezones, 'id'))
                    ],
                    'country_code'    => [
                        'required',
                        Rule::in(array_pluck($countries, 'charcode'))
                    ],
                    'phone'          => 'required',
                    'address'        => 'required',
                    'city'           => 'required',
                    'state'          => 'required'
                ]);

                if ( $validator->fails() ) {
                    $errors = $validator->messages();
                    if ( $errors->all() ) {
                        foreach ( $errors->all() as $error )
                            add_message($error, 'danger');
                    }
                } else {
                    $company_contact->timezone_id     = $request->input('timezone_id');
                    $company_contact->country_code    = $request->input('country_code');
                    $company_contact->city            = $request->input('city');
                    $company_contact->address         = $request->input('address');
                    $company_contact->state           = $request->input('state');
                    $company_contact->phone           = $request->input('phone');
                    $company_contact->user_id         = $user->id;

                    $company_contact->save();

                    $user->updateLastActivity();
                }
            }

            return redirect()->route('user.contact_info');
        }

        return view('pages.user.contact_info', [
            'page' => 'user.contact_info',
            'user' => $user,
            'timezones' => $timezones,
            'countries' => $countries,
            'error' => isset($error) ? $error : null,
            'j_trans' => [
                'duplicated_email' => trans('auth.error_duplicated_email'),
                'confirm_removing_avatar'   => trans('profile.confirm_removing_avatar')
            ]
        ]);

    }

    /**
    * Show user proifle, it depends to the user's setting to show,
    * refrence to share field on user_profiles table.
    * Note that allow only freelancers.
    *
    * @param  integer $user_id User id
    * @return Response
    */
    public function profile(Request $request, $user_id = 0) {
        $controller = new ProfileController();
        return $controller->index($request, $user_id);
    }

    /**
    * user/close-my-account
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function close_my_account(Request $request)
    {
        $user = Auth::user();

        if ( !$user ) {
            return redirect()->route('user.login');
        }

        $action = '';
        $reason = 0;
        $comment = '';

        // If user input info
        if ( $request->isMethod('post') ) {
            $action  = $request->input('_action');
            $reason  = $request->input('reason');
            $comment = $request->input('_reason');

            if ( $action == 'close' ) {
            	$user->closed_reason = $reason;
                $user->save();

                try {
                    if ($user->delete()) {
                        add_message( trans('user.close_my_account.message_success_closed_account'), 'success' );

                        return redirect()->route('user.login');
                    }
                } catch (Exception $e) {
                }
            }
        }

        return view('pages.user.close_my_account', [
            'page'      => 'user.close_my_account',
            'user'      => $user,
            'action'    => $action,
            'reason'    => $reason,
            'comment'   => $comment,
            'error'     => isset($error) ? $error : null,
        ]);
    }   

    /**
    * profile/create-view-history
    * 
    * save the history of profile-view (buyerId and freelancerId)
    * @author sg
    * @param  Request $request
    * @return Response
    */
    public function create_view_history(Request $request, $user_id) {
        $user = Auth::user();

        if ($user->isSuspended())
            abort(404);

        // use the mass-assignment
        ProfileViewHistory::create([
            'buyer_id' => $user->id,
            'user_id' => $user_id,
        ]);

        $user = User::find($user_id);
        if (!$user)
            abort(404);

        return redirect()->to(_route('user.profile', ['uid' => $user->id]));
    }

    /**
    * 
    * 
    * update user locale
    * @author sg
    * @param  Request $request
    * @return Response
    */
    public function update_locale(Request $request, $lang) {
        $user = Auth::user();
        $user->locale = $lang;
        $user->save();
        
        return back();
    }

    /**
     * Ignore warning
     */
    public function ignore_warning(Request $request) {
        $user = Auth::user();
        
        $type       = $request->input('type');
        $target_id  = $request->input('target_id');

        if ($user->isIgnoredWarning($type, $target_id))
            return response()->json(['success' => false]);

        $user->ignoreWarning($type, $target_id);

        return response()->json(['success' => true]);
    }
}