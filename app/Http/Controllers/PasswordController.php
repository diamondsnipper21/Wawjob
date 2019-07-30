<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Validator;
use Mail;
use Hash;

use iJobDesk\Models\User;
use iJobDesk\Models\UserToken;
use iJobDesk\Models\EmailTemplate;

class PasswordController extends Controller
{
	protected $redirectPath = '/';

	/**
	* Create a new password controller instance.
	*
	* @return void
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Display the form to request a password reset link.
	*
	* @return Response
	*/
	public function forgot(Request $request) {
		$user = Auth::user();

		if ($user)
			return redirect()->intended('/');

		if ( $request->isMethod('post') ) {

			$validator = Validator::make($request->all(), ['email' => 'required|email']);

			$email = $request->input('email');
			if ( $validator->fails() ) {
				add_message( $validator->errors()->first('email'), 'danger' );

				return view('pages.auth.forgot', [
					'page' => 'auth.forgot',
					'email' => $email,
				]);
			}

			$user = User::where('email', $email)
						->orWhere('username', $email)
						->first();
						
			if ( !$user ) {
				add_message( trans('page.auth.forgot.invalid_email'), 'danger' );
				return view('pages.auth.forgot', [
					'page' => 'auth.forgot',
					'email' => $email,
				]);
			} elseif ($user && $user->isSuspended()) {
				add_message( trans('page.auth.forgot.suspended_account'), 'danger' );
				return view('pages.auth.forgot', [
					'page' => 'auth.forgot',
					'email' => $email,
				]);

			} else {
				$token = $user->generateToken(UserToken::TYPE_FORGOT_PASSWORD);

				if ( $token ) {
					$reset_url = route('forgot.reset', ['token' => $token]);

					EmailTemplate::send($user, 'FORGOT_PASSWORD', 0, [
						'URL' => $reset_url,
						'USER' => $user->fullname()
					]);

					add_message( trans('page.auth.forgot.submit_get_new_password'), 'success' );
				} else {
					add_message( trans('page.auth.forgot.failed_get_new_password'), 'danger' );
				}

				return view('pages.auth.forgot', [
					'page' => 'auth.forgot',
					'email' => $email,
				]);
			}
			
		}

		return view('pages.auth.forgot', [
			'page' => 'auth.forgot'
		]);
	}

	/**
	* Reset the password from the token
	*
	* @param  string $token
	* @return Response
	*/
	public function reset(Request $request, $token = '')
	{
		$token_error = false;

		if ( !$token ) {
			$token_error = true;
		} else {
			$user_token = UserToken::where('type', UserToken::TYPE_FORGOT_PASSWORD)
								   ->where('token', $token)
								   ->first();
			if ( !$user_token ) {
				$token_error = true;
			}
		}

		if ( $token_error ) {
			add_message( trans('page.auth.forgot.invalid_token'), 'danger' );

			return redirect()->route('forgot');
		}

		if ( $request->isMethod('post') ) {
			$validator = Validator::make($request->all(), [
				'password' => 'required|confirmed',
			]);

			if ( $validator->fails() ) {
				add_message( $validator->errors()->first('password'), 'danger' );

				return view('pages.auth.reset', [
					'page' => 'auth.reset',
					'token' => $token,
				]);
			}

			$user_token = UserToken::where('type', UserToken::TYPE_FORGOT_PASSWORD)
									->where('token', $token)
									->first();
			if ( !$user_token ) {
				add_message( trans('page.auth.forgot.invalid_token'), 'success' );

				return view('pages.auth.reset', [
					'page' => 'auth.reset',
					'token' => $token,
				]);
			}

			$user = User::find($user_token->user_id);
			$user->password = bcrypt($request->input('password'));
			if ( $user->save() ) {
				$user_token->delete();

				EmailTemplate::send($user, 'RESET_PASSWORD', 0, [
					'USER' => $user->fullname()
				]);
			}

			add_message( trans('page.auth.forgot.success_reset_password'), 'success' );
			return redirect()->route('user.login');

			/*
			Auth::login($user);

			// Redirect to the welcome pages by user type.
			if ( $user->isFreelancer() ) {
				return redirect()->route('user.dashboard');
			} else if ( $user->isBuyer() ) {
				return redirect()->route('user.dashboard');
			} else {
				return redirect()->route('home');
			}
			*/
		}

		return view('pages.auth.reset', [
			'page' => 'auth.reset',
			'token' => $token,
		]);
	}
}
