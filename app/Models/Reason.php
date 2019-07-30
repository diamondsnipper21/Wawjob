<?php namespace iJobDesk\Models;


class Reason extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'reasons';

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = false;

    const TYPE_USER 			= 1;
    const TYPE_PROJECT 			= 2;
    const TYPE_CONTRACT 		= 3;
    const TYPE_PROPOSAL 		= 4;
    const TYPE_MESSAGE_THREAD 	= 5;
    const TYPE_CONTRACT_MILESTONE = 6;
    const TYPE_TRANSACTION = 7;

    public static $str_reason;
    const REASON_UNRESPONSIVE = 1;
    const REASON_DISPUTE = 2;
    const REASON_OTHER = 3;

    const ACTION_DELETE		= 1;
    const ACTION_SUSPENSION	= 2;
    const ACTION_RELEASE	= 3;
    const ACTION_REFUND		= 4;

    function __construct() {
        parent::__construct();
        
        self::$str_reason = [
            self::REASON_UNRESPONSIVE => 'Unresponsive',
            self::REASON_DISPUTE => 'Dispute',
            self::REASON_OTHER => 'Other', 
        ];
    }

    public function reason_string() {
        if ( isset(self::$str_reason[$this->reason]) ) {
            return self::$str_reason[$this->reason];
        }

        return '';
    }
}