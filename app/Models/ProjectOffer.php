<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use Auth;

class ProjectOffer extends Model {

	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'project_offers';

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

	const STATUS_NORMAL = 0;
	const STATUS_ACCEPTED = 1;
	const STATUS_DECLINED = 2;
	const STATUS_WITHDRAWN = 3;

	protected static $str_status = [
		self::STATUS_NORMAL   => 'Sent',
		self::STATUS_ACCEPTED => 'Accepted', 
		self::STATUS_DECLINED => 'Declined',
		self::STATUS_WITHDRAWN => 'Withdrawn',
	];

  	function __construct() {
        parent::__construct();
  	}

	public function sender() {
		return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'sender_id');
	}

	public function receiver() {
		return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'receiver_id');
	}

	public function project() {
        return $this->hasOne('iJobDesk\Models\Project', 'id', 'project_id');
    }

    public function contract() {
    	return $this->hasOne('iJobDesk\Models\Contract', 'id', 'contract_id');
    }

    public function is_receiver($user_id) {
		return $this->receiver_id == $user_id;
	}

	public function isAccepted() {
        return $this->status == self::STATUS_ACCEPTED; 
    }

    public function isDeclined() {
        return $this->status == self::STATUS_DECLINED; 
    }

    public function isWithdrawn() {
        return $this->status == self::STATUS_WITHDRAWN; 
    }

    public function isNormal() {
        return $this->status == self::STATUS_NORMAL; 
    }

    public function accept() {
		$this->status = self::STATUS_ACCEPTED;
		$this->save();
	}

	public function decline() {
		$this->status = self::STATUS_DECLINED;
		$this->save();
		$this->contract->refund_milestones();
	}

	public function withdraw() {
		$this->status = self::STATUS_WITHDRAWN;
		$this->save();
		$this->contract->refund_milestones();
	}

	/**
	 * check if buyer sent offer to freelancer or not.
	 */
	public static function isSent($project, $user) {
		return self::where('sender_id', $project->client_id)
				   ->where('receiver_id', $user->id)
				   ->where('project_id', $project->id)
				   ->where('status', ProjectOffer::STATUS_NORMAL)
				   ->exists();
	}
}