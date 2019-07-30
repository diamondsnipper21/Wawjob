<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use DB;
use Auth;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectInvitation;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;
use iJobDesk\Models\User;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Settings;

class ProjectApplication extends Model {

    use SoftDeletes;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'project_applications';

    protected $dates = ['deleted_at'];

    const DUR_MT6M = 'MT6M';
    const DUR_3T6M = '3T6M';
    const DUR_1T3M = '1T3M';
    const DUR_LT1M = 'LT1M';
    const DUR_LT1W = 'LT1W';
    const DUR_NS   = 'NS';
    public static $str_application_duration;

    const PROVENANCE_NORMAL = 0;
    const PROVENANCE_INVITED = 2;
    const PROVENANCE_OFFER = 3;

    const STATUS_NORMAL = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PROJECT_CANCELLED = 5;
    const STATUS_PROJECT_EXPIRED = 6;
    const STATUS_HIRED = 7;
    const STATUS_HIRING_CLOSED = 8;
    const STATUS_WITHDRAWN = 10; // NOT USED, BUT DON'T REMOVE

    const IS_DECLINED_NO = 0;
    const IS_FREELANCER_DECLINED = 1;
    const IS_CLIENT_DECLINED = 2;

    const IS_ARCHIVED_NO = 0;
    const IS_ARCHIVED_YES = 1;

    // Decline reason
    const DECLINE_REASON_PREFER_OTHER_STYLE = 0;
    const DECLINE_REASON_TOO_HIGH_PRICE = 1;
    const DECLINE_REASON_NO_DESIRABLE_EXPERIENCE = 2;
    const DECLINE_REASON_OTHER = 3;
    protected static $str_declined_reason;

    // Withdraw reason
    const WITHDRAW_REASON_APPLIED_BY_MISTAKE = 1;
    const WITHDRAW_REASON_NO_RESPONSIVE = 2;
    const WITHDRAW_REASON_SCHEDULE_CONFLICT = 3;
    const WITHDRAW_REASON_NO_DESIRABLE_SKILLS = 4;
    const WITHDRAW_REASON_OTHER = 5;
    protected static $str_withdrawn_reason;

    // N days for connections
    const INTERVAL_CONNECTIONS = 7;

    // Total available connections for INTERVAL_CONNECTIONS days
    const TOTAL_AVAILABLE_CONNECTIONS = 30;

    const JOB_CONNECTIONS = 1;

    // Connections to be used for the featured job
    const FEATURED_JOB_CONNECTIONS = 2;

    const FEATURED_PROPOSAL_TIMES = 2;

    // Max length for the message
    const MESSAGE_MAX_LENGTH = 5000;

    // Max length for the memo
    const MEMO_MAX_LENGTH = 250;

    public function __construct()
    {
        parent::__construct();
        
        self::$str_application_duration = [
            self::DUR_MT6M => trans('common.mt6m'),
            self::DUR_3T6M => trans('common.3t6m'),
            self::DUR_1T3M => trans('common.1t3m'),
            self::DUR_LT1M => trans('common.lt1m'),
            self::DUR_LT1W => trans('common.lt1w'),
            self::DUR_NS   => trans('common.not_sure')
        ];

		self::$str_declined_reason = [
			self::DECLINE_REASON_PREFER_OTHER_STYLE => trans('job.reason_prefer_other_style'),
			self::DECLINE_REASON_TOO_HIGH_PRICE => trans('job.reason_too_high_price'),
			self::DECLINE_REASON_NO_DESIRABLE_EXPERIENCE => trans('job.reason_no_desirable_experience'),
			self::DECLINE_REASON_OTHER => trans('common.other'),
		];

		self::$str_withdrawn_reason = [
			self::WITHDRAW_REASON_APPLIED_BY_MISTAKE => trans('job.applied_by_mistake'),
			self::WITHDRAW_REASON_NO_RESPONSIVE => trans('common.no_responsive'),
			self::WITHDRAW_REASON_SCHEDULE_CONFLICT => trans('job.schedule_conflict'),
			self::WITHDRAW_REASON_NO_DESIRABLE_SKILLS => trans('job.no_desirable_skills'),
			self::WITHDRAW_REASON_OTHER => trans('common.other'),
		];
    }

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('type', File::TYPE_PROJECT_APPLICATION);
    }

    /**
    * Get the user.
    *
    * @return mixed
    */
    public function user() {
        return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'user_id')->withTrashed();
    }

    /**
    * Get the project.
    *
    * @return mixed
    */
    public function project() {
        return $this->hasOne('iJobDesk\Models\Project', 'id', 'project_id');
    }

    /**
    * Get the contract.
    *
    * @return mixed
    */
    public function contract() {
        return $this->hasOne('iJobDesk\Models\Contract', 'application_id', 'id');
    }

    public function invitation() {
        return $this->hasOne('iJobDesk\Models\ProjectInvitation', 'id', 'project_invitation_id');
    }

    public function freelancerRate($is_affiliated = false) {
        $price = $this->price * Settings::getRate($is_affiliated);

        return round2Decimal($price);
    }

    public function feeRate($is_affiliated = false) {
        $price = $this->price - self::freelancerRate($is_affiliated);

        return $price;
    }

    public static function getOptions($cat) {
        if ($cat == 'status') {
            return [
                'Sent'          => self::STATUS_NORMAL,
                'Interview'     => self::STATUS_ACTIVE,
                'Hired'         => self::STATUS_HIRED,
                'Cancelled'     => self::STATUS_PROJECT_CANCELLED,
                'Withdrawn'     => self::STATUS_WITHDRAWN,
                'Expired'       => self::STATUS_PROJECT_EXPIRED . ',' . self::STATUS_HIRING_CLOSED,
                'Hiring Closed' => self::STATUS_HIRING_CLOSED,
                'Project Expired'=> self::STATUS_PROJECT_EXPIRED
            ];
        }

        return [];
    }

    /**
    * Check if proposal is using connections or not
    * @return boolean
    */
    public function isUsedConnections() {
        if ( in_array($this->status, [
            self::STATUS_NORMAL,
            self::STATUS_ACTIVE,
            self::STATUS_HIRED,
            self::STATUS_HIRING_CLOSED
        ]) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Calculate the connections used
    * @return boolean
    */
    public function connections() {
    	$connections = 0;

    	if ( $this->project->is_featured == Project::STATUS_FEATURED ) {
    		$connections = Settings::get('CONNECTIONS_FEATURED_PROJECT');
        } else {
        	$connections = self::JOB_CONNECTIONS;
        }

		if ( $this->is_featured ) {
			$connections = $connections * self::FEATURED_PROPOSAL_TIMES;
    	}

        return $connections;
    }

    /**
    * Get the message thread created by this proposal
    * @author Ro Un Nam
    */
    public function messageThread() {
    	return $this->hasOne('iJobDesk\Models\ProjectMessageThread', 'application_id', 'id')
    				->orderBy('created_at', 'desc');
    }

    /**
    * Get the Open Application using Project and User.
    * Open : Normal, Active, Invited, Offer, Hired
    * @return mixed
    */
    public static function getOpenApplication($project_id, $user_id)
    {
        try {
            $app = self::whereRaw('project_id=? AND user_id=? AND 
                (status=? OR status=? OR status=? OR status=? OR status=?)', 
                [
                    $project_id, $user_id,
                    self::STATUS_NORMAL, 
                    self::STATUS_ACTIVE, 
                    self::STATUS_HIRED
                ])
            ->orderBy('updated_at', 'DESC')
            ->first();

            return $app;
        }
        catch(Exception $e) {

        }
        return false;
    }

    /**
    * Get MessageThread from Application
    * 
    * @return mixed
    */
    public function getMessageThread() {
        $thread = ProjectMessageThread::where('application_id', $this->id)->first();

        if ( !$thread ) {
            // New Message Thread
            $thread = new ProjectMessageThread();
            $thread->subject        = $this->project->subject;
            $thread->sender_id      = $this->project->client_id;
            $thread->receiver_id    = $this->user_id;
            $thread->application_id = $this->id;

            $thread->save();

            $thread->unique_id = generate_unique_id($thread->id);
            $thread->save();
        }
        
        return $thread;
    }

    /**
    * Get Messages between Buyer and Contractor (Application)
    * 
    * @return mixed
    */
    public function getMessages($grouped=false) {
        $thread = $this->getMessageThread();
        if ($thread) {
            $messages = $thread->messages;
            if ($grouped) {
                $groupMessages = $messages->groupBy( function ($item, $key) {
                    return substr($item->created_at, 0, 10);
                });
                return $groupMessages;
            }
            return $messages;
        }
        return array();
    }

    /**
    * Send Message through application
    * from : User ID
    * @return mixed
    */
    public function sendMessage($msg, $from, $notified = true) {
        if ($msg == '')
            return false;

        $thread = $this->getMessageThread();
        $thread->recent_message_created_at = date('Y-m-d H:i:s');
        $thread->save();

    	//New Message
        $newMessage = new ProjectMessage();

        $newMessage->thread_id  = $thread->id;
        $newMessage->sender_id  = $from;
        $newMessage->message    = mb_substr($msg, 0, 5000);

        $newMessage->save();

    	//Notification
        $to = '';
        if ($thread->sender_id == $from) {
            $to = $thread->receiver_id;
        } else {
            $to = $thread->sender_id;
        }

        $from_user = User::find($from);
        $to_user = User::find($to);
        if ( $from_user && $notified ) {
            // Notification::send(
            //     Notification::SEND_MESSAGE, 
            //     SUPERADMIN_ID, 
            //     $to,
            //     [
            //     	'sender_name' => $from_user->fullname()
            //     ]
            // );

			EmailTemplate::send($to_user, 'SEND_MESSAGE', 0, [
				'USER'          => $to_user->fullname(),
                'SENDER_NAME'   => $from_user->fullname(),
                'JOB_TITLE'     => $this->project->subject,
                'MESSAGE'       => strip_tags(nl2br($msg), '<br>'),
                'SHORT_MSG'     => substr(strip_tags($msg), 0, 50),
                'MESSAGE_URL'   => _route('message.list', ['id' => $thread->id], true, null, $to_user),
			]);
        }

    	// This is an only case where buyer send message to freelancer
        if ( $this->status == self::STATUS_NORMAL ) {
            $this->status = self::STATUS_ACTIVE;
            $this->is_checked = 1;
            $this->save();
        }

        return $newMessage->id;
    }

    /**
    * Get the available connections
    * 
    * @author Ro Un Nam
    * @return mixed
    */
    public static function getAvailableConnections($user_Id) {
        
        $usedConnections = 0;

        // Connections used for interval days
        $proposals = ProjectApplication::
        whereRaw('user_id = ? AND (TO_DAYS(NOW()) - TO_DAYS(created_at)) < ? AND (status = ? OR status = ?)', 
            [$user_Id,
            ProjectApplication::INTERVAL_CONNECTIONS, 
            ProjectApplication::STATUS_NORMAL, 
            ProjectApplication::STATUS_ACTIVE])->get();

        if ( count($proposals) ) {
        	foreach ( $proposals as $p ) {
        		$connections = 0;

		    	if ( $p->project->is_featured == Project::STATUS_FEATURED ) {
		    		$connections = Settings::get('CONNECTIONS_FEATURED_PROJECT');
		        } else {
		        	$connections = self::JOB_CONNECTIONS;
		        }

				if ( $p->is_featured ) {
					$connections = $connections * self::FEATURED_PROPOSAL_TIMES;
		    	}

		    	$usedConnections += $connections;
		    }
        }

        $totalConnections = ProjectApplication::TOTAL_AVAILABLE_CONNECTIONS - $usedConnections;

        if ( $totalConnections < 0 ) {
        	$totalConnections = 0;
        }

        return $totalConnections;
    }

    /**
    * Less than a week | Less than a month | 1 to 3 months | 3 to 6 months | More than 6 months
    *
    * @return mixed
    */
    public function duration_string() {
        if ($this->duration == null)
            $this->duration = self::DUR_NS;
        
        if (isset(self::$str_application_duration[$this->duration])) {
            return self::$str_application_duration[$this->duration];
        }

        return '';
    }

	public function declined_reason_string() {
		if ( isset(self::$str_declined_reason[$this->decline_reason]) ) {
			return self::$str_declined_reason[$this->decline_reason];
		}

		return '';
	}

    public function withdrawn_reason_string() {
        if ( isset(self::$str_withdrawn_reason[$this->decline_reason]) ) {
            return self::$str_withdrawn_reason[$this->decline_reason];
        }

        return '';
    }

    public function isInvited() {
		return $this->provenance == self::PROVENANCE_INVITED; 
	}

    public function isOffer() {
        return $this->provenance == self::PROVENANCE_OFFER; 
    }

    public function isNormal() {
        return $this->status == self::STATUS_NORMAL; 
    }

    public function isActive() {
		return $this->status == self::STATUS_ACTIVE; 
	}

	public function isClosed() {
		return $this->status == self::STATUS_HIRING_CLOSED; 
	}

    public function isCancelled() {
        return $this->status == self::STATUS_PROJECT_CANCELLED;
    }

    public function isExpired() {
        return $this->status == self::STATUS_PROJECT_EXPIRED;
    }

    public function isHired() {
        return $this->status == self::STATUS_HIRED;
    }

    public function isArchived() {
        return $this->is_archived == self::IS_ARCHIVED_YES;
    }

    public function isDeclined() {
        if ( $this->is_declined == self::IS_FREELANCER_DECLINED || $this->is_declined == self::IS_CLIENT_DECLINED ) {
            return true;
        }

        return false;
    }

	public function isDeclinedByFreelancer() {
		return $this->is_declined == self::IS_FREELANCER_DECLINED;
	}

	public function isDeclinedByBuyer() {
		return $this->is_declined == self::IS_CLIENT_DECLINED;
	}

    public static function openedApplications($user) {
        return self::whereIn('status', [
                       self::STATUS_NORMAL,
                       self::STATUS_HIRED,
                       self::STATUS_ACTIVE
                   ])
                   ->where('user_id', $user->id)
                   ->get();
    }
}