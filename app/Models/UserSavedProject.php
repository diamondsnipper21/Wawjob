<?php namespace iJobDesk\Models;

use DB;
use iJobDesk\Models\Project;

class UserSavedProject extends Model {
  	
  	protected $table = 'user_saved_projects';
	
	public function job() {
		return $this->belongsTo('iJobDesk\Models\Project', 'project_id');
	}
}
	