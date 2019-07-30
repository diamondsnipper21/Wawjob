<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ActionHistory extends Model {

	/**
   	* The table associated with the model.
   	*
   	* @var string
   	*/
	protected $table = 'action_histories';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at'];

    /* Type */
    const TYPE_USER         = 1;
    const TYPE_CONTRACT     = 2;
    const TYPE_JOB          = 3;
    const TYPE_TRANSACTION  = 4;

    public function render_description() {
        $description = $this->description;

        $description = str_replace('@#doer_link#', $this->doer->fullname, $description);

        if ($this->type == self::TYPE_USER) {

        }

        return $description;
    }

    /**
    * Get the Admin.
    */
    public function doer() {
        return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'doer_id');
    }

    /**
    * Get the action string.
    */
    public function action_string() {
        $action_string = '';
        $action_type = strtolower($this->action_type);

        if ($action_type == 'activate' || $action_type == 'active') {
            $action_string = 'active';
        } elseif ($action_type == 'suspend') {
            $action_string = 'suspended';
        } elseif ($action_type == 'suspend financial') {
            $action_string = 'suspended';
        } elseif ($action_type == 'delete') {
            $action_string = 'deleted';
        } elseif ($action_type == 'update') {
            $action_string = 'updated';
        } elseif ($action_type == 'close') {
            $action_string = 'closed';
        } elseif ($action_type == 'login blocked') {
            $action_string = 'warning';
        }

        return $action_string;
    }
}