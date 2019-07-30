<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class QueueWechatDeposit extends Model {

	/**
   	* The table associated with the model.
   	*
   	* @var string
   	*/
	protected $table = 'queue_wechat_deposits';

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

    const STATUS_WAITING_QRCODE     = 0;
    const STATUS_WAITING_PAYMENT    = 1;
    const STATUS_APPROVED_PAYMENT   = 2;

    function __construct() {
        parent::__construct();
    }

    public function isWaitingPayment() {
        return $this->status == self::STATUS_WAITING_PAYMENT;
    }

    public function isDone() {
        return $this->status == self::STATUS_APPROVED_PAYMENT;
    }
}