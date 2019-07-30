<?php namespace iJobDesk\Models;

/**
 * @author KCG
 * @since June 30, 2017
 * TODO model
 */

use DB;

use Auth;

class Todo extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'todos';

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    // type
    const TYPE_DISPUTE 		= 1;
    const TYPE_REFUND 		= 2;
    const TYPE_WITHDRAW 	= 3;
    const TYPE_PAYMENT 		= 4;
    const TYPE_QUESTION 	= 5;
    const TYPE_OTHER 		= 6;

    // priority
    const PRIORITY_HIGH 	= 1;
    const PRIORITY_MEDIUM 	= 2;
    const PRIORITY_LOW 		= 3;

    // status
    const STATUS_OPEN       = 1;
    const STATUS_COMPLETE   = 2;
    const STATUS_CANCEL     = 3;

  	function __construct() {
        parent::__construct();
  	}

  	public static function options($category) {
  		if ($category == 'type')
  			return [
  				'Dispute' 	=> self::TYPE_DISPUTE, 
  				'Refund' 	=> self::TYPE_REFUND, 
  				'Withdraw' 	=> self::TYPE_WITHDRAW, 
  				'Payment' 	=> self::TYPE_PAYMENT, 
  				'Question' 	=> self::TYPE_QUESTION, 
  				'Other' 	=> self::TYPE_OTHER
  			];
        elseif ($category == 'priority')
            return [
                'High' => self::PRIORITY_HIGH, 
                'Medium' => self::PRIORITY_MEDIUM, 
                'Low'    => self::PRIORITY_LOW
            ];
        elseif ($category == 'status')
            return [
                'Open'          => self::STATUS_OPEN, 
                'Completed'     => self::STATUS_COMPLETE, 
                'Cancelled'     => self::STATUS_CANCEL
            ];
  		return [];
  	}

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('type', File::TYPE_TODO);
    }

    public function related_ticket() {
        return $this->belongsTo('iJobDesk\Models\Ticket', 'related_ticket_id');
    }

    public function getAssignersAttribute() {
        $users = [];
        
        foreach (explode_bracket($this->assigner_ids) as $user_id) {
            if (empty($user_id))
                continue;
            
            $users[] = User::find($user_id);
        }

        return $users;
    }

    public function creator() {
        return $this->belongsTo('iJobDesk\Models\User', 'creator_id');
    }

    /**
    * Get the message to super admin associated with the ticket.
    *
    * @return mixed
    */
    public function messages() {
        return $this->hasMany('iJobDesk\Models\AdminMessage', 'target_id')
                    ->where('message_type', AdminMessage::MESSAGE_TYPE_TODO);
    }

    public function getStatus() {
        $status = array_search($this->status, self::options('status'));

        if ($status === FALSE)
            $status = 'Open';

        return $status;
    }

    public static function getAvailable() {
        $user = Auth::user();

        if ($user->isSuper())
            return self::whereRaw(true);

        return self::orWhere(function($query) use ($user) {
                $query->orWhere('creator_id', '=', $user->id)
                      ->orWhere('assigner_ids', 'LIKE', "%[$user->id]%");
            });
    }

    /**
     * Get opening todos
     */
    public static function getOpenings($limit = null) {
        $user = Auth::user();

        $todos = self::getAvailable()
            ->where('status', '=', self::STATUS_OPEN)
            ->orderBy('due_date', 'ASC')
            ->orderBy('priority', 'ASC');

        if (!empty($limit)) {
            $todos = $todos->take($limit)->get();
        }

        return $todos;
    }

    /**
     * Check whether this todo is overdued.
     */
    public function isOverdue() {
        return strtotime($this->due_date . ' 23:59:59') < time();
    }

    public function isClosed() {
        return $this->status == self::STATUS_COMPLETE || $this->status == self::STATUS_CANCEL;
    }
}

