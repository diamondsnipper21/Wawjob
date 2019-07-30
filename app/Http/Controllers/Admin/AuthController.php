<?php namespace iJobDesk\Http\Controllers\Admin;

/**
 * @author KCG
 * @since June 8, 2017
 */


use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;
use Session;
use Config;
use iJobDesk\Models\User;
use iJobDesk\Models\UserProfile;
use iJobDesk\Models\UserContact;
use iJobDesk\Models\UserAnalytic;

class AuthController extends Controller {

	/**
	* Authenticate user info.
	*
	* @author KCG
	* @param  Request $request
	* @return Response
	*/
	public function login(Request $request) {
		$user = Auth::user();

		$allowed_ips = [
			'175.163.104.93',
			'175.163.98.25',
			'175.163.104.163',
			'175.170.13.209',
			'175.163.104.107',
			'175.163.99.9',
			'119.112.153.185',
			'119.112.154.81',
			'113.226.199.238',

			'185.95.16.209', // Boss IP
			'104.128.234.158' // Boss IP
		];

		$ip = \Request::getClientIp();

		if (!in_array($ip, $allowed_ips) && $ip != '127.0.0.1') {
			return redirect()->route('home');
		}

		if ($user) {
			$redirect = getRedirectByRole($user);
    		return redirect()->route($redirect);
		}

		// If user input info
		if ($request->isMethod('post')) {
			$checklist = ['username', 'email'];
			$error = 'Invalid user info, please try again.';

			// Gather user information from request.
			$username = $request->input('username');
			$password = $request->input('password');
			$remember = $request->input('remember');

			// Attempt to login to the system.
			foreach ($checklist as $key) {
				$credential = [
					'password' => $password
				];
				$credential[$key] = $username;

				if (Auth::attempt($credential, $remember)) {
					$user = Auth::user();

					if ($user->status == User::STATUS_AVAILABLE) {
						// Log user login
						UserAnalytic::insert([
							'user_id' => $user->id,
							'login_ipv4' => $_SERVER['REMOTE_ADDR'],
							'logged_at' => date("Y-m-d H:i:s")
						]);

						// if user doesn't have profile, create new profile for this user.
						if (!$user->profile) {
							$profile = new UserProfile();
							$profile->user_id = $user->id;

							$profile->save();
						}

						if (!$user->contact) {
							$contact = new UserContact();
							$contact->user_id = $user->id;

							$contact->save();
						}

						// Redirect to the welcome pages by user role.
						$redirect = getRedirectByRole($user);

						return redirect()->intended(route($redirect));
					} else {
						Auth::logout();
					}
				}
			}

			// Flash email to the session.
			$request->flashOnly('username', 'remember');
		}

		if (!empty($error)) {
			add_message($error, 'danger');
		}

		view()->share('res_version', Config::get('settings.res_version.backend'));

		return view('pages.admin.auth.login', [
			'page' => 'auth.login'
		]);
	}

	/**
	* Log the user out of the application.
	*
	* @return Response
	*/
	public function logout() {
		$user = Auth::user();

		if ( $user ) {
			// Log user logout
			UserAnalytic::insert([
				'user_id' => $user->id,
				'type' => 1,
				'login_ipv4' => $_SERVER['REMOTE_ADDR'],
				'logged_at' => date("Y-m-d H:i:s")
			]);
		}

		// Log the user out.
		Auth::logout();

		// Delete session for user security question
		Session::forget('user_secured');

		return redirect()->route('admin.user.login');
	}
}