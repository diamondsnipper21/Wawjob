<?php namespace iJobDesk\Models;


class UserSkillPoint extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'user_skill_points';

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = true;

	function __construct() {
        parent::__construct();
    }
}