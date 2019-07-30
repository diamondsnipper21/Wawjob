<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iJobDesk\Models\User;

class IsAdmin extends IsCustomer {
    protected $roles = [User::ROLE_USER_TICKET_MANAGER, User::ROLE_USER_SUPER_ADMIN];
    protected $login = 'admin.user.login';
}