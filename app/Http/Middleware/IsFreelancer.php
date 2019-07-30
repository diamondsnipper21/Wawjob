<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use iJobDesk\Models\User;

class IsFreelancer extends IsCustomer {
  	protected $roles = [User::ROLE_USER_FREELANCER];
}