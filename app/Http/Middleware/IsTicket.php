<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iJobDesk\Models\User;

class IsTicket extends IsCustomer {
    protected $roles = [User::ROLE_USER_TICKET_MANAGER];
    protected $login = 'admin.user.login';
}