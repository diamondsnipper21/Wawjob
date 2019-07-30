<?php namespace iJobDesk\Models\Views;

use iJobDesk\Models\Model;
class ViewProjectMessage extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'view_project_messages';

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = false;

	public function freelancer() {
		return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'freelancer_id');
	}

	public function buyer() {
		return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'buyer_id');
	}

	public function sender() {
		return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'sender_id');
	}

	public function proposal() {
		return $this->hasOne('iJobDesk\Models\ProjectApplication', 'id', 'proposal_id');
	}
}