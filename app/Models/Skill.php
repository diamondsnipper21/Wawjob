<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model {

	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'skills';

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

	const USER_POINT_SKILLS = [
		'laravel',
		'magento',
		'mysql',
		'php',
		'ruby',		
		'wordpress'
	];

	function __construct() {
        parent::__construct();
    }

	/**
	* Get the users.
	*/
	public function users()
	{
		return $this->hasMany('iJobDesk\Models\UserSkill', 'skill_id');
	}

	/**
	* Get the projects.
	*/
	public function projects()
	{
		return $this->hasMany('iJobDesk\Models\ProjectSkill', 'skill_id');
	}

	public static function getName($id) {
		$skill = self::where('id', $id)->first();

		if ( $skill ) {
			return $skill->name;
		}

		return false;

		return '';
	}
}