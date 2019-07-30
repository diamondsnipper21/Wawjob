<?php namespace iJobDesk\Models;

class UserToken extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'user_tokens';

	/**
	* The attributes that should be mutated to dates.
	*
	* @var array
	*/
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = true;

	const TYPE_VERIFY_ACCOUNT = 0;
	const TYPE_FORGOT_PASSWORD = 1;
	const TYPE_API_V1 = 2;
	const TYPE_LOGIN_BLOCKED = 3;
}