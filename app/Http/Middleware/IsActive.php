<?php namespace iJobDesk\Http\Middleware;

/**
* Check if current user is active or suspended
*/

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iJobDesk\Models\User;

class IsActive {

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
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        if ( $this->auth->guest() ) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->route($this->login);
            }
        }

        $user = $this->auth->user();

        if ( $user->isSuspended() ) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}