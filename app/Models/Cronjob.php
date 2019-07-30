<?php namespace iJobDesk\Models;

# @note: Methods starting with 'cr' means cron job method

use DB;
use Config;
use Log;
use Storage;
use File;

use iJobDesk\Models\Contract;
use iJobDesk\Models\ContractMeter;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\HourlyReview;
use iJobDesk\Models\Notification;
use iJobDesk\Models\NotifyInsufficientFund;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\ProjectSkill;
use iJobDesk\Models\Settings;
use iJobDesk\Models\Skill;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\User;
use iJobDesk\Models\UserDeposit;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\UserStat;
use iJobDesk\Models\UserPoint;
use iJobDesk\Models\UserSkill;
use iJobDesk\Models\UserSkillPoint;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\WalletHistory;
use iJobDesk\Models\SiteWallet;
use iJobDesk\Models\SiteWalletHistory;

class Cronjob extends Model {

	/**
	* Cronjob
	*
	* NOTE: freelancer, buyer, system
	* NOTE: define cronjob type for freelancer, buyer, system
	* @var string
	*/
	const TYPE_HOURLY_LOG_MAP = 1; // Run at every hour
	const TYPE_PROCESS_REVIEW_TRANSACTIONS = 2; // Run on every wednesday midnight
	const TYPE_PROCESS_PENDING_TRANSACTIONS = 3; // Run every day midnight
	const TYPE_REVIEW_LAST_WEEK = 4; // Run on every sunday midnight
	const TYPE_PROCESS_PROJECTS = 5; // Run at 1st day every month
	const TYPE_PROCESS_CONTRACTS = 6; // Run on every monday midnight
	const TYPE_PROCESS_USER_STATS = 7; // Run at 1st day every month
	const TYPE_PROCESS_USER_SKILL_POINTS = 8; // Run at 1st day every month
	const TYPE_PROCESS_USER_CONNECTS = 9; // Run everyday
	const TYPE_PROCESS_AFFILIATE_TRANSACTIONS = 10; // Run at 1st day every month, but affiliates transactions should be performed by super admin
	const TYPE_PROCESS_DEPOSITS = 11; // Run everyhour, proceeding requests
	const TYPE_PROCESS_SITE_WITHDRAWS = 12; // Run everyhour
	const TYPE_CHECK_WITHDRAWS = 13; // Run everyday, sending notificaction to admin for overdue requests
	const TYPE_PROCESS_USER_PAYMENT_METHODS = 14; // Run everyday, checking user payment methods
	const TYPE_CHECK_AFFILIATE_TRANSACTIONS = 15; // Run everyday, sending notificaction to admin for overdue requests
	const TYPE_PROCESS_USER_CREDIT_CARDS = 16; // Run every month, checking user credit cards expired
	const TYPE_PROCESS_WITHDRAWS = 17; // Run everyhour, proceeding requests
	const TYPE_CHECK_TRANSACTIONS = 18; // Run everyday, check the transactions
	const TYPE_JOB_RECOMMENDATION = 19; // Run every 10 mins, email to freelancers for job recommendation
	const TYPE_PROCESS_USER_PROJECTS = 20; // Run every hour, update buyer total projects posted

	const TYPE_PROCESS_USER_POINTS = 21; // Run by admin, update freelancer points
	const TYPE_UPDATE_USER_POINTS = 22; // Run every day, update freelancer points for recent activity and open jobs earning
	
	protected static $str_types = [
		self::TYPE_HOURLY_LOG_MAP => 'Generate Hourly Log Maps',
		self::TYPE_PROCESS_REVIEW_TRANSACTIONS => 'Process Review Transactions',
		self::TYPE_PROCESS_PENDING_TRANSACTIONS => 'Process Pending Transactions',
		self::TYPE_REVIEW_LAST_WEEK => 'Process Last Week Hourly Transactions',
		self::TYPE_PROCESS_PROJECTS => 'Process Jobs',
		self::TYPE_PROCESS_USER_STATS => 'Update User Stats',
		self::TYPE_PROCESS_USER_SKILL_POINTS => 'Update Freelancer Points By Skills',
		self::TYPE_PROCESS_USER_CONNECTS => 'Update Freelancer Connects',
		self::TYPE_PROCESS_CONTRACTS => 'Process Contracts',
		self::TYPE_PROCESS_AFFILIATE_TRANSACTIONS => 'Process Affiliates',
		self::TYPE_PROCESS_DEPOSITS => 'Process Deposits',
		self::TYPE_PROCESS_WITHDRAWS => 'Process Withdraws',
		self::TYPE_PROCESS_SITE_WITHDRAWS => 'Process Site Withdraws',
		self::TYPE_CHECK_WITHDRAWS => 'Check Withdraws',
		self::TYPE_CHECK_TRANSACTIONS => 'Check Transactions',
		self::TYPE_PROCESS_USER_PAYMENT_METHODS => 'Process User Payment Methods',
		self::TYPE_CHECK_AFFILIATE_TRANSACTIONS => 'Check Affiliate Transactions',
		self::TYPE_PROCESS_USER_CREDIT_CARDS => 'Check User Credit Cards',
		self::TYPE_JOB_RECOMMENDATION => 'Job Recommendation',
		self::TYPE_PROCESS_USER_PROJECTS => 'Update Total Projects Posted',
		self::TYPE_PROCESS_USER_POINTS => 'Update Freelancer Points',
	];

	/**
	* save(create or update) CronJob Data
	*
	* @author brice
	* @created Mar 30, 2016
	*
	* NOTE: define cronjob status
	*/
	const STATUS_DISABLED = 0;
	const STATUS_READY = 1;
	const STATUS_PROCESSING = 2;
	const STATUS_PAUSED = 3;	
	const STATUS_COMPLETED = 4;

	protected static $str_status = [
		self::STATUS_DISABLED => 'Disabled',
		self::STATUS_READY => 'Ready',
		self::STATUS_PROCESSING => 'Processing',
		self::STATUS_PAUSED => 'Paused',
		self::STATUS_COMPLETED => 'Completed',
	];

	protected $dates = ['done_at', 'created_at', 'updated_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    function __construct() {
        parent::__construct();
    }

    public function cronType() {
        return $this->hasOne('iJobDesk\Models\CronJobType', 'id', 'type');
    }

	/**
	* Init Cronjob
	*
	* @author Ro Un Nam
	* @since Dec 24, 2017
	*/
	public static function initCronJob($type)
	{
		try {
			$cr = new Cronjob;
			$cr->type = $type;
			$cr->save();

			return $cr;
		} catch (Exception $e) {
			Log::error('[Cronjob::initCronJob] ' . $e->getMessage());
			return false;
		}
	}

	/**
	* get CronJob Data
	*
	* @author Ro Un Nam
	* @since Oct 25, 2017
	* @param int $type: Cronjob Type
	*/
	public static function getCronJob($type) {
		return self::where('type', $type)->first();
	}
	
	public function status_string() {
		if ( isset(self::$str_status[$this->status]) ) {
			return self::$str_status[$this->status];
		}

		return '';
	}

	public function type_string() {
		$typeString = $this->cronType->name;

		if ( !$typeString ) {
			if ( isset(self::$str_types[$this->type]) ) {
				$typeString = self::$str_types[$this->type];
			}
		}

		return $typeString;
	}

	public function isDisabled() {
		return $this->status == self::STATUS_DISABLED; 
	}

	public function isReady() {
		return $this->status == self::STATUS_READY; 
	}

	public function isProcessing() {
		return $this->status == self::STATUS_PROCESSING; 
	}

	public function isPaused() {
		return $this->status == self::STATUS_PAUSED; 
	}

	public function isCompleted() {
		return $this->status == self::STATUS_COMPLETED; 
	}

	public function updateFields($params = []) {
		if ( $params ) {
			foreach ( $params as $key => $value ) {
				$this->$key = $value;
			}

			return $this->save();
		}

		return false;
	}

	public static function getOptions($cat) {
		if ($cat == 'status') {
			return [
				self::STATUS_DISABLED => 'Disabled',
				self::STATUS_READY => 'Ready', 
				self::STATUS_PROCESSING => 'Processing', 
				self::STATUS_PAUSED => 'Paused', 
				self::STATUS_COMPLETED => 'Completed', 
			];
		}

		return [];
	}

	public static function availableActionsByStatus($status) {
		$actions = [];

		if ( $status == self::STATUS_DISABLED ) {
			$actions[] = self::STATUS_READY;
			$actions[] = self::STATUS_PROCESSING;
		} else if ( $status == self::STATUS_READY ) {
			$actions[] = self::STATUS_DISABLED;
			$actions[] = self::STATUS_PROCESSING;
		} else if ( $status == self::STATUS_PAUSED ) {
			$actions[] = self::STATUS_DISABLED;
			$actions[] = self::STATUS_PROCESSING;
		} else if ( $status == self::STATUS_PROCESSING ) {
			$actions[] = self::STATUS_READY;
		} else if ( $status == self::STATUS_COMPLETED ) {
			$actions[] = self::STATUS_DISABLED;
			$actions[] = self::STATUS_PROCESSING;
		}

		$attributes = '';
		foreach ($actions as $action) {
			$attributes .= ' data-status-'.$action.'=true';
		}

		return $attributes;
	}

	public static function log($func, $error = '') {
		$params = [
			'NAME' => $func
		];

		Notification::sendToSuperAdmin('ERROR_CRON_JOB', SUPERADMIN_ID, $params);
		EmailTemplate::sendToSuperAdmin('SUPER_ADMIN_ERROR_CRON_JOB', User::ROLE_USER_SUPER_ADMIN, $params);
	}

	/**
	* Re-generate hourly_log_maps for the contracts that have changes after started_at of HLM generation
	*/
	public static function crHourlyLogMap() {
		$type = self::TYPE_HOURLY_LOG_MAP;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crHourlyLogMap] Disabled this cron job.');

			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$cids = HourlyLog::where('is_calculated', 0)
							->orWhere('is_deleted', 1)
							->select('contract_id')
							->distinct()
							->pluck('contract_id')
							->toArray();

			if ( $cids ) {
				foreach ($cids as $cid) {
					if ( HourlyLog::generateMap($cid) ) {
						HourlyLog::updateContractMeter($cid);
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crHourlyLogMap] ' . $e->getMessage());

			self::log('crHourlyLogMap', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Check hourly log and add weekly payment transaction for last week
	*
	* @author paulz
	* @created Mar 30, 2016
	*/
	public static function crReviewLastWeek() {
		$type = self::TYPE_REVIEW_LAST_WEEK;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crReviewLastWeek] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Reset last working hours and amount for all contracts
			ContractMeter::where('last_mins', '>', 0)
							->update([
								'last_mins' => 0,
								'last_amount' => 0.00
							]);

			$cids = HourlyLogMap::getActiveHourlyContractIds('last');

			if ( empty($cids) ) {
				Log::error('[Cronjob::crReviewLastWeek] No hourly contracts having logs for the last week.');
			} else {
				if ( count($cids) > 1 ) {
					$contracts = Contract::whereIn('id', $cids);
				} else {
					$contracts = Contract::where('id', $cids[0]);
				}

				$contracts = $contracts->get();

				list($from, $to) = weekRange('-1 weeks');

				$queue_emails = [];

				foreach ( $contracts as $contract ) {
					$mins = HourlyLogMap::getContractTotalMinsUnderReview($contract);

					if ( $mins <= 0 ) {
						continue;
					}

					$total_price = $contract->buyerPrice($mins);

					$existed = HourlyReview::where('contract_id', $contract->id)
											->where('buyer_id', $contract->buyer_id)
											->where('contractor_id', $contract->contractor_id)
											->where('hourly_from', $from)
											->where('hourly_to', $to)
											->first();
					if ( !$existed ) {
						$from_date = date('M d, Y', strtotime($from));
						$to_date = date('M d, Y', strtotime($to));

						$freelancer_name = $contract->contractor->fullname();
						$buyer_name = $contract->buyer->fullname();
						$contract_url = route('report.timesheet', ['from' => $from, 'to' => $to]);
						$timelogs_url = route('report.timelogs', ['from' => $from]);

						// Buyer should pay
						$pay_result = TransactionLocal::pay_hourly([
							'cid' => $contract->id, 
							'amount' => $total_price, 
							'type' => TransactionLocal::TYPE_HOURLY,
							'hourly_from' => $from,
							'hourly_to' => $to,
							'hourly_mins' => $mins,
							'status' => TransactionLocal::STATUS_DONE,
						]);
						
						if ( $pay_result['success'] && $pay_result['amount'] ) {
							$total_price = $pay_result['amount'];

							if ( !$queue_emails || !in_array($contract->buyer_id, $queue_emails) ) {
								// Send email to buyer
								EmailTemplate::send($contract->buyer, 'TIMELOG_REVIEW', 2, [
									'USER' => $buyer_name,
									'FREELANCER' => $freelancer_name,
									'CONTRACT_TITLE' => $contract->title,
									'CONTRACT_TRANSACTION_URL' => $contract_url,
									'AMOUNT' => formatCurrency($total_price),
									'START_DATE' => $from_date,
									'END_DATE' => $to_date,
								]);

								$queue_emails[] = $contract->buyer_id;
							}

							if ( !$queue_emails || !in_array($contract->contractor_id, $queue_emails) ) {
								// Send email to freelancer
								EmailTemplate::send($contract->contractor, 'TIMELOG_REVIEW', 1, [
									'USER' => $freelancer_name,
									'BUYER' => $buyer_name,
									'CONTRACT_TITLE' => $contract->title,
									'TIMELOG_URL' => $timelogs_url,
									'AMOUNT' => formatCurrency($total_price),
									'START_DATE' => $from_date,
									'END_DATE' => $to_date,
								]);

								$queue_emails[] = $contract->contractor_id;
							}

							$hourly_review = new HourlyReview;
							$hourly_review->contract_id = $contract->id;
							$hourly_review->buyer_id = $contract->buyer_id;
							$hourly_review->contractor_id = $contract->contractor_id;
							$hourly_review->hourly_from = $from;
							$hourly_review->hourly_to = $to;
							$hourly_review->hourly_mins = $mins;
							$hourly_review->amount = $total_price;
							$hourly_review->transaction_id = $pay_result['id'];

							if ( $hourly_review->save() ) {
								// Update contract_meters for the last week
								$contract->meter->last_mins = $mins;
								$contract->meter->last_amount = $total_price;
								$contract->meter->this_mins = 0;
								$contract->meter->this_amount = 0;
								$contract->meter->total_mins = $contract->meter->total_mins + $mins;
								$contract->meter->total_amount = $contract->meter->total_amount + $total_price;

								$contract->meter->save();
							}
						}
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crReviewLastWeek] ' . $e->getMessage());

			self::log('crReviewLastWeek', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Process hourly review to pending transaction
	* @author Ro Un Nam
	*/
	public static function crProcessReview($opts = []) {
		$type = self::TYPE_PROCESS_REVIEW_TRANSACTIONS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessReview] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		list($from, $to) = weekRange('-1 weeks');

		try {
			$hourly_reviews = HourlyReview::where('status', HourlyReview::STATUS_PENDING)
										->where('hourly_from', $from)
										->where('hourly_to', $to)
										->where('transaction_id', '>', 0)
										->get();

			foreach ($hourly_reviews as $review) {
				$amount = $review->amount;

				$res = TransactionLocal::pay_hourly([
					'tid' => $review->transaction_id,
					'cid' => $review->contract_id, 
					'amount' => $amount, 
					'type' => TransactionLocal::TYPE_HOURLY,
					'hourly_from' => $review->hourly_from,
					'hourly_to' => $review->hourly_to,
					'hourly_mins' => $review->hourly_mins,
				]);

				if ( $res['success'] ) {
					// Update contract_meters for the last week
					$review->contract->meter->last_mins = $review->hourly_mins;
					$review->contract->meter->last_amount = $amount;

					$review->contract->meter->save();

					// Update hourly_reviews
					$review->status = HourlyReview::STATUS_DONE;
					$review->save();
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessReview] ' . $e->getMessage());

			self::log('crProcessReview', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Processes pending transaction to apply changes to `wallets`
	*
	* @author paulz
	* @created Mar 30, 2016
	*/
	public static function crProcessPending($opts = []) {
		$type = self::TYPE_PROCESS_PENDING_TRANSACTIONS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessPending] Disabled this cron job.');
			return false;
		}
		
		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$transactions = TransactionLocal::where('status', TransactionLocal::STATUS_AVAILABLE)
											->whereNotIn('type', [
												TransactionLocal::TYPE_CHARGE,
												TransactionLocal::TYPE_WITHDRAWAL,
												TransactionLocal::TYPE_REFUND,
												TransactionLocal::TYPE_SITE_WITHDRAWAL,
												TransactionLocal::TYPE_FEATURED_JOB,
												TransactionLocal::TYPE_AFFILIATE,
												TransactionLocal::TYPE_AFFILIATE_CHILD
											])
											->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
											->where('user_id', '<>', SUPERADMIN_ID)
											->whereRaw('DATEDIFF(CURDATE(), created_at) >= ' . Settings::get('DAYS_PROCESS_PENDING_TRANSACTION'))
											->get();

			foreach ($transactions as $t) {
				if ( $t->amount == 0 || $t->user_id == 0 ) {
					continue;
				}

				if ( $t->user && ($t->user->isSuspended() || $t->user->isFinancialSuspended()) ) {
					Log::error('[Cronjob::crProcessPending()] Error: User has been suspended or financial suspended [' . $t->user_id . '].');
					continue;
				}

				$now = date('Y-m-d H:i:s');

				$t->status = TransactionLocal::STATUS_DONE;
				$t->done_at = $now;
				
				if ( $t->save() ) {
				  	// Update user wallet history
			  		$wallet = Wallet::account($t->user_id);
			  		$newAmount = round($wallet->amount + $t->amount, 2);
					$wallet->amount = $newAmount;
					$wallet->save();

					WalletHistory::addHistory($t->user_id, $newAmount, $t->id);

					// Update iJobDesk fee transaction
					$fee = TransactionLocal::where('ref_id', $t->id)->first();
					if ( $fee ) {
						$fee->status = TransactionLocal::STATUS_DONE;
						$fee->done_at = $now;
						$fee->save();

						// Update iJobDesk earning wallet history
						$earning = SiteWallet::earning();
						$newAmount = round($earning->amount + $fee->amount, 2);
						$earning->amount = $newAmount;
						$earning->save();

						SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $fee->id);
					}
				}

			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessPending] ' . $e->getMessage());

			self::log('crProcessPending', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Update user_stat by cron job
	* @author Ro Un Nam
	* @since Jun 15, 2017
	*/
	public static function crProcessUserStat() {
		$type = self::TYPE_PROCESS_USER_STATS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessUserStat] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$users = User::whereIn('role', [
							User::ROLE_USER_FREELANCER,
							User::ROLE_USER_BUYER,
						])
						->where('status', User::STATUS_AVAILABLE)
						->get();

			foreach ( $users as $u ) {
				if ( !$u->stat ) {
					$u->stat = new UserStat;
					$u->stat->user_id = $u->id;
				}

				if ( $u->isFreelancer() ) {
					$hours = $u->howManyHours();

					$u->stat->hours = round($hours[0] / 60);
					$u->stat->last6_hours = round($hours[1] / 60);
					$u->stat->earning = $u->totalEarned();
					$u->stat->earning_6months = $u->totalEarned(6);
					$u->stat->earning_12months = $u->totalEarned(12);

					// $u->stat->ratings = $u->totalRatings();
					// $u->stat->job_success = $u->getJobSuccess();
					$u->stat->contracts = $u->totalClosedContracts();
					$u->stat->open_contracts = $u->totalActiveContracts();
					$u->stat->hourly_contracts = $u->totalClosedHourlyContracts();
					$u->stat->total_portfolios = count($u->portfolios);

					// Update user points
					if ( Settings::get('POINT_LAST_12MONTHS_ENABLED') ) {
						$u->point->last_12months = $u->totalContractPoints();
					} else {
						$u->point->last_12months = 0;
					}

					if ( Settings::get('POINT_LIFETIME_ENABLED') ) {
						$u->point->lifetime = $u->totalContractPoints(false);
					} else {
						$u->point->lifetime = 0;
					}

					$u->point->save();
				} else if ( $u->isBuyer() ) {
					// $u->stat->jobs_posted = $u->totalJobsPosted();
					$u->stat->contracts = $u->totalClosedContracts();
					$u->stat->open_contracts = $u->totalActiveContracts();
					$u->stat->hourly_contracts = $u->totalClosedHourlyContracts();
					$u->stat->hire_rate = $u->getHireRate();
					$u->stat->total_spent = $u->totalSpent();
					$u->stat->total_paid_hrs = $u->totalPaidHours();

					$totalPaidHourly = $u->totalPaidHourly();
					$u->stat->avg_paid_rate = $u->stat->total_paid_hrs ? round($totalPaidHourly / $u->stat->total_paid_hrs, 2) : 0;
				}

				// $u->stat->score = $u->totalScore();
				$u->stat->total_reviews = $u->totalReviews();
				$u->stat->total_users_suspended = $u->totalSuspended();
				$u->stat->total_jobs_disputed = $u->totalDisputedContracts();
				
				$u->stat->save();
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessUserStat] ' . $e->getMessage());

			self::log('crProcessUserStat', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Close projects expired by cron job
	* @author Ro Un Nam
	* @since Aug 22, 2017
	*/
	public static function crProcessProjects() {
		$type = self::TYPE_PROCESS_PROJECTS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessProjects] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		$expired_days = Settings::get('DAYS_PROJECT_EXPIRED');
		$expired_date = date('Y-m-d 00:00:00', strtotime('-' . $expired_days . ' days'));

		try {
			$projects = Project::where('status', '<>', Project::STATUS_CLOSED)
								->where('created_at', '<', $expired_date)
								->get();

			if ( count($projects) ) {
				foreach ( $projects as $project ) {
					$project->status = Project::STATUS_CLOSED;
					$project->cancelled_at = date('Y-m-d H:i:s');
					if ( $project->save() ) {
						// Send email to buyer
						EmailTemplate::send($project->client, 'JOB_EXPIRED', 2, [
							'JOB_POSTING' => $project->subject,
							'JOB_POSTING_URL' => _route('job.view', ['id' => $project->id], true, null, $project->client),
						]);

						// Expire all proposals
						$proposals =  ProjectApplication::where('project_id', $project->id)
														->whereIn('status', [
															ProjectApplication::STATUS_NORMAL,
															ProjectApplication::STATUS_ACTIVE
														])
													   ->get();
						if ( count($proposals) ) {
							$search_job_url = route('search.job');

							foreach ( $proposals as $application ) {
								EmailTemplate::send($application->user, 'JOB_EXPIRED', 1, [
									'USER' => $application->user->fullname(),
									'JOB_POSTING' => $project->subject,
									'SEARCH_JOB_URL' => $search_job_url,
								]);
								
								$application->status = ProjectApplication::STATUS_PROJECT_EXPIRED;
								$application->is_archived = ProjectApplication::IS_ARCHIVED_YES;
								$application->save();
							}
						}
					}
				}
			}

			$expired_next_date = date('Y-m-d 00:00:00', strtotime('-' . ($expired_days - 1) . ' days'));
			$future_projects = Project::where('status', '<>', Project::STATUS_CLOSED)
								->where('created_at', '>=', $expired_date)
								->where('created_at', '<', $expired_next_date)
								->get();

			if ( count($future_projects) ) {
				foreach ( $future_projects as $project ) {
					// Send email to buyer
					EmailTemplate::send($project->client, 'JOB_EXPIRED_SOON', 2, [
						'JOB_POSTING' => $project->subject,
						'JOB_POSTING_URL' => _route('job.view', ['id' => $project->id], true, null, $project->client),
					]);
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessProjects] ' . $e->getMessage());

			self::log('crProcessProjects', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* And set the new weekly limit changed
	* @author Ro Un Nam
	* @since Dec 15, 2017
	*/
	public static function crProcessContracts() {
		$type = self::TYPE_PROCESS_CONTRACTS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessContracts] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$contracts = Contract::where('type', Contract::TYPE_HOURLY)
								->where('new_limit', '<>', 0)
								->whereIn('status', [
		                            Contract::STATUS_OPEN,
		                            Contract::STATUS_PAUSED,
		                            Contract::STATUS_SUSPENDED,
		                        ])
								->get();

			if ( $contracts ) {
				foreach ( $contracts as $c ) {
					$c->limit = $c->new_limit;
					$c->new_limit = 0;
					$c->save();
				}
			}

		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessContracts] ' . $e->getMessage());

			self::log('crProcessContracts', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Reset Connects
	* Check registered and last activity for user point
	* @author PYH
	* @since Sep 27, 2017
	*/
	public static function crResetConnects() {
		$type = self::TYPE_PROCESS_USER_CONNECTS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crResetConnects] Disabled this cron job.');
			return false;
		}
		
		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$freelancers = User::where('role', User::ROLE_USER_FREELANCER)
								->where('status', User::STATUS_AVAILABLE)
								->get();
			$today = date('Y-m-d');
			$today_timestamp = strtotime($today);

			$totalConnectionsReset = Settings::get('TOTAL_CONNECTIONS_RESET');

			foreach ($freelancers as $freelancer) {
				if ( $freelancer->needRefreshConnects() ) {
					$created_at = date('Y-m-d', strtotime($freelancer->created_at));
					$created_at_timestamp = strtotime($created_at);
					$interval_timestamp = $today_timestamp - $created_at_timestamp;

					$cycle_timestamp = Settings::get('DAYS_RESET_CONNECTIONS') * 24 * 3600;

					$mod = $interval_timestamp % $cycle_timestamp;

					if ($mod == 0) {
						if ( $freelancer->stat ) {
							$freelancer->stat->connects = $totalConnectionsReset;
							$freelancer->stat->connects_reset_at = date('Y-m-d H:i:s');
							$freelancer->stat->save();
						}
					}
				}

				/*********** Update points ***********/
				// Check new freelancer
				if ( ago_days($freelancer->created_at) > 90 ) {
					if ( $freelancer->point->new_freelancer > 0 ) {
						$freelancer->point->new_freelancer = 0;
					}
				}
				
				// Check recent activity
				if ( Settings::get('POINT_ACTIVITY_ENABLED') ) {
					if ( ago_days($freelancer->stat->last_activity) > 3 ) {
						if ( $freelancer->point->activity > 0 ) {
							$freelancer->point->activity = 0;
						}
					} else {
						if ( !$freelancer->point->activity ) {
							$freelancer->point->activity = Settings::get('POINT_ACTIVITY');
						}
					}
				} else {
					$freelancer->point->activity = 0;
				}

				// Open jobs earning
				if ( Settings::get('POINT_OPEN_JOBS_ENABLED') ) {
					$totalEarnings = $freelancer->totalEarnedOpenContracts();

					if ( $totalEarnings ) {
						$freelancer->point->open_jobs = ($totalEarnings * Settings::get('POINT_OPEN_JOBS')) / 2;
					}
				} else {
					$freelancer->point->open_jobs = 0;
				}

				// Changed
				$freelancer->point->save();
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crResetConnects] ' . $e->getMessage());

			self::log('crResetConnects', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Process user skill points
	* @author Ro Un Nam
	* @since Oct 19, 2017
	*/
	public static function crProcessUserSkillPoints() {
		$type = self::TYPE_PROCESS_USER_SKILL_POINTS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessUserSkillPoints] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
	    	$freelancers = User::leftJoin('user_skill_points', 'users.id', '=', 'user_skill_points.user_id')
	    						->where('role', User::ROLE_USER_FREELANCER)
								->where('status', User::STATUS_AVAILABLE)
								->select('users.*')
								->get();

			foreach ( $freelancers as $u ) {
				if ( $u->needRefreshSkillPoint() ) {
					if ( $u->skillPoint ) {
						$skillPoint = $u->skillPoint;
					} else {
						$skillPoint = new UserSkillPoint;
						$skillPoint->user_id = $u->id;
						$skillPoint->updated_at = date('Y-m-d H:i:s');
					}

					foreach ( Skill::USER_POINT_SKILLS as $skill ) {
						$user_contracts_by_skill = Contract::leftJoin('contract_meters', 'contracts.id', '=', 'contract_meters.contract_id')
															->leftJoin('contract_feedbacks', 'contracts.id', '=', 'contract_feedbacks.contract_id')
															->where('contracts.contractor_id', $u->id)
															->where('contracts.status', Contract::STATUS_CLOSED)
															->where('contracts.is_calculated', 0)
															->whereRaw('LOWER(contracts.title) LIKE "%' . strtolower($skill) . '%"')
															->select([
																'contracts.id',
																'contracts.title',
																'contract_meters.total_amount',
																'contract_feedbacks.freelancer_score'
															])
															->get();

						if ( count($user_contracts_by_skill) ) {
							foreach ( $user_contracts_by_skill as $c ) {
								$point = round($c->freelancer_score * $c->total_amount / 100, 2);

								$point_field = 'c_' . $skill;
								$skillPoint->$point_field += $point;							

								$c->is_calculated = 1;
								$c->save();
							}
						}
					}

					$skillPoint->save();
				}							
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessUserSkillPoints] ' . $e->getMessage());

			self::log('crProcessUserSkillPoints', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
    }

	/**
	* Process user points
	* @author Ro Un Nam
	* @since Aug 29, 2018
	*/
	public static function crProcessUserPoints() {
		$type = self::TYPE_PROCESS_USER_POINTS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( !$cr->isReady() ) {
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$last_updated_time = date('Y-m-d H:i:s', strtotime('-1 hour'));

	    	$user_points = UserPoint::get();

			foreach ( $user_points as $up ) {
				$up->updateAll();
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessUserPoints] ' . $e->getMessage());

			self::log('crProcessUserPoints', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_DISABLED
		]);
    }

    /**
	* Processes affiliate transactions
	*
	* @author Ro Un Nam
	* @since Oct 27, 2017
	*/
	public static function crProcessAffiliate($opts = []) {
		$type = self::TYPE_PROCESS_AFFILIATE_TRANSACTIONS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessAffiliate] Disabled this cron job.');
			return false;
		}
		
		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Affiliate fee rate
			$fee_rate = Settings::getAffiliateFreelancerFeeRate();
			$child_fee_rate = Settings::getAffiliateChildFreelancerFeeRate();

			//$yesterday = date('Y-m-d', strtotime('-1 day'));

			//$created_at_between = [$yesterday . ' 00:00:00', $yesterday . ' 23:59:59'];

			$query = TransactionLocal::where('for', TransactionLocal::FOR_FREELANCER)
									//->whereBetween('created_at', $created_at_between)
									->whereIn('type', [
										TransactionLocal::TYPE_FIXED,
										TransactionLocal::TYPE_BONUS,
										TransactionLocal::TYPE_REFUND,
										TransactionLocal::TYPE_HOURLY
									])
									->where('status', TransactionLocal::STATUS_DONE)
									->where('checked_affiliate', 0)
									->where('user_id', '<>', SUPERADMIN_ID);

			$ts = $query->get();

			foreach ($ts as $t) {
				TransactionLocal::process_affiliate($t);
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessAffiliate] ' . $e->getMessage());

			self::log('crProcessAffiliate', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Update proceeding deposits by cron job
	* @author Ro Un Nam
	* @since Dec 18, 2017
	*/
	public static function crProcessDeposits($force = false) {
		$type = self::TYPE_PROCESS_DEPOSITS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessDeposits] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Get all proceeding deposits.
			$deposits = TransactionLocal::where('status', TransactionLocal::STATUS_PROCEEDING)
										->where('type', TransactionLocal::TYPE_CHARGE);

			if ( !$force ) {
				$deposits = $deposits->whereRaw('HOUR(TIMEDIFF(NOW(), updated_at)) >= 1');
			}

			$deposits = $deposits->get();

			if ( count($deposits) ) {
				foreach ( $deposits as $t ) {
					$t->status = TransactionLocal::STATUS_DONE;
					$t->done_at = date('Y-m-d H:i:s');
					
					if ( $t->save() ) {
						// Update amount in user_deposits table
						if ( $t->userPaymentGateway && $t->userPaymentGateway->real_id ) {
							UserDeposit::updateAmount($t->user_id, $t->userPaymentGateway->gateway, $t->userPaymentGateway->real_id, $t->amount);
						}

						// Update user wallet history
						$wallet = Wallet::account($t->user_id);
	                    $wallet->amount += $t->amount;
	                    $wallet->save();

						WalletHistory::addHistory($t->user_id, $wallet->amount, $t->id);

						// Update notify_insufficient_fund table
						NotifyInsufficientFund::updateClient($t->user_id);

						// Update iJobDesk holding wallet history
	                    $holding = SiteWallet::holding();
	                    $newAmount = $holding->amount + $t->amount;
	                    $holding->amount = $newAmount;
	                    $holding->save();

	                    SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_HOLDING, $newAmount, $t->id);

						// Check affiliate
						// TransactionLocal::addDepositAffiliates($t->user_id);

				  		// Send notification and email
				  		Notification::send(Notification::BUYER_DEPOSIT, SUPERADMIN_ID, $t->user_id, [
						 	'amount' => formatCurrency($t->amount)
						]);

						EmailTemplate::send($t->user, 'DEPOSIT', User::ROLE_USER_BUYER, [
				            'USER' => $t->user->fullname(),
							'AMOUNT' => formatCurrency($t->amount),
						]);
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessDeposits] ' . $e->getMessage());

			self::log('crProcessDeposits', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Update proceeding withdraws by cron job
	* @author Ro Un Nam
	* @since Jun 10, 2018
	*/
	public static function crProcessWithdraws($force = false) {
		$type = self::TYPE_PROCESS_WITHDRAWS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessWithdraws] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Get all proceeding withdraws.
			$withdraws = TransactionLocal::where('status', TransactionLocal::STATUS_PROCEEDING)
										 ->where('type', TransactionLocal::TYPE_WITHDRAWAL)
										 ->where('for', '<>', TransactionLocal::FOR_IJOBDESK);
			if (!$force) {
				$withdraws->whereRaw('HOUR(TIMEDIFF(NOW(), updated_at)) >= 1');
			}

			$withdraws = $withdraws->get();

			if ( count($withdraws) ) {
				$now = date('Y-m-d H:i:s');

				foreach ( $withdraws as $t ) {
					$t->status = TransactionLocal::STATUS_DONE;
					$t->done_at = $now;
					
					if ( $t->save() ) {
                        $amount = abs($t->amount);
                        
						// Update iJobDesk holding wallet history
						$holding = SiteWallet::holding();
						$newAmount = $holding->amount - $amount;
						$holding->amount = $newAmount;
						$holding->save();

						// Fee
						$fee_amount = 0;
						if ( $t->reference ) {
							$fee_amount = abs($t->reference->amount);
						}

                        // Send notification and email
		                Notification::send(
		                    Notification::USER_WITHDRAWAL, 
		                    SUPERADMIN_ID,
		                    $t->user_id, 
		                    [
		                        'amount' => formatCurrency($amount + $fee_amount)
		                    ]
		                );

		                EmailTemplate::send($t->user, 'WITHDRAW', 0, [
		                    'USER' => $t->user->fullname(),
		                    'AMOUNT' => formatCurrency($amount + $fee_amount),
		                    'PAYMENT_METHOD' => $t->gateway_string(),
		                    'CONTACT_US_URL' => route('frontend.contact_us'),
		                ]);

						SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_HOLDING, $newAmount, $t->id);

                        $fee = TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
                        						->where('ref_id', $t->id)
	                        					->where('user_id', SUPERADMIN_ID)
	                        					->first();
                        if ( $fee ) {
                            // Update withdraw fee transaction
							TransactionLocal::where('type', TransactionLocal::TYPE_WITHDRAWAL)
                    						->where('ref_id', $t->id)
                        					->update([
                        						'status' => TransactionLocal::STATUS_DONE,
                        						'done_at' => $now,
                        					]);

							if ( $fee->save() ) {
								// Update iJobDesk earning wallet history
								$earning = SiteWallet::earning();
								$newAmount = $earning->amount + $fee->amount;
								$earning->amount = $newAmount;
								$earning->save();

								SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $fee->id);
							}
						}
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessWithdraws] ' . $e->getMessage());

			self::log('crProcessWithdraws', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Checking withdraw requests
	* @author Ro Un Nam
	* @since Jan 16, 2018
	*/
	public static function crCheckWithdraws() {
		$type = self::TYPE_CHECK_WITHDRAWS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crCheckWithdraws] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Get all overdue withdraws.
			$withdraws = TransactionLocal::getOverdueWithdraws();

			if ( count($withdraws) ) {
				$withdraws_string = '';
				$amount = 0;

				foreach ( $withdraws as $t ) {
					$withdraws_string .= '$' . formatCurrency(abs($t->amount)) . ' by ' . $t->user->fullname() . ' on ' . format_date('M d, Y g:i A', $t->created_at) . "\n\r\n\r";

					$amount += abs($t->amount);
				}

				// Send email to super admin or financial manager
            	$admin_users = User::getAdminUsers([
	                User::ROLE_USER_SUPER_ADMIN,
	                User::ROLE_USER_FINANCIAL_MANAGER
	            ]);

	            if ( $admin_users ) {
	            	foreach ( $admin_users as $admin_user ) {
						Notification::send('OVERDUE_WITHDRAWS', SUPERADMIN_ID, $admin_user->id, [
							'TOTAL' => count($withdraws),
						]);

						EmailTemplate::send($admin_user, 'SUPER_ADMIN_OVERDUE_WITHDRAWS', 0, [
							'USER' => $admin_user->fullname(),
							'WITHDRAWS' => $withdraws_string,
							'AMOUNT' => formatCurrency($amount),
						]);
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crCheckWithdraws] ' . $e->getMessage());

			self::log('crCheckWithdraws', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Update pending ijobdesk withdraws by cron job
	* @author Ro Un Nam
	* @since Jan 4, 2018
	*/
	public static function crProcessSiteWithdraws() {
		$type = self::TYPE_PROCESS_SITE_WITHDRAWS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessSiteWithdraws] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Get all pending site withdraws.
			$withdraws = TransactionLocal::where('status', TransactionLocal::STATUS_PROCEEDING)
									->where('type', TransactionLocal::TYPE_SITE_WITHDRAWAL)
									->whereRaw('HOUR(TIMEDIFF(NOW(), updated_at)) >= 1')
									->get();

			if ( count($withdraws) ) {
				foreach ( $withdraws as $t ) {
					$t->status = TransactionLocal::STATUS_DONE;
					$t->done_at = date('Y-m-d H:i:s');

					if ( $t->save() ) {
						// Update iJobDesk holding wallet history
						$holding = SiteWallet::holding();
						$newAmount = $holding->amount - abs($t->amount);
						$holding->amount = $newAmount;
						$holding->save();

						SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_HOLDING, $newAmount, $t->id);

						// Send email to super admin
						EmailTemplate::sendToSuperAdmin('SUPER_ADMIN_SITE_WITHDRAW', User::ROLE_USER_SUPER_ADMIN, [
							'AMOUNT' => formatCurrency(abs($t->amount)),
							'DOER' => $t->user->fullname(),
							'ROLE' => array_get(User::adminType(), $t->user->role),
							'DATE' => date('Y/m/d H:i:s')
						]);
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessSiteWithdraws] ' . $e->getMessage());

			self::log('crProcessSiteWithdraws', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Check the user payment methods added and approve
	* @author Ro Un Nam
	* @since Jan 18, 2018
	*/
	public static function crProcessUserPaymentMethods() {
		$type = self::TYPE_PROCESS_USER_PAYMENT_METHODS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessUserPaymentMethods] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Get pending user payment methods.
			UserPaymentGateway::where('status', UserPaymentGateway::IS_STATUS_YES)
								->where('is_pending', UserPaymentGateway::IS_PENDING_YES)
								->whereRaw('DATEDIFF(CURDATE(), updated_at) >= ' . Settings::get('DAYS_AVAILABLE_PAYMENT_METHOD'))
								->update([
									'is_pending' => UserPaymentGateway::IS_PENDING_NO
								]);
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessUserPaymentMethods] ' . $e->getMessage());

			self::log('crProcessUserPaymentMethods', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Checking withdraw requests
	* @author Ro Un Nam
	* @since Jan 16, 2018
	*/
	public static function crCheckAffiliateTransactions() {
		$type = self::TYPE_CHECK_AFFILIATE_TRANSACTIONS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crCheckAffiliateTransactions] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Get all overdue transactions.
			$transactions = TransactionLocal::getOverdueAffiliateTransactions();

			if ( count($transactions) ) {
				Notification::sendToSuperAdmin('OVERDUE_AFFILIATE_TRANSACTIONS', SUPERADMIN_ID, [
					'TOTAL' => count($transactions),
				]);

				EmailTemplate::sendToSuperAdmin('SUPER_ADMIN_OVERDUE_AFFILIATE_TRANSACTIONS', User::ROLE_USER_SUPER_ADMIN, [
					'TOTAL' => count($transactions),
				]);
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crCheckAffiliateTransactions] ' . $e->getMessage());

			self::log('crCheckAffiliateTransactions', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Check the user credit cards expired
	* @author Ro Un Nam
	* @since May 7, 2018
	*/
	public static function crProcessUserCreditCards() {
		$type = self::TYPE_PROCESS_USER_CREDIT_CARDS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessUserCreditCards] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$search = sprintf('"expDateYear":"%d","expDateMonth":"%d"', date('Y'), date('m'));

			// Get expired user credit cards
			UserPaymentGateway::where('status', UserPaymentGateway::IS_STATUS_YES)
								->whereRaw("`data` LIKE '%" . $search . "%'")
								->update([
									'status' => UserPaymentGateway::IS_STATUS_EXPIRED,
									'is_primary' => UserPaymentGateway::IS_PRIMARY_NO
								]);
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessUserCreditCards] ' . $e->getMessage());

			self::log('crProcessUserCreditCards', $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Check the transactions for all deposits, withdraws and holding or earning amount and send email
	* @author Ro Un Nam
	* @since Jun 12, 2018
	*/
	public static function crCheckTransactions() {
		$type = self::TYPE_CHECK_TRANSACTIONS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}

		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crCheckTransactions] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// File::put(storage_path('/logs/test.txt'), 'Testing');
			// $result = File::makeDirectory('/path/to/directory', 0775);

			$logHeader = sprintf('------------- %s ------------', date('Y-m-d H:i:s'));

			$totalDeposits = TransactionLocal::totalAmountDeposits();
			$totalWithdraws = TransactionLocal::totalAmountWithdraws();
			$totalHolding = round($totalDeposits - $totalWithdraws, 2);
			$holding = SiteWallet::holding();

			// Check site balance
			$logContent = "\n" . '== checksum for Total Deposit & Withdrawal & Site Balance ==';
			$logContent .= "\n\t" . 'Total Deposit: $' . formatCurrency($totalDeposits);
			$logContent .= "\n\t" . 'Total Withdrawal: $' . formatCurrency($totalWithdraws);
			$logContent .= "\n\t" . 'Site Balance: $' . formatCurrency($totalHolding);

			if ( doubleval($totalHolding) == doubleval($holding->amount) ) {
				$logContent .= "\n\t" . 'OK';
			} else {
				$logContent .= "\n\t" . 'Fatal Error: $' . formatCurrency($holding->amount);
			}

			// Check site earning
			$totalEarning = TransactionLocal::getEarningAmount();
			$earning = SiteWallet::earning();

			$logContent .= "\n\n" . '== checksum for Site Earning ==';
			$logContent .= "\n\t" . 'Total Earning: $' . formatCurrency($totalEarning);

			if ( doubleval($totalEarning) == doubleval($earning->amount) ) {
				$logContent .= "\n\t" . 'OK';
			} else {
				$logContent .= "\n\t" . 'Fatal Error: $' . formatCurrency($earning->amount);
			}

			// Check site balance
			$totalBalance = Wallet::totalWallet();
			
			$totalEscrows = TransactionLocal::totalAmountEscrows();
			$totalPendingTransactions = TransactionLocal::totalAmountPending();
			$totalPendingWithdraws = TransactionLocal::totalAmountPendingWithdraws();

			$totalPending = round($totalEscrows + $totalPendingTransactions + $totalPendingWithdraws + $totalEarning, 2);

			$logContent .= "\n\n" . '== checksum for Total users balance & Pending & Site Balance ==';
			$logContent .= "\n\t" . 'Total Users Balance: $' . formatCurrency($totalBalance);
			$logContent .= "\n\t" . 'Total Pending: $' . formatCurrency($totalPending);
			$logContent .= "\n\t\t" . 'Total Escrows: $' . formatCurrency($totalEscrows);
			$logContent .= "\n\t\t" . 'Total Pending Transactions: $' . formatCurrency($totalPendingTransactions);
			$logContent .= "\n\t\t" . 'Total Pending Withdraws: $' . formatCurrency($totalPendingWithdraws);
			$logContent .= "\n\t" . 'Total Site Earning: $' . formatCurrency($totalEarning);
			$logContent .= "\n\n\t" . 'Site Balance: $' . formatCurrency($totalHolding);
			$totalHolding = round($totalBalance + $totalPending, 2);

			if ( doubleval($totalHolding) == doubleval($holding->amount) ) {
				$logContent .= "\n\t" . 'OK';
			} else {
				$logContent .= "\n\t" . 'Fatal Error: $' . formatCurrency($totalHolding);
			}

			$logContent .= "\n\n" . '== checksum for Each User ==';

			$users = User::where('status', '<>', User::STATUS_NOT_AVAILABLE)
							->whereIn('role', [
								User::ROLE_USER_FREELANCER,
								User::ROLE_USER_BUYER,
							])
							->orderBy('id', 'asc')
							->get();
			foreach ( $users as $u ) {
				$wallet = Wallet::account($u->id);
				$totalBalance = TransactionLocal::getUserAmount($u->id);

				$logContent .= "\n\t" . '-- ' . $u->fullname() . ' ($' . formatCurrency($wallet->amount) . ') --';

				if ( doubleval($wallet->amount) == doubleval($totalBalance) ) {
					$logContent .= "\n\t\t" . 'OK';
				} else {
					$logContent .= "\n\t\t" . 'Fatal Error: $' . formatCurrency($totalBalance);
				}
			}

			$log = $logHeader . "\n" . $logContent;

			File::put(storage_path('/logs/transactions.log'), $log);
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crCheckTransactions] ' . $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Send an emaiil about job recommendation to freelancers
	* @author Ro Un Nam
	* @since Jul 23, 2018
	*/
	public static function crJobRecommendation() {
		$type = self::TYPE_JOB_RECOMMENDATION;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crJobRecommendation] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			// Get user skills
			/*
			$skill_ids = UserSkill::where('user_id', $u->id)
									->pluck('skill_id')
									->toArray();

			if ( $skill_ids ) {
				$total_projects = Project::leftJoin('project_skills', 'project_skills.project_id', '=', 'projects.id')
										->where('projects.status', '<>', Project::STATUS_PRIVATE)
										->whereRaw('HOUR(TIMEDIFF(NOW(), projects.created_at)) < 24')
										->whereIn('project_skills.skill_id', $skill_ids)
										->select('projects.id')
										->groupBy('projects.id')
										->get();
				
				EmailTemplate::send($u, 'JOB_RECOMMENDATION', 1, [
					'USER' => $u->fullname(),
				]);
			}
			*/

			// Get new projects
			$created_at = date('Y-m-d H:i:s', strtotime('-15 minutes'));
			$new_projects = Project::where('created_at', '>=', $created_at)
									->where('accept_term', Project::ACCEPT_TERM_YES)
									->where('status', Project::STATUS_OPEN)
									->whereIn('is_public', [
										Project::STATUS_PUBLIC,
										Project::STATUS_PROTECTED
									])
									->orderBy('created_at', 'asc')
									->get();

			if ( $new_projects ) {
				foreach ( $new_projects as $project ) {
					$job_url = _route('job.view', ['id' => $project->id], true, null, null, true);

					if ( $project->isHourly() ) {
						$job_type = trans('common.hourly');
					} else {
						$job_type = trans('common.fixed_price');
					}

					if ( intval($project->experience_level) == Project::EXPERIENCE_LEVEL_EXPERT ) {
                        $job_type .= ' - ' . trans('job.expert');
					} else if ( intval($project->experience_level) == Project::EXPERIENCE_LEVEL_INTERMEDIATE ) {
                        $job_type .= ' - ' . trans('job.intermediate');
					} else {
                        $job_type .= ' - ' . trans('job.entry');
					}

					if ( $project->isHourly() ) {
						$job_type .= ' - ' . trans('common.budget') . ': ' . $project->affordable_rate_string();
					} else {
						$job_type .= ' - ' . trans('common.budget') . ': ' . $project->price_string(true);
					}

					$job_desc = strip_tags($project->desc);
					if ( mb_strlen($job_desc) > 200 ) {
						$job_desc = mb_substr($job_desc, 0, 200) . '...';
					}

			    	// Get all freelancers
			    	$freelancers = User::where('role', User::ROLE_USER_FREELANCER)
										->where('status', User::STATUS_AVAILABLE)
										->select('users.*')
										->get();

					foreach ( $freelancers as $u ) {
						EmailTemplate::send($u, 'JOB_RECOMMENDATION', 1, [
							'USER' => $u->fullname(),
							'JOB_POSTING' => $project->subject,
							'JOB_POSTING_URL' => $job_url,
							'JOB_TYPE_DESC' => $job_type,
							'JOB_POSTING_SHORT_DESC' => $job_desc,
							'BUYER' => $project->client->fullname(),
						]);
					}
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crJobRecommendation] ' . $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Update buyer total projects posted
	* @author Ro Un Nam
	* @since Aug 14, 2018
	*/
	public static function crProcessUserProjects() {
		$type = self::TYPE_PROCESS_USER_PROJECTS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crProcessUserProjects] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$users = User::where('role', User::ROLE_USER_BUYER)
						->where('status', User::STATUS_AVAILABLE)
						->get();

			foreach ( $users as $u ) {
				if ( !$u->stat ) {
					$u->stat = new UserStat;
					$u->stat->user_id = $u->id;
				}

				$u->stat->jobs_posted = $u->totalJobsPosted();
				$u->stat->save();
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crProcessUserProjects] ' . $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
	}

	/**
	* Update user points for recent activity and open jobs earning
	* @author Ro Un Nam
	* @since Sep 03, 2018
	*/
	public static function crUpdateUserPoints() {
		$type = self::TYPE_UPDATE_USER_POINTS;

		$cr = self::getCronJob($type);

		if ( !$cr ) {
			self::initCronJob($type);
		}
		
		if ( $cr->isDisabled() ) {
			Log::error('[Cronjob::crUpdateUserPoints] Disabled this cron job.');
			return false;
		}

		$b_time = time();
		$cr->updateFields(['status' => self::STATUS_PROCESSING]);

		try {
			$freelancers = User::where('role', User::ROLE_USER_FREELANCER)
								->where('status', User::STATUS_AVAILABLE)
								->get();

			if ( count($freelancers) ) {
				foreach ( $freelancers as $u ) {

					// Check recent activity
					if ( Settings::get('POINT_ACTIVITY_ENABLED') ) {
						if ( ago_days($u->stat->last_activity) > 3 ) {
							if ( $u->point->activity > 0 ) {
								$u->point->activity = 0;
							}
						} else {
							if ( !$u->point->activity ) {
								$u->point->activity = Settings::get('POINT_ACTIVITY');
							}
						}
					} else {
						$u->point->activity = 0;
					}

					// Open jobs earning
					if ( Settings::get('POINT_OPEN_JOBS_ENABLED') ) {
						$totalEarnings = Contract::leftJoin('contract_meters', 'contract_meters.contract_id', '=', 'contracts.id')
												->where('contractor_id', $u->id)
												->where('contracts.status', Contract::STATUS_OPEN)
												->sum('contract_meters.total_amount');

						if ( $totalEarnings ) {
							$u->point->open_jobs = ($totalEarnings * Settings::get('POINT_OPEN_JOBS')) / 2;
						}
					} else {
						$u->point->open_jobs = 0;
					}

					// Changed
					$u->point->save();
				}
			}
		} catch ( Exception $e ) {
			Log::error('[Cronjob::crUpdateUserPoints] ' . $e->getMessage());

			$cr->updateFields(['status' => self::STATUS_PAUSED]);

			return false;
		}

		// Get cron job run time
		$e_time = time();

		// Complete this cron job
		$cr->updateFields([
			'max_runtime' => $e_time - $b_time,
			'done_at' => date('Y-m-d H:i:s'),
			'status' => self::STATUS_COMPLETED
		]);
    }
}