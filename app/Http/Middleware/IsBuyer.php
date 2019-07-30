<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iJobDesk\Models\User;

class IsBuyer extends IsCustomer {
    protected $roles = [User::ROLE_USER_BUYER];
}