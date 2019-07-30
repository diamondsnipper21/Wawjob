<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Session;
use Cookie;
use Route;
use Illuminate\Contracts\Auth\Guard;

use iJobDesk\Models\UserSecurityQuestion;

class IsSecurity {

	/**
	* The Guard implementation.
	*
	* @var Guard
	*/
	protected $auth;

	/**
	* Create a new filter instance.
	*
	* @param  Guard  $auth
	* @return void
	*/
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	* Handle an incoming request.
	*
	* @author Ro Un Nam
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle($request, Closure $next)
	{
		if ($this->auth->guest()) {
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			} else {
				return redirect()->route('user.login');
			}
		}

		$user = $this->auth->user();

		// Get user security question
		$userSecurityQuestion = UserSecurityQuestion::getUserSecurityQueston($user->id);

		// Not set up security question
		if ( !$userSecurityQuestion ) {
			// return $next($request);
			return redirect()->route('user.change_security_question');
		}

		if ( Cookie::get('remember_answer') || Session::get('user_secured')) {
			return $next($request);
		} else {
			Session::put('request_route', Route::currentRouteName());
			return redirect()->route('user.security_question');
		}
	}
}