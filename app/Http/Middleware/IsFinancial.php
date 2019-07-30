<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iJobDesk\Models\User;

class IsFinancial extends IsCustomer {
    protected $roles = [User::ROLE_USER_FINANCIAL_MANAGER];
    protected $login = 'admin.user.login';
}