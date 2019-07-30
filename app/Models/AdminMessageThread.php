<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

use iJobDesk\Models\Views\ViewUser;

class AdminMessageThread extends Model {

	use SoftDeletes;

	protected $table = 'admin_message_threads';

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

    function __construct() {
        parent::__construct();
    }

    public function receivers() {
    	$receiver_ids = explode_bracket($this->to);

    	$receivers = [];
    	foreach ($receiver_ids as $receiver_id) {
    		$receiver = ViewUser::find($receiver_id);

    		if (!$receiver)
    			continue;

    		$receivers[] = $receiver->link();
    	}

    	return implode(', ', $receivers);
    }
}