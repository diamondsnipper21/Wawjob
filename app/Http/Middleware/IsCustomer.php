<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iJobDesk\Models\User;

class IsCustomer {

    /**
    * The Guard implementation.
    *
    * @var Guard
    */
    protected $auth;

    protected $roles = [User::ROLE_USER_BUYER, User::ROLE_USER_FREELANCER, User::ROLE_USER_SUPER_ADMIN, User::ROLE_USER_TICKET_MANAGER];

    protected $login = 'user.login';

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
            if ( $request->ajax() ) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route($this->login));
            }
        }

        $user = $this->auth->user();

        if ( ($user->isTicket() && $user->isSuspended()) || $user->trashed() ) {
            $this->auth->logout();
            return redirect()->guest(route($this->login));
        }

        if ( $this->roles && $user->isAdmin() ) {
            if ( !in_array($this->roles[0], User::adminRoles()) ) {
            	return response('Denied.', 401);
            }
        }

        foreach ( $user->roles as $role ) {
            if ( in_array($role, $this->roles) !== FALSE ) {
                return $next($request);
            }
        }

        if ( $request->ajax() ) {
            return response('Denied.', 401);
        } else {
            return redirect()->route('home');
        }
    }
}