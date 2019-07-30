<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;
use Session;
use Validator;
use Config;

use iJobDesk\Models\User;
use iJobDesk\Models\UserContact;
use iJobDesk\Models\UserProfile;
use iJobDesk\Models\Country;
use iJobDesk\Models\UserAnalytic;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\UserNotificationSetting;
use iJobDesk\Models\UserStat;
use iJobDesk\Models\UserPoint;
use iJobDesk\Models\UserSkillPoint;
use iJobDesk\Models\UserToken;
use iJobDesk\Models\Settings;
use iJobDesk\Models\EmailTemplate;
use Mews\Captcha\Captcha;

class AuthController extends Controller {

	/**
	* Constructor
	*/
	public function __construct() {
		parent::__construct();

		$countries = Country::all();

		view()->share([
			'countries' => $countries,
			'defaults' 	=> [
				'country' 	=> 'US', // China
				'how_hear' 	=> 1, // Google
			],
			'how_hear_options' => [
				'Google' 		=> 1,
				'Friends' 		=> 2,
				'Colleagues' 	=> 3,
				'Facebook' 		=> 4,
				'Twitter' 		=> 5,
				'Other' 		=> 6
			]
		]);
	}


	/**
	* Authenticate user info.
	*
	* @author Sunlight
	* @param  Request $request
	* @return Response
	*/
	public function login(Request $request)
	{
		$user = Auth::user();
        if ( $user ) {
        	return redirect()->route('user.contact_info');
        }

        // Check login blocked token
        if ( isset($request->token) ) {
			$user_token = UserToken::where('type', UserToken::TYPE_LOGIN_BLOCKED)
								   ->where('token', $request->token)
								   ->orderBy('created_at', 'desc')
								   ->first();
			if ( $user_token ) {
				$user = User::find($user_token->user_id);
				if ( $user ) {
					$user->login_blocked = 0;
					$user->try_login = 0;
					$user->save();
				}

				$user_token->delete();
			}
		}

        $show_captcha = 0;

        $from = $request->input('from');
        $to = $request->input('to');

		// If user input info
		if ( $request->isMethod('post') ) {
			// Check Captcha
			$captcha_result = true;
			if ( $request->input('has_captcha') == 1 ) {
				$captcha_result = false;
				$captcha_value = $request->input('captcha');
				if ( app('captcha')->check($captcha_value) ) {
					$captcha_result = true;
				}
			}

			if ( !$captcha_result ) {
				add_message(trans('user.login.error_invalid_captcha'), 'danger');
				$show_captcha = 1;
			} else {

				// Gather user information from request.
				$username = $request->input('username');
				$password = $request->input('password');
				$remember = $request->input('remember') ? true : false;

				if ( filter_var($username, FILTER_VALIDATE_EMAIL) ) {
					$credential = [
						'email' => $username,
						'password' => $password
					];
				} else {
					$credential = [
						'username' => $username,
						'password' => $password
					];
				}

				$error = trans('user.login.error_invalid_login');
				$invalid_credential = false;
				$found_error = false;

				if ( Auth::attempt($credential, $remember) ) {
					$user = Auth::user();

					if ( $user->isAdmin() ) {
						Auth::logout();

						return redirect()->route('user.login');
					}

					switch ($user->status) {
						case User::STATUS_NOT_AVAILABLE:
							$found_error = true;
							add_message( trans('user.login.error_not_verified'), 'danger' );

							Auth::logout();

							// If this user isn't verfied yet, it will be redirected to sending verification email page.
							Session::put('signup_user_id', $user->id);
							$this->sendVerificationEmail($user);

							return redirect()->route('user.signup.success');

							break;

						case User::STATUS_AVAILABLE:
						case User::STATUS_SUSPENDED:
						case User::STATUS_FINANCIAL_SUSPENDED:
							// Log user login
							UserAnalytic::insert([
								'user_id' => $user->id,
								'login_ipv4' => $_SERVER['REMOTE_ADDR'],
								'logged_at' => date("Y-m-d H:i:s")
							]);

							$user->try_password = 0;
							$user->try_question = 0;
							$user->try_login = 0;
                    		$user->save();

							$user->updateLastActivity();

							$redirect = '/';
							// Redirect to the welcome pages by user type.
							if ( $user->isFreelancer() ) {
								$redirect = route('user.dashboard');
							} else if ( $user->isBuyer() ) {
								$redirect = route('user.dashboard');
							} else {
								$redirect = route('admin.dashboard');
							}

							return redirect()->intended($redirect);

							break;

						default:
							if ( !$found_error ) {
								add_message($error, 'danger');
							}

							$found_error = true;

							Auth::logout();
							
							break;
					}
				} else {
					if ( !$found_error ) {
						add_message($error, 'danger');
					}

					$found_error = true;
					$invalid_credential = true;
				}

				// Increase try_login in users table
				if ( $invalid_credential ) {
					$user = User::where(function($query) use ($username) {
									$query->where('username', $username)
										  ->orWhere('email', $username);
								})->first();

					if ( $user ) {
						$user->try_login = $user->try_login + 1;

						if ( $user->try_login >= User::TOTAL_TRY_CAPTCHA ) {
							$show_captcha = 1;
						}

						if ( $user->try_login >= User::TOTAL_TRY_LOGINS ) {
							$_POST['_reason'] = 'Auto Login Blocked by trying with wrong credential';
							$user->login_blocked = 1;

							$user->try_login = User::TOTAL_TRY_LOGINS;
						}

						$user->save();

						unset($_POST['_reason']);
					}
				}
			}

			// Flash email to the session.
			$request->flashOnly('username', 'remember');
		}

		return view('pages.auth.login', [
			'page' => 'auth.login',
			'error' => isset($error) ? $error : null,
			'show_captcha' => $show_captcha,
			'from' => $from
		]);
	}

	/**
	* Author : Ri Chol Min
	*
	* @param  Request $request
	* @return Response
	*/
	public function signup(Request $request) {
		if ( Auth::user() ) {
			return redirect()->intended('/');
		}

		//return redirect()->route('frontend.coming_soon');

		$from = $request->input('from');
		$role = $request->input('role');
		$ref = $request->input('ref');

		$buyer_params = [
			'role' => 'buyer',
		];

		$freelancer_params = [
			'role' => 'freelancer'
		];

		if ( $from ) {
			$buyer_params['from'] = $from;
			$freelancer_params['from'] = $from;
		}

		if ( $ref ) {
			$buyer_params['ref'] = $ref;
			$freelancer_params['ref'] = $ref;
		}

		$buyer_url = route('user.signup.user', $buyer_params);
		$freelancer_url = route('user.signup.user', $freelancer_params);

		return view('pages.auth.signup', [
			'page' => 'auth.signup',
			'buyer_url' => $buyer_url,
			'freelancer_url' => $freelancer_url,
		]);

	}

	/**
	* Author : Ri Chol Min
	*
	* @param  Request $request
	* @return Response
	*/
	public function signup_checkusername(Request $request)
	{
		if ($request->isMethod('get')) {
			$username = $request->input('username_ajax');

			$reserved_user_ids = Config::get('settings.reserved_user_ids');
			foreach ($reserved_user_ids as $reserved_user_id) {
				if (str_is($reserved_user_id, $username))
					return 'error';
			}

			$duplicated_user = User::where('username', $username)
								   ->withTrashed()
								   ->first();
			if ( !$duplicated_user ){
				return 'success';
			}else{
				return 'error';
			}
		}
	}

	/**
	* Author : Ri Chol Min
	*
	* @param  Request $request
	* @return Response
	*/
	public function signup_checkemail(Request $request)
	{
		if ($request->isMethod('get')) {
			$duplicated_user = User::where('email', '=', $request->input('email_ajax'))
								   ->withTrashed()
								   ->first();
			if ( !$duplicated_user ){
				return 'success';
			} else {
				return 'error';
			}
		}
	}

	public function signup_checkfield(Request $request) {
		$result = 'true';
		$error_message = '';

		$field = $request->input('field');
		$value = $request->input($field);

		// For edit profile
		$id = $request->input('id');

		if ( !$field || !$value ) {
			$result = 'false';
		}

		if ( $field == 'captcha' ) {
			if ( !app('captcha')->check($value) ) {
				$result = 'false';
			}
		} else {
			if ( $result == 'true' ) {

				if ($field == 'username') {
					$reserved_user_ids = Config::get('settings.reserved_user_ids');
					foreach ($reserved_user_ids as $reserved_user_id) {
						if (str_is($reserved_user_id, $value))
							return 'false';
					}
				}

				$users = User::where($field, $value)
							 ->withTrashed();

				if ( $id ) {
					$users->where('id', '<>', $id);
				}

				$exists = $users->exists();
				if ( $exists ) {
					$result = 'false';
				}
			}
		}

		return $result;
	}

	/**
	* @author paulz
	* @created Apr 19, 2016
	* @param  Request $request
	* @return Response
	*/
	public function signup_user(Request $request, $role) {
		if ( Auth::user() ) {
			return redirect()->intended('/');
		}

		//return redirect()->route('frontend.coming_soon');

		// If the current user has affiliate id
		$ref_username = $request->input('ref');

		// If user input info
		$submitted = false;
		if ( $request->isMethod('post') ) {

			try {
				
				// Check Captcha
				$captcha_result = false;
				if ( app('captcha')->check($request->input('captcha')) ) {
					$captcha_result = true;
				}

				if ( !$captcha_result ) {
					add_message(trans('user.login.error_invalid_captcha'), 'danger');
				} else {
					$validator = Validator::make($request->all(), [
						'first_name' 	=> 'required',
						'last_name' 	=> 'required',
						'password' 		=> 'required|min:8',
						'username' 		=> 'required|unique:users',
						'email' 		=> 'required|email|unique:users'
					]);

					if ( $validator->fails() ) {
						$errors = $validator->messages();
						if ( $errors->all() ) {
							foreach ( $errors->all() as $error ) {
								add_message($error, 'danger');
							}
						}
					} else {

						// User
						$user = new User;
						$user->email         = $request->input('email');
						$user->username      = strtolower($request->input('username'));
						$user->password      = bcrypt($request->input('password'));
						$user->role 		 = ($role == 'buyer'?User::ROLE_USER_BUYER:User::ROLE_USER_FREELANCER);

						$reserved_user_ids = Config::get('settings.reserved_user_ids');
						foreach ($reserved_user_ids as $reserved_user_id) {
							if (str_is($reserved_user_id, $user->username))
								throw new Exception('Invalid Username.');
						}
						
						if ( $user->save() ) {

							// Contact
							$contact = new UserContact;
							$contact->user_id       = $user->id;
							$contact->first_name    = $request->input('first_name');
							$contact->last_name     = $request->input('last_name');
							$contact->country_code  = $request->input('country');
							$contact->save();

							// User profile
							$profile = new UserProfile;
							$profile->user_id = $user->id;
							$profile->save();

							// Wallet
							$wallet = new Wallet;
							$wallet->user_id = $user->id;
							$wallet->save();

							// Notification Settings
							$notificationSetting = new UserNotificationSetting;
							$notificationSetting->user_id = $user->id;
							$notificationSetting->save();

							// User Stat
							$userStat = new UserStat;
							$userStat->user_id = $user->id;
							$userStat->save();

							if ( $user->isFreelancer() ) {
								// User Point
								$userPoint = new UserPoint;
								$userPoint->user_id = $user->id;

								if ( Settings::get('POINT_NEW_FREELANCER_ENABLED') ) {
									$userPoint->new_freelancer = Settings::get('POINT_NEW_FREELANCER');
								}

								$userPoint->save();

								// User Skill Point
								$userSkillPoint = new UserSkillPoint;
								$userSkillPoint->user_id = $user->id;
								$userSkillPoint->save();
							}

							if ( $ref_username ) {
								$ref_user = User::getByUsername($ref_username);

								if ( $ref_user ) {
									$affiliate = UserAffiliate::where('email', $user->email)
																->where('user_id', $ref_user->id)
																->first();
									if ( !$affiliate ) {
										$affiliate = new UserAffiliate;
										$affiliate->user_id = $ref_user->id;
										$affiliate->email = $user->email;
									}
									
									$affiliate->affiliate_id = $user->id;
									$affiliate->save();
								}
							}

							Session::put('signup_user_id', $user->id);

							$this->sendVerificationEmail($user);

							return redirect()->route('user.signup.success');
						} else {
							add_message(trans('auth.error_something_wrong'), 'danger');
						}
					}
				}

			} catch (Exception $e) {
				add_message($e->getMessage(), 'danger');
			}

			$submitted = true;
			$request->flash();
		}

		return view('pages.auth.signup_user', [
			'page' => 'auth.signup_user',
			'role' => $role,
			'ref' => $ref_username,
			'submitted' => $submitted,
			'j_trans' => [
				'invalid_username' => trans('auth.error_invalid_username'),
				'duplicated_username' => trans('auth.error_duplicated_username'),
				'invalid_email' => trans('auth.error_invalid_email'),
				'duplicated_email' => trans('auth.error_duplicated_email'),
				'invalid_captcha' => trans('auth.error_invalid_captcha')
			]
		]);
	}

	/**
	* Generate url to verify signed up account
	* @param $token
	*/
	protected function generateVerifyUrl($token) {
		return route('user.signup.verify', ['token' => $token]);
	}

	/**
	* Send account verification email
	* @param $user
	*/
	protected function sendVerificationEmail($user) {
		$email_slug = 'SIGNUP_BUYER';
		if ( $user->isFreelancer() ) {
			$email_slug = 'SIGNUP_FREELANCER';
		}

		$token = hash_hmac('sha256', str_random(40), config('auth.password.key'));

		UserToken::where('user_id', $user->id)->where('type', UserToken::TYPE_VERIFY_ACCOUNT)->delete();

		$user_token = new UserToken;
		$user_token->user_id = $user->id;
		$user_token->type = UserToken::TYPE_VERIFY_ACCOUNT;
		$user_token->token = $token;
		$user_token->save();

		$verification_url = $this->generateVerifyUrl($token);

		return EmailTemplate::send($user, $email_slug, 0, [
			'USER' => $user->fullname(),
			'URL'  => $verification_url
		]);
	}

	/**
	* Log the user out of the application.
	*
	* @return Response
	*/
	public function logout() {
		$user = Auth::user();

		if ( !$user ) {
			return redirect('/');
		}

		// Log user logout
		UserAnalytic::insert([
			'user_id' => $user->id,
			'type' => 1,
			'login_ipv4' => $_SERVER['REMOTE_ADDR'],
			'logged_at' => date("Y-m-d H:i:s")
		]);
		
		// Log the user out.
		Auth::logout();

		// Delete session for user security question
		Session::forget('user_secured');

		return redirect()->route('user.login');
	}

	/**
	 * success page after signup.
	 */
	public function signup_success(Request $request) {
		if ( Auth::user() ) {
			return redirect()->intended('/');
		}
		
		$signup_user_id = Session::get('signup_user_id');
		// Session::forget('signup_user_id');

		$signup_user = User::find($signup_user_id);

		if ( !$signup_user ) {
			add_message( trans('page.auth.signup.verify.invalid_token'), 'danger' );
			return redirect()->route('user.login');
		}

		if ( $request->ajax() ) {
			$json = [
				'success' => false
			];

			$action = $request->input('_action');

			if ( $action == 'change' ) {
				$new_email = $request->input('new_email');

				if ( validateEmail($new_email) ) {
					$duplicated = User::where('email', $new_email)
									  ->withTrashed()
									  ->first();
									  
					if ( $duplicated ) {
						$json['message'] = trans('page.auth.signup.success.message_failed_changed_duplicated_email');
					} else {
						$signup_user->email = $new_email;
						$signup_user->save();

						$json['success'] = true;
						$json['message'] = trans('page.auth.signup.success.message_success_changed_email');

						$this->sendVerificationEmail($signup_user);
					}
				} else {
					$json['message'] = trans('page.auth.signup.success.message_failed_changed_email');
				}
			} else {
				if ( $this->sendVerificationEmail($signup_user) ) {
					$json['success'] = true;
					$json['message'] = trans('page.auth.signup.success.success_resent_email', ['email' => $signup_user->email]);
				} else {
					$json['message'] = trans('page.auth.signup.success.failed_sent_email', ['email' => $signup_user->email]);
				}
			}

			if ($json['success'])
				add_message($json['message'], 'success');
			else
				add_message($json['message'], 'danger');
		}

		return view('pages.auth.signup_success', [
			'page' => 'auth.signup_success',
			'user' => $signup_user,
		]);
	}

	/**
	* Verify signup
	* @param  string $token
	* @return Response	
	*/
	public function verify(Request $request, $token) {
		$token_error = false;

		if ( !$token ) {
			$token_error = true;
		} else {
			$user_token = UserToken::where('type', UserToken::TYPE_VERIFY_ACCOUNT)
									->where('token', $token)
									->first();
			if ( !$user_token ) {
				$token_error = true;
			} else {
				$user = User::find($user_token->user_id);
				if ( $user ) {
					$user->status = User::STATUS_AVAILABLE;

					if ( $user->isLoginBlocked() ) {
						$user->login_blocked = 0;
					}

					$user->save();

					// Disable sending email for now.
					/*
					EmailTemplate::send($user, 'ACCOUNT_VERIFIED', 0, [
						'USER' => $user->fullname()
					]);
					*/
				} else {
					$token_error = true;
				}

				$user_token->delete();
			}
		}

		if ( $token_error ) {
			add_message( trans('page.auth.signup.verify.invalid_token'), 'danger' );
		} else {
			add_message( trans('page.auth.signup.verify.success_verified'), 'success' );
		}

		return redirect()->route('user.login');
	}
}