<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

use Auth;
use Log;

use iJobDesk\Models\ContractMeter;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\HourlyReview;
use iJobDesk\Models\Notification;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\Settings;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\User;

class Contract extends Model {

    use SoftDeletes;

    const MILESTONE_CHANGED_NO = 0;
    const MILESTONE_CHANGED_YES = 1;
    const MILESTONE_CHANGED_ACCEPTED = 2;
    const MILESTONE_CHANGED_DECLINED = 3;

    const WITHDRAW_MESSAGE_MAX_LENGTH = 500;
    const REQUEST_PAYMENT_MESSAGE_MAX_LENGTH = 500;
    const DAYS_WAITING_FEEDBACK = 14;
    const HOURS_REFRESH_METER = 2;

    const STATUS_OFFER    = 0;
    const STATUS_OPEN     = 1;
    const STATUS_PAUSED   = 2;
    const STATUS_SUSPENDED = 3;
    const STATUS_REJECTED = 6; // Rejected by freelancer
    const STATUS_WITHDRAWN = 7; // Withdrawn by client
    const STATUS_CANCELLED = 8;
    const STATUS_CLOSED   = 9;
    const STATUS_DELETED = 10;

    const PAUSED_BY_CLIENT = 0;
    const PAUSED_BY_IJOBDESK = 1;

    const CLOSED_BY_CLIENT = 0;
    const CLOSED_BY_FREELANCER = 1;
    const CLOSED_BY_IJOBDESK = 2;

    const TYPE_FIXED = 0;
    const TYPE_HOURLY = 1;

    public static $str_contract_type;

    public static $str_contract_status;

    const WITHDRAW_REASON_MISTAKE = 1;
    const WITHDRAW_REASON_HIRED_ANOTHER_FREELANCER = 2;
    const WITHDRAW_REASON_IRRESPONSIVE_FREELANCER = 3;
    const WITHDRAW_REASON_OTHER = 4;

    const CLOSED_REASON_SUCCESS = 0;
    const CLOSED_REASON_NO_RESPONSIVE = 1;
    const CLOSED_REASON_CANCELED = 2;
    const CLOSED_REASON_NO_REQUIRED_SKILLS = 3;
    const CLOSED_REASON_REQUIREMENT_CHANGED = 4;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'contracts';

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'started_at', 'ended_at', 'updated_at', 'deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    function __construct() {
        parent::__construct();
        
        self::$str_contract_type = array(
            self::TYPE_HOURLY => trans('common.hourly'), 
            self::TYPE_FIXED  => trans('common.fixed_price')
        );

        self::$str_contract_status = [
	        self::STATUS_OFFER      => trans('common.job_offer_sent'),
	        self::STATUS_OPEN       => trans('common.working'),
	        self::STATUS_PAUSED     => trans('common.paused'),
	        self::STATUS_SUSPENDED  => trans('common.suspended'),
	        self::STATUS_REJECTED   => trans('common.declined_by_freelancer'),
	        self::STATUS_WITHDRAWN  => trans('common.withdrawn_by_you'),
	        self::STATUS_CANCELLED  => trans('common.cancelled'),
	        self::STATUS_CLOSED     => trans('common.closed'),
        ];
    }

    /**
    * Check if User is author(client) of this contract
    * 
    * @return mixed
    */
    public function checkIsAuthor($user_id) {
        return ($this->buyer_id == $user_id);
    }

    /**
    * Check if current freelancer is a contractor
    */
    public function checkCurrentFreelancer($user_id) {
    	return $this->contractor_id == $user_id;
    }

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('type', File::TYPE_CONTRACT);
    }

    /**
    * Get the milestones.
    */
    public function milestones() {
        return $this->hasMany('iJobDesk\Models\ContractMilestone', 'contract_id', 'id');
    }

    /**
    * Get the total milestones amount.
    */
    public function totalMilestonesAmount() {
        if ( $this->milestone_changed == self::MILESTONE_CHANGED_YES ) {
            $total = $this->hasMany('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
                            ->where('changed_status', '<>', ContractMilestone::CHANGED_STATUS_DELETED)
                            ->where('changed_status', '<>', ContractMilestone::CHANGED_STATUS_NO)
                            ->sum('new_price')
                             + 
                    $this->hasMany('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
                            ->where('changed_status', '<>', ContractMilestone::CHANGED_STATUS_DELETED)
                            ->where('changed_status', ContractMilestone::CHANGED_STATUS_NO)
                            ->where('fund_status', '<>', ContractMilestone::FUND_REFUNDED)
                            ->sum('price');
        } else {
            $total = $this->hasMany('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
                            ->where('fund_status', '<>', ContractMilestone::FUND_REFUNDED)
                            ->sum('price');
        }

        return $total;
    }

    public function totalMilestonesRefundedAmount() {
        $total = $this->hasMany('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
                        ->where('fund_status', ContractMilestone::FUND_REFUNDED)
                        ->sum('price');
        return $total;
    }

    /**
    * Get the hourlyLog
    */
    public function hourlyLogs() {
        return $this->hasMany('iJobDesk\Models\HourlyLog', 'contract_id', 'id');
    }

    /**
    * Get the hourlyLogMap
    */
    public function hourlyLogMaps() {
        return $this->hasMany('iJobDesk\Models\HourlyLogMap', 'contract_id', 'id');
    }

    /**
    * Get the total milestones
    * @author Ro Un Nam
    */
    public function countMilestones() {
        return $this->milestones()->count();
    }

	/**
	* Get the total funds
	*/
	public function fundedMilestones() {
    	return $this->hasMany('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
					->where('fund_status', ContractMilestone::FUNDED)
					->where('transaction_id', '<>', 0)
					->get();
	}

	/**
	* Get the current funded for the fixed contract
	* @author Ro Un Nam
	*/
	public function fundedLastMilestone() {
		return $this->hasOne('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
					->where('fund_status', ContractMilestone::FUNDED)
					->where('transaction_id', '<>', 0)
					->orderBy('end_time', 'desc');
	}

	/**
	* Get the requested milestone funded for the fixed contract
	* @author Ro Un Nam
	*/
	public function requestedFundedMilestone() {
		return $this->hasOne('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
                    ->where('fund_status', ContractMilestone::FUNDED)
					->where('payment_requested', 1)
					->where('transaction_id', '<>', 0)
					->orderBy('end_time', 'desc');
	}

	public function totalFunded() {
    	$total = $this->hasMany('iJobDesk\Models\ContractMilestone', 'contract_id', 'id')
					->where('fund_status', ContractMilestone::FUNDED)
					->where('transaction_id', '<>', 0)
					->sum('price');

        return $total;
    }

    public function totalRefunded() {
    	$total = TransactionLocal::where('contract_id', $this->id)
    							->where('user_id', $this->buyer_id)
								->where('for', TransactionLocal::FOR_BUYER)
								->where('type', TransactionLocal::TYPE_REFUND)
								->sum('amount');

        return $total;
    }

    public function totalBonus() {
    	$total = TransactionLocal::where('contract_id', $this->id)
    							->where('user_id', $this->buyer_id)
								->where('for', TransactionLocal::FOR_BUYER)
								->where('type', TransactionLocal::TYPE_BONUS)
								->sum('amount');

        return abs($total);
    }

    /**
    * Total paid except for only funded
    */
    public function grossTotal() {
        $total = TransactionLocal::leftJoin('contract_milestones', 'transactions.milestone_id', '=', 'contract_milestones.id')
                                ->where('transactions.contract_id', $this->id)
                                ->where('transactions.user_id', $this->buyer_id)
                                ->where('transactions.for', TransactionLocal::FOR_BUYER)
                                ->whereNotIn('transactions.type', [
                                    TransactionLocal::TYPE_FEATURED_JOB,
                                    TransactionLocal::TYPE_AFFILIATE,
                                    TransactionLocal::TYPE_AFFILIATE_CHILD
                                ])
                                ->where(function($query) {
                                    $query->whereNull('contract_milestones.fund_status')
                                            ->orWhere('contract_milestones.fund_status', '<>', ContractMilestone::FUNDED);
                                })
                                ->sum('transactions.amount');

        return abs($total);
    }

    /**
    * Get the total paid
    * @author Ro Un Nam
    */
    public function totalPaid() {

        $paid = TransactionLocal::where('contract_id', $this->id)
        						->where('user_id', $this->buyer_id)
                                ->where('for', TransactionLocal::FOR_BUYER)
                                ->whereNotIn('type', [
                                    TransactionLocal::TYPE_FEATURED_JOB,
                              		TransactionLocal::TYPE_AFFILIATE,
                              		TransactionLocal::TYPE_AFFILIATE_CHILD
                              	])
                                ->sum('amount');
        
        return $paid;
    }

    /**
    * Get the total paid
    * @author Ro Un Nam
    */
    public function totalPaidForFreelancer() {

        $paid = TransactionLocal::where('contract_id', $this->id)
			                    ->where('user_id', $this->contractor_id)
			                    ->where('for', TransactionLocal::FOR_FREELANCER)
                                ->whereNotIn('type', [
                                    TransactionLocal::TYPE_FEATURED_JOB,
                                    TransactionLocal::TYPE_AFFILIATE,
                                    TransactionLocal::TYPE_AFFILIATE_CHILD
                                ])
			                    ->sum('amount');
        
        return $paid;
    }

    /**
    * Get the total paid
    * @author Ro Un Nam
    */
    public function totalPaidForFreelancerIncludeFee() {

        $paid = TransactionLocal::where('contract_id', $this->id)
        						->where('status', TransactionLocal::STATUS_DONE)
        						->where(function($query) {
        							$query->where(function($query2) {
        								$query2->where('user_id', $this->contractor_id)
			                    				->where('for', TransactionLocal::FOR_FREELANCER);
        							})->orWhere('for', TransactionLocal::FOR_IJOBDESK);
        						})
                                ->whereNotIn('type', [
                                    TransactionLocal::TYPE_FEATURED_JOB,
                                    TransactionLocal::TYPE_AFFILIATE,
                                    TransactionLocal::TYPE_AFFILIATE_CHILD,
                                ])
			                    ->sum('amount');
        
        return $paid;
    }

    /**
    * Get the total paid
    * @author Ro Un Nam
    */
    public function totalPaidPending() {

        $paid = TransactionLocal::where('contract_id', $this->id)
        						->where('status', TransactionLocal::STATUS_AVAILABLE)
        						->where(function($query) {
        							$query->where(function($query2) {
        								$query2->where('user_id', $this->contractor_id)
			                    				->where('for', TransactionLocal::FOR_FREELANCER);
        							})->orWhere('for', TransactionLocal::FOR_IJOBDESK);
        						})
                                ->whereNotIn('type', [
                                    TransactionLocal::TYPE_FEATURED_JOB,
                                    TransactionLocal::TYPE_AFFILIATE,
                                    TransactionLocal::TYPE_AFFILIATE_CHILD,
                                ])
			                    ->sum('amount');
        
        return $paid;
    }

    /**
    * Get the project.
    */
    public function project()
    {
        return $this->hasOne('iJobDesk\Models\Project', 'id', 'project_id');
    }

    /**
    * Get the application.
    *
    * @return mixed
    */
    public function application()
    {
        return $this->hasOne('iJobDesk\Models\ProjectApplication', 'id', 'application_id');
    }

    /**
    * Get the buyer.
    */
    public function buyer()
    {
        return $this->hasOne('iJobDesk\Models\User', 'id', 'buyer_id')->withTrashed();
    }

    /**
    * Get the contractor.
    */
    public function contractor()
    {
        return $this->hasOne('iJobDesk\Models\User', 'id', 'contractor_id')->withTrashed();
    }

    /**
    * Get the feedback.
    */
    public function feedback()
    {
        return $this->hasOne('iJobDesk\Models\ContractFeedback', 'contract_id', 'id');
    }

    /**
    * Get the meter
    */
    public function meter() {
        return $this->hasOne('iJobDesk\Models\ContractMeter', 'contract_id', 'id');
    }

    public function term_string() {
        if ( $this->isHourly() ) {
            if ( $this->isNoLimit() ) {
                $str = trans('common.no_limit') . ' <span>$' . formatCurrency($this->price) . '/' . trans('common.hour') . '</span>';
            } else {
                $str = trans('common.n_hours_week', ['n' => $this->limit]) . ' <span>$' . formatCurrency($this->price) . '/' . trans('common.hour') . '</span>';
            }
        } else {
            $str = trans('common.fixed_price') . ' <span>$' . formatCurrency($this->price) . '</span>';
        }

        return $str;
    }

    /**
    * @return mixed
    */
    public function status_string() {
        if ( isset(self::$str_contract_status[$this->status]) ) {
            return self::$str_contract_status[$this->status];
        }

        return '';
    }

    public static function onlyContractStatus() {
        return [
            self::STATUS_OPEN,
            self::STATUS_PAUSED,
            self::STATUS_SUSPENDED,
            self::STATUS_CANCELLED,
            self::STATUS_CLOSED
        ];
    }

    /**
    * Get the hourly time logs.
    */
    public function timelogs()
    {
        return $this->hasMany('iJobDesk\Models\HourlyLog');
    }

    public function isHourly() {
        return $this->type == self::TYPE_HOURLY;
    }

    public function isFixed() {
        return $this->type == self::TYPE_FIXED;
    }

    public function isOpen() {
        return $this->status == self::STATUS_OPEN; 
    }

    public function isOffer() {
        return $this->status == self::STATUS_OFFER; 
    }

    public function isRejected() {
        return $this->status == self::STATUS_REJECTED; 
    }

    public function isWithdrawn() {
        return $this->status == self::STATUS_WITHDRAWN; 
    }

    public function isCancelled() {
        return $this->status == self::STATUS_CANCELLED; 
    }

    public function isClosed() {
        return $this->status == self::STATUS_CLOSED; 
    }

    public function isClosedByBuyer() {
        return $this->closed_by == self::CLOSED_BY_CLIENT; 
    }

    public function isClosedByFreelancer() {
        return $this->closed_by == self::CLOSED_BY_FREELANCER; 
    }

    public function isSuspended() {
        return $this->status == self::STATUS_SUSPENDED || ($this->buyer->isSuspended() || $this->contractor->isSuspended());
    }
    
    public function isPaused() {
        return $this->status == self::STATUS_PAUSED; 
    }

    public function isPausedByiJobDesk() {
        return $this->paused_by == self::PAUSED_BY_IJOBDESK; 
    }

    public function isAffiliated() {
        return $this->is_affiliated == 1; 
    }

    public function isAllowedManualTime() {
        return $this->is_allowed_manual_time == 1; 
    }

    public function isAllowedOverTime() {
        return $this->is_allowed_over_time == 1; 
    }

    public function isNoLimit() {
        return $this->limit < 0; 
    }

    public function weekly_limit_string() {
        return ($this->isNoLimit() ? trans('common.no_limit') : $this->limit . ' ' . trans('common.hours') . ' / ' . trans('common.week'));
    }

    public function isChangedLimit() {
        return $this->new_limit != '0';
    }

    public function weekly_new_limit_string() {
        if ( !$this->isChangedLimit() ) {
            return '';
        }

        return ($this->new_limit < 0 ? trans('common.no_limit') : $this->new_limit . ' ' . trans('common.hours') . ' / ' . trans('common.week'));
    }

    public function manual_time_string() {
        return ($this->isAllowedManualTime() ? trans('common.yes') : trans('common.no'));
    }

    public function over_time_string() {
        return ($this->isAllowedOverTime() ? trans('common.yes') : trans('common.no'));
    }

    /**
    * Hourly | Fixed
    *
    * @return mixed
    */
    public function type_string() {
        if ( isset(self::$str_contract_type[$this->type]) ) {
            return self::$str_contract_type[$this->type];
        }

        return '';
    }

    public function getRate() {
        return Settings::getRate($this->isAffiliated());
    }

    public function buyerPrice($mins) {
        $price = $this->price * $mins / 60;

        return round2Decimal($price);
    }

    public function freelancerPrice($mins) {
        $price = ($this->price * $this->getRate()) * 100 * $mins / 6000;

        return round2Decimal($price);
    }

    public function freelancerRate() {
        $price = $this->price * $this->getRate();

        return round2Decimal($price);
    }

    public function feeRate() {
        $price = $this->price - self::freelancerRate();

        return $price;
    }

    /**
    * Check if user can leave the feedback for the contract.
    */
    public function canLeaveFeedback($user = null) {
        if ( !$this->isClosed() ) {
            return false;
        }

        // if ($this->buyer->trashed() || $this->contractor->trashed())
        //     return false;

        if (!$user)
            $user = Auth::user();

        $feedback = $this->feedback;
        $last_dispute = $this->getLastSolvedDispute();
        if ($last_dispute) {
            if ($last_dispute->archive_type == Ticket::RESULT_PUNISH_FREELANCER) {
                if ($user->isFreelancer())
                    return false;

                if ($user->isBuyer() && $feedback && $feedback->buyer_feedback)
                    return false;
            }

            if ($last_dispute->archive_type == Ticket::RESULT_PUNISH_BUYER) {
                if ($user->isBuyer())
                    return false;

                if ($user->isFreelancer() && $feedback && $feedback->freelancer_feedback)
                    return false;
            }
        } else {
            if ($user->isBuyer() && $feedback && $feedback->buyer_feedback)
                return false;

            if ($user->isFreelancer() && $feedback && $feedback->freelancer_feedback)
                return false;
        }

        if ( $this->meter && $this->meter->last_amount + $this->meter->this_amount + $this->meter->total_amount == 0 )
            return false;

        $date = date_diff(date_create(), date_create($this->ended_at));

        $days = $date->y * 365 + $date->m * 30 + $date->d;

        if ( $days > self::DAYS_WAITING_FEEDBACK )
            return false;

        return true;
    }

    /**
    * Check if shows the alert about ended contract and leave the feedback
    * @author Ro Un Nam
    * @since Jun 04, 2017
    */
    public function checkLeaveFeedbackAlert($user) {
        return $this->canLeaveFeedback($user);
    }

    public function accept_milestones() {
        
        $milestones = $this->milestones;

        if ( $this->milestone_changed == self::MILESTONE_CHANGED_YES ) {
        	$total_price = 0;
            foreach ( $milestones as $milestone ) {
                if ( intval($milestone->changed_status) == ContractMilestone::CHANGED_STATUS_NO ) {
                	$total_price += $milestone->price;

                    continue;
                }

                if ( $milestone->changed_status == ContractMilestone::CHANGED_STATUS_DELETED ) {
                    $milestone->delete();
                } else {
                    $milestone->name = $milestone->new_name;
                    $milestone->start_time = $milestone->new_start_time;
                    $milestone->end_time = $milestone->new_end_time;
                    $milestone->price = $milestone->new_price;
                    $milestone->changed_status = ContractMilestone::CHANGED_STATUS_NO;
                    $milestone->save();

                    $total_price += $milestone->price;
                }
            }
            
            $this->price = $total_price;
            $this->milestone_changed = self::MILESTONE_CHANGED_ACCEPTED;
            $this->save();
        }

        return true;
    }

    public function decline_milestones() {
        if ( $this->milestone_changed == self::MILESTONE_CHANGED_YES ) {
            $this->milestone_changed = self::MILESTONE_CHANGED_DECLINED;
            $this->save();
        }
        
        return true;
    }
    
    public function start() {
        if ( !$this->accept_milestones() ) {
            return false;
        }

        $this->status = self::STATUS_OPEN;
        $this->started_at = date('Y-m-d H:i:s');
        $this->save();

		// Close project
		if ( $this->project->contract_limit == Project::CONTRACT_LIMIT_ONE ) {
			$this->project->cancelled_at = date('Y-m-d H:i:s');
			$this->project->status = Project::STATUS_CLOSED;
			$this->project->save();
		}

		// Close proposal
        if ( $this->application ) {
            $this->application->status = ProjectApplication::STATUS_HIRED;
            $this->application->save();
        }

        // ContractMeter
        $contractMeter = new ContractMeter;
        $contractMeter->contract_id = $this->id;
        $contractMeter->save();

        Notification::send(Notification::CONTRACT_STARTED, 
            SUPERADMIN_ID,
            $this->contractor_id, 
            ['contract_title' => sprintf('%s', $this->title)]
        );

        Notification::send(Notification::CONTRACT_STARTED, 
            SUPERADMIN_ID,
            $this->buyer_id, 
            ['contract_title' => sprintf('%s', $this->title)]
        );

        $contract_title = $this->title;
        $contract_url = _route('contract.contract_view', ['id' => $this->id], true, null, $this->buyer);
        $contract_start_date = format_date('M d, Y', $this->started_at);

		// Send email to buyer
        EmailTemplate::send($this->buyer, 'CONTRACT_STARTED', 0, [
            'USER' => $this->buyer->fullname(),
            'CONTRACT_TITLE' => $contract_title,
            'CONTRACT_URL' => $contract_url,
            'CONTRACT_START_DATE' => $contract_start_date,
        ]);
    
		// Send email to freelancer
        EmailTemplate::send($this->contractor, 'CONTRACT_STARTED', 0, [
            'USER' => $this->contractor->fullname(),
            'CONTRACT_TITLE' => $contract_title,
            'CONTRACT_URL' => $contract_url,
            'CONTRACT_START_DATE' => $contract_start_date,
        ]);

        return true;
    } 

    public function reject(Request $request) {
        $this->status = self::STATUS_REJECTED;
        $this->closed_reason = $request->input('message');
        $this->ended_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public function refund_milestones() {
        if ( $this->milestones ) {
            foreach ( $this->milestones as $m ) {
                if ( $m->isFunded() ) {
                    TransactionLocal::refund_fund($this->id, $m->id);
                }
            }
        }
    }

	public function suspend($reasons, $user = null, $saved = true) {
        if (!$user)
            $user = Auth::user();

        if ($saved)
		  $this->status = self::STATUS_SUSPENDED;

        $reason = $user->isBuyer()?$reasons['buyer']:$reasons['freelancer'];

		if ( !$saved || ($saved && $this->save()) ) {
			Notification::send(
				Notification::CONTRACT_SUSPENDED, 
				SUPERADMIN_ID,
				$this->buyer_id, 
				['contract_title' => sprintf('%s', $this->title)]
			);

			Notification::send(
				Notification::CONTRACT_SUSPENDED, 
				SUPERADMIN_ID,
				$this->contractor_id, 
				['contract_title' => sprintf('%s', $this->title)]
			);

            $contract_title = $this->title;
            $contract_url = _route('contract.contract_view', ['id' => $this->id], true, null, $this->buyer);

			EmailTemplate::send($this->buyer, 'CONTRACT_SUSPENDED', 0, [
                'USER'           => $this->buyer->fullname(),
				'CONTRACT_TITLE' => $contract_title,
				'CONTRACT_URL'   => $contract_url,
                'REASON'         => $this->buyer_id == $user->id?$reasons['me']:$reason
			]);

			EmailTemplate::send($this->contractor, 'CONTRACT_SUSPENDED', 0, [
                'USER'           => $this->contractor->fullname(),
				'CONTRACT_TITLE' => $contract_title,
				'CONTRACT_URL'   => $contract_url,
                'REASON'         => $this->contractor_id == $user->id?$reasons['me']:$reason
			]);
		}
	}

	public function pause($paused_by = 0) {
		$this->status = self::STATUS_PAUSED;
		$this->paused_by = $paused_by;
		
		if ( $this->save() ) {
			Notification::send(
				Notification::CONTRACT_PAUSED, 
				SUPERADMIN_ID,
				$this->buyer_id, 
				['contract_title' => sprintf('%s', $this->title)]
			);

			Notification::send(
				Notification::CONTRACT_PAUSED, 
				SUPERADMIN_ID,
				$this->contractor_id, 
				['contract_title' => sprintf('%s', $this->title)]
			);

            $freelancer_name = $this->contractor->fullname();
            $buyer_name = $this->buyer->fullname();
            $contract_title = $this->title;
            $contract_url = _route('contract.contract_view', ['id' => $this->id], true, null, $this->buyer);

			EmailTemplate::send($this->buyer, 'CONTRACT_PAUSED', 0, [
				'USER' => $this->buyer->fullname(),
                'BUYER_NAME' => $buyer_name,
				'CONTRACT_TITLE' => $contract_title,
				'CONTRACT_URL' => $contract_url,
			]);

			EmailTemplate::send($this->contractor, 'CONTRACT_PAUSED', 0, [
				'USER' => $freelancer_name,
                'BUYER_NAME' => $buyer_name,
				'CONTRACT_TITLE' => $contract_title,
				'CONTRACT_URL' => $contract_url,
			]);

			return true;
		}

		return false;
	}

	public function cancel() {
		$this->status = self::STATUS_CLOSED;
		$this->ended_at = date('Y-m-d H:i:s');

		if ( $this->save() ) {
            Notification::send(
                Notification::CONTRACT_CANCELLED, 
                SUPERADMIN_ID,
                $this->buyer_id, 
                ['contract_title' => sprintf('%s', $this->title)]
            );

			Notification::send(
				Notification::CONTRACT_CANCELLED, 
				SUPERADMIN_ID,
				$this->contractor_id, 
				['contract_title' => sprintf('%s', $this->title)]
			);

			$contractd_url = _route('contract.contract_view', ['id' => $this->id], true, null, $this->buyer);
			$contract_ended_date = format_date('M d, Y', $this->ended_at);

			EmailTemplate::send($this->buyer, 'CONTRACT_ENDED_WITHOUT_PAYMENT', 0, [
				'USER' => $this->buyer->fullname(),
				'CONTRACT_TITLE' => $this->title,
				'CONTRACT_URL'  => $contractd_url,
				'CONTRACT_END_DATE' => $contract_ended_date,
			]);

			EmailTemplate::send($this->contractor, 'CONTRACT_ENDED_WITHOUT_PAYMENT', 0, [
				'USER' => $this->contractor->fullname(),
				'CONTRACT_TITLE' => $this->title,
				'CONTRACT_URL'  => $contractd_url,
				'CONTRACT_END_DATE' => $contract_ended_date,
			]);
		}
	}

	public function restart() {
		$this->status = Contract::STATUS_OPEN;
        $this->paused_by = 0;
        
		if ( $this->save() ) {
			Notification::send(
				Notification::CONTRACT_RESTARTED, 
				SUPERADMIN_ID,
				$this->buyer_id, 
				['contract_title' => sprintf('%s', $this->title)]
			);

			Notification::send(
				Notification::CONTRACT_RESTARTED, 
				SUPERADMIN_ID,
				$this->contractor_id, 
				['contract_title' => sprintf('%s', $this->title)]
			);
			
            $contract_title = $this->title;
            $contract_url = _route('contract.contract_view', ['id' => $this->id], true, null, $this->buyer);

			EmailTemplate::send($this->buyer, 'CONTRACT_RESTARTED', 0, [
				'USER' => $this->buyer->fullname(),
				'CONTRACT_TITLE' => $contract_title,
				'CONTRACT_URL' => $contract_url,
			]);

			EmailTemplate::send($this->contractor, 'CONTRACT_RESTARTED', 0, [
				'USER' => $this->contractor->fullname(),
				'CONTRACT_TITLE' => $contract_title,
				'CONTRACT_URL' => $contract_url,
			]);

			return true;
		}

		return false;
	}

    public function term_changed() {
		$buyer_name = $this->buyer->fullname();
		$freelancer_name = $this->contractor->fullname();
		$contract_url = _route('contract.contract_view', ['id' => $this->id], true, null, $this->buyer);
		$contract_weekly_limit = $this->isChangedLimit() ? $this->weekly_new_limit_string() : $this->weekly_limit_string();
		$contract_manual_time = $this->manual_time_string();
        $contract_over_time = $this->over_time_string();

		// Send email to frelancer
		EmailTemplate::send($this->contractor, 'CONTRACT_TERM_CHANGED', 0, [
            'USER' => $freelancer_name,
            'CONTRACT_TITLE' => $this->title,
            'CONTRACT_URL' => $contract_url,
            'WEEKLY_LIMIT' => $contract_weekly_limit,
            'MANUAL_TIME_DESC' => $contract_manual_time,
            'OVER_TIME_DESC' => $contract_over_time,
        ]);

		// Send email to buyer
		EmailTemplate::send($this->buyer, 'CONTRACT_TERM_CHANGED', 0, [
            'USER' => $buyer_name,
            'CONTRACT_TITLE' => $this->title,
            'CONTRACT_URL' => $contract_url,
            'WEEKLY_LIMIT' => $contract_weekly_limit,
            'MANUAL_TIME_DESC' => $contract_manual_time,
            'OVER_TIME_DESC' => $contract_over_time,
        ]);
    }

    /**
    * Get Contract Object from Application
    *
    * @author nada
    * @since Apr 12, 2016
    * @version 1.0
    * @return Contract
    */
    public static function getContractFromApplication($application_id) {
        $object = self::where('application_id', '=', $application_id)->first();
        if ($object) {
            return $object;
        }
        return false;
    }

    /**
    * Get the user contracts
    * @param $params [buyer_id, contractor_id, type, from, to, server_timezone_offset, user_timezone_offset]
    */
    public static function getOpenedContracts($params = []) {
        $contracts = Contract::whereRaw(true);

        if ( isset($params['type']) )
            $contracts->where('type', $params['type']);

        if ( isset($params['buyer_id']) ) {
            $contracts->where('buyer_id', $params['buyer_id']);
        } else if ( isset($params['contractor_id']) ) {
            $contracts->where('contractor_id', $params['contractor_id']);
        }

        if ( isset($params['from']) && isset($params['to']) ) {
            $contracts->where(function($query) use ($params) {
                $query->where(function($query2) use ($params) {
                    $query2->whereIn('status', [
                                self::STATUS_OPEN,
                                self::STATUS_PAUSED
                            ])
                            ->whereRaw("CONVERT_TZ(started_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
                })
                ->orWhere(function($query2) use ($params) {
                    $query2->where('status', self::STATUS_CLOSED)
                            ->whereRaw("CONVERT_TZ(ended_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
                            ->whereRaw("CONVERT_TZ(ended_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
                });
            });
        }

        if ( isset($params['status']) ) {
            if ( is_array($params['status']) ) {
                $contracts->whereIn('status', $params['status']);
            } else {
                $contracts->where('status', $params['status']);
            }
        }

        if ( isset($params['orderby']) ) {
            $contracts->orderBy($params['orderby'], 'asc');
        }

        return $contracts->get();
    }

    /**
    * Get the list of contracts
    * @param $params [buyer_id, contractor_id, type, status, closed_reason, orderby, order, count, paginate, earned]
    */
    public static function getContracts($params = []) {
        $order = 'asc';
        if ( isset($params['order']) ) {
            $order = $params['order'];
        }

        $orderby = 'title';
        if ( isset($params['orderby']) ) {
            $orderby = $params['orderby'];
        }

        if ( in_array($orderby, ['earning', 'review', 'newest']) ) {
            $contracts = self::leftJoin('contract_feedbacks', 'contract_feedbacks.contract_id', '=', 'contracts.id')
                            ->leftJoin('contract_meters', 'contract_meters.contract_id', '=', 'contracts.id')
                            ->select([
                                'contracts.*', 
                                'contract_feedbacks.freelancer_score',
                                'contract_meters.total_amount'
                            ]);
        } else {
            $contracts = self::select(['contracts.*']);
        }

        switch ($orderby) {
            case 'earning':
                $contracts = $contracts->orderBy('contract_meters.total_amount', 'desc');
                break;
            case 'review':
                $contracts = $contracts->orderBy('contract_feedbacks.freelancer_score', 'desc');
                break;
            case 'newest':
                $contracts = $contracts->orderBy('contracts.started_at', 'desc');
                break;
            default:
                $contracts = $contracts->orderBy($orderby, $order);
        }

        if ( isset($params['earned']) ) {
            $contracts = $contracts->where('contract_meters.total_amount', '>', 0);
        }

        if ( isset($params['type']) ) {
            $contracts = $contracts->where('type', $params['type']);
        }

        if ( isset($params['buyer_id']) ) {
            $contracts = $contracts->where('buyer_id', $params['buyer_id']);
        } else if ( isset($params['contractor_id']) ) {
            $contracts = $contracts->where('contractor_id', $params['contractor_id']);
        }

        if ( isset($params['status']) ) {
            if ( is_array($params['status']) ) {
                $contracts = $contracts->whereIn('status', $params['status']);
            } else {
                $contracts = $contracts->where('status', $params['status']);
            }
        }

        if ( isset($params['closed_reason']) ) {
            $contracts = $contracts->where('closed_reason', $params['closed_reason']);
        }

        $contracts = $contracts->where('status', '<>', self::STATUS_OFFER);

        if ( isset($params['count']) ) {
            return $contracts->count();
        }

        if ( isset($params['paginate']) ) {
            return $contracts;
        }

        return $contracts->get();
    }

    /**
    * Get all hourly contracts under working
    */
    public static function getContractsUnderWork($user_id = 0) {
    	list($from, $to) = weekRange();

		return self::leftJoin('hourly_log_maps', 'contract_id', '=', 'contracts.id')
					->where('contracts.buyer_id', $user_id)
					->where('contracts.type', self::TYPE_HOURLY)
					->whereIn('contracts.status', [
						self::STATUS_OPEN,
						self::STATUS_CLOSED
					])
					->whereBetween('hourly_log_maps.date', [$from, $to])
					->select(['contracts.*'])
					->distinct()
					->get();
    }

    /**
    * Get all hourly contracts under review
    */
    public static function getContractsUnderReview($user_id = 0) {
		return self::leftJoin('transactions', 'contract_id', '=', 'contracts.id')
					->where('contracts.buyer_id', $user_id)
					->where('contracts.type', self::TYPE_HOURLY)
					->whereIn('contracts.status', [
						self::STATUS_OPEN,
						self::STATUS_CLOSED
					])
					->where('transactions.user_id', $user_id)
					->where('transactions.for', TransactionLocal::FOR_BUYER)
					->where('transactions.status', TransactionLocal::STATUS_PENDING)
					->select(['contracts.*'])
					->distinct()
					->get();
    }

    /**
    * Get the total count of offers
    * 
    * @author Ro Un Nam
    * @return integer
    */
    public static function totalOffersCount($job_id) {
        return Contract::where('project_id', $job_id)
                        ->where('status', self::STATUS_OFFER)
                        ->select('id')
                        ->count();
    }

    /**
    * Get the total count of hires
    * 
    * @author Ro Un Nam
    * @return integer
    */
    public static function totalHiresCount($job_id)
    {
        return Contract::where('project_id', $job_id)
                        ->whereIn('status', [
                            self::STATUS_OPEN,
                            self::STATUS_PAUSED,
                            self::STATUS_SUSPENDED,
                            self::STATUS_CLOSED,
                        ])
                        ->select('id')
                        ->count();
    }

    /**
     * Get the count of active contracts
     */
    public static function getOpengingCount($type = null) {
        $contracts = self::where('status', self::STATUS_OPEN);

        if ((String)$type !== '')
            $contracts->where('type', $type);

        return $contracts->count();
    }

    public function actions() {
        return $this->hasMany('iJobDesk\Models\ContractAction');
    }

    public function getMessageThreadId() {
        $thread = ProjectMessageThread::where('application_id', $this->application_id)
                                      ->first();

        if ($thread)
            return $thread->id;
        return null;
    }

    public function enableStatusChanged() {
        $attributes = '';

        if ($this->status == self::STATUS_SUSPENDED) {
            $dispute = $this->getOpenedDispute();

            if (!$dispute)
                $attributes .= ' data-status-' . self::STATUS_OPEN . '=true';
        } elseif ($this->status == self::STATUS_OPEN) {
            $attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
        } elseif ($this->status == self::STATUS_PAUSED) {
            $attributes .= ' data-status-' . self::STATUS_OPEN . '=true';
            $attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
        }

        return $attributes;
    }

    /**
     * @param
     * @return dispute ticket for this contract
     */
    public function getOpenedDispute() {
        $ticket = Ticket::where('type', Ticket::TYPE_DISPUTE)
                        ->where('contract_id', $this->id)
                        ->whereNotIn('status', [Ticket::STATUS_SOLVED, Ticket::STATUS_CLOSED])
                        ->orderBy('created_at', 'DESC')
                        ->first();

        return $ticket;
    }

    /**
     * @param
     * @return solved dispute for this contract
     */
    public function getSolvedDisputes() {
        $tickets = Ticket::where('type', Ticket::TYPE_DISPUTE)
                         ->where('contract_id', $this->id)
                         ->whereIn('status', [Ticket::STATUS_SOLVED, Ticket::STATUS_CLOSED])
                         ->get();

        return $tickets;
    }

    /**
     * @param
     * @return last solved dispute for this contract
     */
    public function getLastSolvedDispute() {
        $ticket = Ticket::where('type', Ticket::TYPE_DISPUTE)
                        ->where('contract_id', $this->id)
                        ->whereIn('status', [Ticket::STATUS_SOLVED, Ticket::STATUS_CLOSED])
                        ->orderBy('created_at', 'DESC')
                        ->first();

        return $ticket;
    }

    /**
     * @param
     * @return The funded milestones.
     */
    public function getFundedMilestones() {
        $milestones = ContractMilestone::where('contract_id', $this->id)
                                       ->where('fund_status', ContractMilestone::FUNDED)
                                       ->get();

        return $milestones;
    }

    /**
     * Close all project applications when closing this contract
     */
    public function closeSelfAndApplications() {
        $user = Auth::user();
        
        $request = $_REQUEST;

        try {
            $projectApplication = ProjectApplication::find($this->application_id);
			// Close proposal
	        if ( $projectApplication ) {
	            $projectApplication->status = ProjectApplication::STATUS_HIRING_CLOSED;
	            $projectApplication->save();
	        }
	        
            if ( !$this->isClosed() || $user->isAdmin() ) {
                $this->closed_by = $user->isAdmin()?self::CLOSED_BY_IJOBDESK:($user->isBuyer()?self::CLOSED_BY_CLIENT:self::CLOSED_BY_FREELANCER);
                $this->closed_reason = $request['reason'];
                $this->status = self::STATUS_CLOSED;
                $this->ended_at = date('Y-m-d H:i:s');

                // If milestones are still in funding, all funds should be released or refunded.
                $performClose = true;
                if ( $this->isFixed() && !empty($request['confirm_fund']) ) {
                    $fundedMilestones = $this->fundedMilestones();
                    if ( $fundedMilestones ) {
                        foreach ( $fundedMilestones as $fund ) {
                            if ( $this->isClosedByBuyer() ) {
                                $fundResult = TransactionLocal::release($this->id, $fund->id);
                                if ( !$fundResult['success'] ) {
                                    $performClose = false;
                                }
                            } else if ( $this->isClosedByFreelancer() ) {
                                $fundResult = TransactionLocal::refund_fund($this->id, $fund->id, false, TransactionLocal::REFUND_REASON_END_CONTRACT);
                                if ( !$fundResult['success'] ) {
                                    $performClose = false;
                                }
                            }
                        }
                    }
                }
                
                if ( $performClose && $this->save() ) {
                    // Close all proposals
                    ProjectApplication::where('project_id', $this->project_id)
                                        ->update([
                                            'status' => ProjectApplication::STATUS_HIRING_CLOSED
                                        ]);

                    // Update contract_meters
                    $contractMeter = ContractMeter::where('contract_id', $this->id)->first();
                    if ( !$this->isHourly() && $contractMeter ) {
                        $contractMeter->total_amount = abs($this->totalPaid());
                        $contractMeter->save();
                    }
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            Log::error('[Contract::closeSelfAndApplications()] Error: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Close current contract
     * @param $request Request
     */
    public function closeAndLeaveFeedback(Request $request) {
        $user = Auth::user();

        try {
        	$only_feedback = false;

	        if ( !$this->isClosed() || $user->isAdmin() ) {
                if ($this->closeSelfAndApplications()) {
		            if ($user->isAdmin() || $user->isBuyer())
		                Notification::send(
		                    Notification::CONTRACT_CLOSED, 
		                    SUPERADMIN_ID,
		                    $this->contractor_id, 
		                    ['contract_title' => sprintf('%s', $this->title)]
		                );
		            
		            if ($user->isAdmin() || $user->isFreelancer())
		                Notification::send(
		                    Notification::CONTRACT_CLOSED, 
		                    SUPERADMIN_ID,
		                    $this->buyer_id, 
		                    ['contract_title' => sprintf('%s', $this->title)]
		                );

                    $email_key = 'CONTRACT_ENDED';
                    if ( !$this->canLeaveFeedback() ) {
                        $email_key = 'CONTRACT_ENDED_WITHOUT_PAYMENT';
                    }

                    $endedDate = date('M d, Y', strtotime($this->ended_at));
                    $contractUrl = _route('contract.contract_view', ['id' => $this->id]);
                    $transactionUrl = route('report.transactions');
                    $feedbackUrl = route('contract.feedback', ['id' => $this->id]);
                    $contractTotalPaid = abs($this->totalPaid());

                    if ( $this->isHourly() ) {
                        $contractTotalPaid += $this->meter->this_amount;
                    }

		            // Send email to buyer
		            EmailTemplate::send($this->buyer, $email_key, 0, [
                        'USER' => $this->buyer->fullname(),
                        'CONTRACT_TITLE' => $this->title,
                        'CONTRACT_URL' => _route('contract.contract_view', ['id' => $this->id]),
                        'CONTRACT_END_DATE' => $endedDate,
                        'TRANSACTION_URL' => $transactionUrl,
                        'GIVE_FEEDBACK_URL' => $feedbackUrl,
                        'PAID_TOTAL' => formatCurrency($contractTotalPaid),
                    ]);

		            // Send email to freelancer
		            EmailTemplate::send($this->contractor, $email_key, 0, [
                        'USER' => $this->contractor->fullname(),
                        'CONTRACT_TITLE' => $this->title,
                        'CONTRACT_URL' => _route('contract.contract_view', ['id' => $this->id]),
                        'CONTRACT_END_DATE' => $endedDate,
                        'TRANSACTION_URL' => $transactionUrl,
                        'GIVE_FEEDBACK_URL' => $feedbackUrl,
                        'PAID_TOTAL' => formatCurrency($contractTotalPaid),
                    ]);

		            if (!$user->isAdmin())
		                add_message(trans('message.buyer.contract.close.success_close', ['contract_title' => $this->title]), 'success');

		            $contractFeedback = new ContractFeedback;
		            $contractFeedback->contract_id = $this->id;
		        } else {
		        	throw new Exception('Error whiling updating contract.');
		        }
	        } else {
	        	$only_feedback = true;
	            $contractFeedback = $this->feedback;
	        }

	        if ( !$user->isSuper() ) {
                $role_name = strtolower($user->role_name());
	            $contractFeedback->{$role_name . '_score'}      = $request->input('review');
	            $contractFeedback->{$role_name . '_feedback'}   = $request->input('feedback');
	        }

	        if ( $contractFeedback->save() ) {
				if ( !$user->isSuper() && $only_feedback ) {
		        	add_message(trans('contract.contract_left_feedback', ['title' => $this->title]), 'success');
		        }

                // Update user's score
                if ( $user->isBuyer() ) {

                    $this->contractor->updateUserScore();

                } else if ( $user->isFreelancer() ) {

                    $this->buyer->updateUserScore();

                }
	        }

	    } catch ( Exception $e ) {
	    	Log::error('[Contract::closeAndLeaveFeedback()] Error: ' . $e->getMessage());
	    	return false;
	    }

        return true;
    }

    public function isAvailableAction($financial = false) {
    	if ( $this->isSuspended() ) {
    		return false;
    	}

    	if ( $this->buyer->isSuspended() ) {
    		return false;
    	}

    	if ( $this->contractor->isSuspended() ) {
    		return false;
    	}

    	if ( $financial && $this->buyer->isFinancialSuspended() ) {
    		return false;
    	}

    	if ( $financial && $this->contractor->isFinancialSuspended() ) {
    		return false;
    	}

    	return true;
    }

	public function isAvailableRefund() {
		if ( !$this->isClosed() ) {
			return true;
		}

		$date = date_diff(date_create(), date_create($this->ended_at));

		$days = $date->y * 365 + $date->m * 30 + $date->d;

		if ( $days > Settings::get('DAYS_AVAILABLE_REFUND') )
			return false;

		return true;
	}

	public function isAvailableDispute() {
        $user = Auth::user();

        if ($user->isAdmin())
            return false;

        if ($user->id != $this->buyer_id && $user->id != $this->contractor_id)
            return false;

		if ( !$this->isClosed() ) {
			return true;
		}

		$date = date_diff(date_create(), date_create($this->ended_at));

		$days = $date->y * 365 + $date->m * 30 + $date->d;

		if ( $days > Settings::get('DAYS_AVAILABLE_DISPUTE') )
			return false;

		return true;
	}

	public function isAvailableApproveManualTime() {
		if ( $this->isClosed() ) {
			return false;
		}

		if ( !$this->isAllowedManualTime() ) {
			return false;
		}

		$hourly_review = HourlyReview::getContractHourlyReview($this->id);
		if ( $hourly_review && !$hourly_review->isPending() ) {
			return false;
		}

		return true;
	}

    public static function isOverLimit($project) {
        return $project->contract_limit == Project::CONTRACT_LIMIT_ONE &&
               self::where('buyer_id', $project->client_id)
                   ->where('project_id', $project->id)
                   ->whereIn('status', [
                        self::STATUS_OFFER,
                        self::STATUS_OPEN,
                        self::STATUS_PAUSED,
                        self::STATUS_SUSPENDED
                   ])
                   ->exists();
    }

    public static function isStarted($project, $user) {
        return self::where('buyer_id', $project->client_id)
                   ->where('contractor_id', $user->id)
                   ->where('project_id', $project->id)
                   ->whereIn('status', [
                        self::STATUS_OPEN, 
                        self::STATUS_PAUSED, 
                        self::STATUS_SUSPENDED
                   ])
                   ->exists();
    }

    /**
     * Check if this project was started with this user. This include closed contract.
     */
    public static function isHired($project, $user) {
        return self::where('buyer_id', $project->client_id)
                   ->where('contractor_id', $user->id)
                   ->where('project_id', $project->id)
                   ->whereIn('status', [
                        self::STATUS_CLOSED, 
                        self::STATUS_OPEN, 
                        self::STATUS_PAUSED, 
                        self::STATUS_SUSPENDED
                   ])
                   ->exists();
    }

    public static function endedBuyerContracts($user_id) {
        return Contract::leftJoin('contract_meters', 'contracts.id', '=', 'contract_meters.contract_id')
                       ->where('contracts.buyer_id', $user_id)
                       ->where('contract_meters.total_amount', '>', 0)
                       ->where('contracts.status', Contract::STATUS_CLOSED);
    }
}