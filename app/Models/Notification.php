<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

use iJobDesk\Models\UserNotification;
use iJobDesk\Models\User;

class Notification extends Model {

	use SoftDeletes;

	/**
	* Notifications
	*
	* NOTE: freelancer, buyer, system
	* NOTE: define slug for freelancer, buyer, system
	* @var string
	*/

	//Account
	const ACCOUNT_SUSPENDED = 'ACCOUNT_SUSPENDED';
	const ACCOUNT_REACTIVATED = 'ACCOUNT_REACTIVATED';
	const FINANCIAL_ACCOUNT_SUSPENDED = 'FINANCIAL_ACCOUNT_SUSPENDED';
	const FINANCIAL_ACCOUNT_REACTIVATED = 'FINANCIAL_ACCOUNT_REACTIVATED';

	//Application
	const RECEIVED_JOB_OFFER = 'RECEIVED_JOB_OFFER';
	const RECEIVED_INVITATION = 'RECEIVED_INVITATION';
	const BUYER_JOB_CANCELLED = 'BUYER_JOB_CANCELLED';
	const BUYER_JOB_CLOSED = 'BUYER_JOB_CLOSED';
	const BUYER_JOB_REPOSTED = 'BUYER_JOB_REPOSTED';
	const APPLICATION_DECLINED = 'APPLICATION_DECLINED';

	//Payment
	const PAY_AFFILIATE = 'PAY_AFFILIATE';
	const PAY_BONUS = 'PAY_BONUS';
	const PAY_FIXED = 'PAY_FIXED';
	const PAY_HOURLY = 'PAY_HOURLY';
	const REFUND_IJOBDESK = 'REFUND_IJOBDESK';
	const REFUND = 'REFUND';
	const FUND = 'FUND';
	const RELEASE = 'RELEASE';
	const REFUNDED_FUND = 'REFUNDED_FUND';
	const TIMELOG_REVIEW = 'TIMELOG_REVIEW';
	const BUYER_DEPOSIT = 'BUYER_DEPOSIT';
	const USER_WITHDRAWAL = 'USER_WITHDRAWAL';
	const BUYER_PAY_BONUS = 'BUYER_PAY_BONUS';
	const BUYER_PAY_FIXED = 'BUYER_PAY_FIXED';
	const BUYER_PAY_HOURLY = 'BUYER_PAY_HOURLY';
	const BUYER_REFUND = 'BUYER_REFUND';
	const FREELANCER_REQUESTED_MILESTONE_PAYMENT = 'FREELANCER_REQUESTED_MILESTONE_PAYMENT';
	
	const FREELANCER_ACCEPTED_OFFER = 'FREELANCER_ACCEPTED_OFFER';
	const FREELANCER_DECLINED_OFFER = 'FREELANCER_DECLINED_OFFER';

	const BUYER_FUND = 'BUYER_FUND';
	const BUYER_RELEASE = 'BUYER_RELEASE';

	//Ticket
	const TICKET_CLOSED 	= 'TICKET_CLOSED';

	const ADMIN_TICKET_SOLVED 	= 'ADMIN_TICKET_SOLVED';
	const ADMIN_TICKET_ASSIGNED = 'ADMIN_TICKET_ASSIGNED';

	//Contract
	const CONTRACT_STARTED = 'CONTRACT_STARTED';
	const CONTRACT_CANCELLED = 'CONTRACT_CANCELLED';
	const CONTRACT_CLOSED = 'CONTRACT_CLOSED';
	const CONTRACT_PAUSED = 'CONTRACT_PAUSED';
	const CONTRACT_RESTARTED = 'CONTRACT_RESTARTED';
	
	const CONTRACT_WEEK_LIMIT_HRS = 'CONTRACT_WEEK_LIMIT_HRS';
	const CONTRACT_WEEK_LIMIT_NO = 'CONTRACT_WEEK_LIMIT_NO';
	const CONTRACT_ALLOWED_MANUAL_TIME = 'CONTRACT_ALLOWED_MANUAL_TIME';
	const CONTRACT_NOT_ALLOWED_MANUAL_TIME = 'CONTRACT_NOT_ALLOWED_MANUAL_TIME';
	const CONTRACT_SUSPENDED = 'CONTRACT_SUSPENDED';

	//Message
	const SEND_MESSAGE = 'SEND_MESSAGE';

	// job
	const JOB_ACTIVATED = 'JOB_ACTIVATED';
	const JOB_SUSPENDED = 'JOB_SUSPENDED';
	const JOB_DELETED = 'JOB_DELETED';

	// Dispute
	const DISPUTE_LASTWEEK_BUYER = 'DISPUTE_LASTWEEK_BUYER';
	const DISPUTE_LASTWEEK_FREELANCER = 'DISPUTE_LASTWEEK_FREELANCER';

	// Priority
    const PRIORITY_URGENT 	= 1;
    const PRIORITY_NORMAL 	= 2;
    const PRIORITY_LOW 		= 3;

	/**
	* Notification types
	*
	* @var string
	*/
	const NOTIFICATION_TYPE_NORMAL = 0;
	const NOTIFICATION_TYPE_SYSTEM = 1;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'notifications';

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

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE  = 1;
    const STATUS_DELETE  = 2;

    function __construct() {
        parent::__construct();
    }

	public function isDisabled() {
        return $this->status == self::STATUS_DISABLE; 
    }

	/**
	* Get the all notifications and decode content field.
	*
	* @return array
	*/
	public static function getAll()
	{
		$notifications = self::orderBy('is_const')->orderBy('type', 'desc')->orderBy('slug')->get();

		foreach ($notifications as &$notification) {
			$notification->content = str_replace('"', '&quot;', json_decode($notification->content, true));
		}

		return $notifications;
	}

	/**
	* Get the all slug of notifications.
	*
	* @return array
	*/
	public static function getAllSlugs()
	{
		$slugs = self::select("id", "slug")
		->orderBy('is_const')->orderBy('type', 'desc')->orderBy('slug')->get();
		return $slugs;
	}

	/**
	* Get the content of notification.
	*
	* @return array
	*/
	public static function getContent($id)
	{
		$content = self::select("content", "slug")
		->where("id", $id)->first();
		return $content;
	}

	/**
	* Add the notification for users.
	* @return mixed
	*/
	public static function getWithSlug($slug)
	{
		$notification = self::where("slug", "=", $slug)
		->first();
		return $notification;
	}

	/**
	* Add the notification for users.
	* @return mixed
	*/
	public static function send($slug, $sender_id, $receiver_id, $params = [], $valid_date = null)
	{
		try {
			$now = date('Y-m-d H:i:s');
			$notification = self::where("slug", $slug)
							    ->first();

			if ( !$notification ) {
				return false;
			}

			if ( $notification->isDisabled() ) {
				return false;
			}

			if (!empty($receiver_id)) { // when sending notification to admin
				$user = User::find($receiver_id);

				if (!$user)
					return false;
				
				$user_lang = $user->getLocale();
			}

			if (!empty($user_lang) && isset($params[$user_lang])) {
				$parameters = $params[$user_lang];
			} else {
				$parameters = $params;
				$user_lang = 'EN';//"EN" should be changed by the receiver's language code  
			}

			if (array_key_exists('amount', $parameters) || array_key_exists('AMOUNT', $parameters)) {
				if ( isset($parameters['amount']) )
					$parameters['amount'] = '$' . $parameters['amount'];
				else if ( isset($parameters['AMOUNT']) )
					$parameters['AMOUNT'] = '$' . $parameters['AMOUNT'];
			}

			$content = json_encode(array($user_lang => $parameters));

			return UserNotification::add($notification->id, $content, $sender_id, $receiver_id, $valid_date);
		} catch(Exception $e) {
			Log::error($e->getMessage());
		}

		return false;
	}

	public static function sendToSuperAdmin($slug, $sender_id, $params = [], $valid_date = null) {
		$users = User::getSuperAdmins();

		$sent = true;
		foreach ($users as $user) {
			$sent = self::send($slug, $sender_id, $user->id, $params, $valid_date) && $sent;
		}

		return $sent;
	}

	/**
	* Add the notification for users.
	* @return mixed
	*/
	public static function saveModified($changes)
	{
		$result = [];
		try {
			foreach ($changes as $notification) {
				if (is_numeric($notification["id"])) {
					if (isset($notification['remove']) && $notification['remove'] = true) {
						self::where('id', $notification["id"])
						->delete();
						UserNotification::del($notification["id"], true);
					} else {
						self::where('id', $notification["id"])
						->update(['slug' => $notification['slug'], 
							'content' => json_encode($notification['content']), 
							'is_const' => $notification['is_const'], 
							'type' => $notification['type']]
							);  
					}

				} else {
					$result[$notification["id"]] = self::insertGetId(['slug' => $notification['slug'], 
						'content' => json_encode($notification['content']), 
						'is_const' => $notification['is_const'], 
						'type' => $notification['type']]
						);

				}
			}
			return $result;
		} catch(Exception $e) {
			error_log($e->getMessage());
			return false;
		}
		
		return $result;
	}

	/**
	 * @author KCG
	 * @since July 6, 2017
	 */
  	public static function options($category) {
  		if ($category == 'priority') {
            return [
                'Urgent' => self::PRIORITY_URGENT, 
                'Normal' => self::PRIORITY_NORMAL, 
                'Low'    => self::PRIORITY_LOW
            ];
        } else if ($category == 'status') {
			return [
				self::STATUS_ENABLE => 'Enabled',
				self::STATUS_DISABLE => 'Disabled',
			];
		}

  		return [];
  	}

    /**
     * @author KCG
     * @since July 6, 2017
     * icons by each priority.
     */
    public static function iconsByPriority() {
        return [
            self::PRIORITY_URGENT => 'fa-bolt', // success
            self::PRIORITY_NORMAL => 'fa-bolt', // info
            self::PRIORITY_LOW => 'fa-bullhorn'
        ];
    }

    public static function iconByPriority($pritority) {
        return self::iconsByPriority()[$pritority];
    }

	public static function enableStatusChanged($email_template) {
		$attributes = '';

		if ($email_template->status == self::STATUS_DISABLE) {
			$attributes .= ' data-status-' . self::STATUS_ENABLE . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETE . '=true';
		} elseif ($email_template->status == self::STATUS_ENABLE) {
			$attributes .= ' data-status-' . self::STATUS_DISABLE . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETE . '=true';
		} else {
			$attributes .= ' data-status-' . self::STATUS_ENABLE . '=true';
		}

		return $attributes;
	}

}