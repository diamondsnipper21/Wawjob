<?php namespace iJobDesk\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

use DB;
use Auth;
use Session;

use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectInvitation;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\UserPoint;
use iJobDesk\Models\UserToken;
use iJobDesk\Models\File;
use iJobDesk\Models\Settings;

class User extends Model  implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract {

    use Authenticatable, Authorizable, CanResetPassword, Notifiable, SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'users';

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

	/**
	* The attributes that should be hidden for arrays.
	*
	* @var array
	*/
	protected $hidden = ['password', 'remember_token'];

	/**
	* The user type list.
	*
	* @var array
	*/
	public static $userTypeList = ['user_sadmin', 'user_admin', 'user_buyer', 'user_freelancer'];

	/**
	* The user status list.
	*
	* @var array
	*/
	public static $userStatusList = [0, 1, 2, 3, 4, 9];

	const AVATAR_WIDTH   = 150;
    const AVATAR_HEIGHT  = 150;

	const STATUS_NOT_AVAILABLE 	= 0;
	const STATUS_AVAILABLE 		= 1;
	const STATUS_SUSPENDED 		= 2;
	const STATUS_FINANCIAL_SUSPENDED = 4;
	const STATUS_DELETED 		= 5;

	// These are just used for only filtering
	const STATUS_LOGIN_BLOCKED 			= 10;
	const STATUS_LOGIN_ENABLED 			= 11;
	const STATUS_REQUIRE_ID_VERIFIED 	= 12;
	const STATUS_ID_VERFIED 			= 13;
	const STATUS_ID_UNVERFIED 			= 14;

	/* User Roles */
	// const ROLE_USER = 1;
	// const ROLE_USER_SADMIN = 2;
	// const ROLE_USER_ADMIN = 3;
	const ROLE_USER_FREELANCER 			= 1;
	const ROLE_USER_BUYER      			= 2;
	const ROLE_USER_BOTH 				= 3; 
	const ROLE_USER_SUPER_ADMIN 	   	= 4;
	const ROLE_USER_ADMIN 	   			= 5;
	const ROLE_USER_TICKET_MANAGER 	   	= 6;
	const ROLE_USER_SITE_MANAGER 	   	= 7;
	const ROLE_USER_SECURITY_MANAGER 	= 8;
	const ROLE_USER_FINANCIAL_MANAGER 	= 9;

	/* Closed Reason */
	const CLOSED_REASON_POOR_SERVICE = 1;
	const CLOSED_REASON_IRRESPONSIVE = 2;
	const CLOSED_REASON_COMPLICATED = 3;
	const CLOSED_REASON_POOR_FREELANCERS = 4;
	const CLOSED_REASON_OTHER = 5;	
	protected static $str_closed_reasons;

	const TOTAL_TRY_CAPTCHA = 5;
	const TOTAL_TRY_LOGINS = 10;
	const TOTAL_TRY_SECURITY_ANSWER = 5;

	// variables for getter and setter..
	protected $appends = ['roles'];

	function __construct() {
		parent::__construct();

		self::$str_closed_reasons = [
			self::CLOSED_REASON_POOR_SERVICE => trans('user.close_my_account.reason_poor_service'),
			self::CLOSED_REASON_IRRESPONSIVE => trans('user.close_my_account.reason_irresponsive'), 
			self::CLOSED_REASON_COMPLICATED => trans('user.close_my_account.reason_complicated'), 
			self::CLOSED_REASON_POOR_FREELANCERS => trans('user.close_my_account.reason_poor_freelancers'), 
			self::CLOSED_REASON_OTHER => trans('common.other'), 		
		];
	}

	public static function getOptions($cat) {
		if ($cat == 'status') {
			return [
				'Unverified' 		=> self::STATUS_NOT_AVAILABLE,
				'Active' 			=> self::STATUS_AVAILABLE,
				'Finn Susp' 		=> self::STATUS_FINANCIAL_SUSPENDED,
				'Acct Susp' 		=> self::STATUS_SUSPENDED,
				'Login Blocked' 	=> self::STATUS_LOGIN_BLOCKED,
				'Deleted' 			=> self::STATUS_DELETED
				
			];
		} else if ($cat == 'login_blocked') {
			return [
				'Yes' => 1,
				'No' => 0,
			];
		} else if ($cat == 'role') {
			return [
				'Freelancer' => self::ROLE_USER_FREELANCER,
				'Buyer' => self::ROLE_USER_BUYER,
				// 'Freelancer & Buyer' => self::ROLE_USER_BOTH
			];
		}
		return [];
	}

	public function generateToken($type = 0) {
		$token = hash_hmac('sha256', str_random(40), config('auth.password.key'));
		
		$user_token = new UserToken;
		$user_token->user_id = $this->id;
		$user_token->type = $type;
		$user_token->token = $token;
		
		if ( $user_token->save() ) {
			return $token;
		}

		return false;
	}

	/**
	* Check if the user has the specific role.
	*
	* @param mixed $roles The role list
	* @return mixed
	*/
	public function hasRole($roles) {
		if (!is_array($roles)) {
			$roles = [$roles];
		}

		return in_array($this->role, $roles);
	}

	public function getRolesAttribute() {
		return [$this->role];
	}

	/**
	* Check if the user is a admin.
	* TODO: this function will be removed in future.
	*/
	public function isAdmin() {
		return $this->hasRole(array_values(User::adminRoles()));
	}

	/**
	* Check if the user is a financial manager.
	* TODO: this function will be removed in future.
	*/
	public function isFinancial() {
		return $this->hasRole(self::ROLE_USER_FINANCIAL_MANAGER);
	}

	/**
	* Check if the user is a admin.
	* TODO: this function will be removed in future.
	*/
	public function isTicket() {
		return $this->hasRole(self::ROLE_USER_TICKET_MANAGER);
	}

	/**
	* Check if the user is a buyer.
	*
	* @return boolean
	*/
	public function isBuyer()
	{
		return $this->hasRole(self::ROLE_USER_BUYER);
	}

	/**
	* Check if the user is a freelancer.
	*
	* @return boolean
	*/
	public function isFreelancer()
	{
		return $this->hasRole(self::ROLE_USER_FREELANCER);
	}

	/**
	* Check if the user is a super admin.
	*
	* @return boolean
	*/
	public function isSuper() {
		return $this->hasRole(self::ROLE_USER_SUPER_ADMIN);
	}

	/**
	* Check if the user is suspended or not
	*
	* @return boolean
	*/
	public function isSuspended() {
		return $this->status == self::STATUS_SUSPENDED;
	}

	/**
	* Check if the user is financial suspended or not
	*
	* @return boolean
	*/
	public function isFinancialSuspended() {
		return $this->status == self::STATUS_FINANCIAL_SUSPENDED;
	}

	/**
	* Check if the user is login blocked or not
	*
	* @return boolean
	*/
	public function isLoginBlocked() {
		return $this->login_blocked == 1;
	}

	/**
	* Check if the user is verified or not
	*
	* @return boolean
	*/
	public function isIDVerified() {
		return $this->id_verified == 1;
	}

	/**
	* Check if the user is deleted or not
	*
	* @return boolean
	*/
	public function isDeleted() {
		return $this->status == self::STATUS_DELETED;
	}

	public function isNotVerified() {
		return $this->status == self::STATUS_NOT_AVAILABLE;
	}

    public function isAvailableAction($financial = false) {
    	if ( $this->isSuspended() ) {
    		return false;
    	}

    	if ( $financial && $this->isFinancialSuspended() ) {
    		return false;
    	}

    	return true;
    }

    public function isAvailableWithdraw() {
    	if ( !$this->isAvailableAction(true) ) {
    		return false;
    	}

    	if ( !$this->primaryPaymentGateway ) {
    		return false;
    	}

    	if ( $this->myBalance() <= 0 ) {
    		return false;
    	}

    	// Check the suspended contracts
    	$params = [
    		'count' => true,
    		'status' => Contract::STATUS_SUSPENDED
    	];

    	if ( $this->isBuyer() ) {
    		$params['buyer_id'] = $this->id;
    	} else {
    		$params['contractor_id'] = $this->id;
    	}

    	if ( Contract::getContracts($params) ) {
    		return false;
    	}

    	return true;
    }

    /**
     * Check whether current user is individual or company or not.
     */
    public function isCompany() {
    	return $this->is_company == 1;
    }

    public function isProfileCompleted() {
    	return $this->profile_step >= 8;
    }

	/**
	* Get the user type.
	*
	* @return mixed
	*/
	public function userType()
	{
		return $this->role;
	}

	/**
	* get role ids.
	*
	* @param mixed $roles The role id list from roles table.
	* @return mixed
	*/
	public function getRoleSlugs()
	{
		$roles = [];
		foreach($this->roles AS $role) {
			$roles[] = $role;
		}
		return $roles;
	}

	/**
	* get roles.
	*
	* @param mixed $roles The role id list from roles table.
	* @return mixed
	*/
	public function getRoleIds() {
		return $this->role;
	}

	/**
	* Add role.
	*
	* @param mixed $role The role id.
	* @return mixed
	*/
	public function syncRole($role)
	{
		$this->role = $role;
	}

	/*
	|--------------------------------------------------------------------------
	| The methods associated with extend properties of user.
	|--------------------------------------------------------------------------
	*/
	/**
	* Get the full name.
	*
	* @return mixed
	*/
	public function fullname($include_user_id = false) {
		$user = ViewUser::where('id', $this->id)
					    ->withTrashed()
					    ->first();

		$fullname = $user->fullname;

		if ($this->isCompany()) {
			$fullname = $this->company->name;
		}

		if (!$include_user_id)
			return $fullname;

		return "{$fullname}<span class=\"badge pull-right\">#{$user->id}</span>";
	}

	/**
	* Get the first name.
	*
	* @return mixed
	*/
	public function firstname() {
		return $this->contact->first_name ? $this->contact->first_name : $this->username;
	}

	/**
	* Get the full name.
	*
	* @return mixed
	*/
	public function location() {
		$user = ViewUser::find($this->id);

		return $user->location;
	}

	/**
	* Get the contact record associated with the user.
	*
	* @return mixed
	*/
	public function contact()
	{
		return $this->hasOne('iJobDesk\Models\UserContact', 'user_id');
	}

	/**
	* Get the contact record associated with the user.
	*
	* @return \iJobDesk\Models\UserCompanyContact
	*/
	public function getCompanyContactAttribute() {
        // Initialize models
        $company_contact = \iJobDesk\Models\UserCompanyContact::where('user_id', $this->id)->first();
        if (!$company_contact) {
            $company_contact = new UserCompanyContact();
            $company_contact->user_id     = $this->id;
            $company_contact->timezone_id = 0;

            $company_contact->save();
        }

        return $company_contact;
	}

	/**
	* Get the user_company record associated with the user.
	* @author KCG
	* @since June 8, 2017
	*/
	public function getCompanyAttribute() {
		// Initialize models
        $company = UserCompany::where('user_id', $this->id)->first();
        if (!$company) {
            $company = new UserCompany();
            $company->user_id   = $this->id;
            $company->name 		= '';

            $company->save();
        }

        return $company;
	}

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return [];
    }

	/**
	* Get the tokens.
	*
	* @return mixed
	*/
	public function tokens()
	{
		return $this->hasOne('iJobDesk\Models\UserToken', 'user_id');
	}

	/**
	* Get the profile record associated with the user.
	*
	* @return mixed
	*/
	public function profile()
	{
		return $this->hasOne('iJobDesk\Models\UserProfile', 'user_id');
	}

	/**
	* Get the profile record associated with the user.
	*
	* @return mixed
	*/
	public function wallet()
	{
		return $this->hasOne('iJobDesk\Models\Wallet', 'user_id');
	}

	/**
	* Get the users_stats record associated with the user.
	* @author Ro Un Nam
	* @since May 24, 2017
	*/
	public function stat() {
		return $this->hasOne('iJobDesk\Models\UserStat', 'user_id');
	}

	/**
	* Get the users_points record associated with the user.
	*/
	public function point() {
		return $this->hasOne('iJobDesk\Models\UserPoint', 'user_id');
	}

	/**
	* @author Ro Un Nam
	* @since Oct 19, 2017
	*/
	public function skillPoint() {
		return $this->hasOne('iJobDesk\Models\UserSkillPoint', 'user_id');
	}

	public function userNotificationSetting() {
		return $this->hasOne('iJobDesk\Models\UserNotificationSetting', 'user_id');	
	}

	public function userAffiliate() {
		return $this->hasOne('iJobDesk\Models\UserAffiliate', 'affiliate_id');
	}

	/**
	* Get the short user description
	* @author Ro Un Nam
	* @since May 24, 2017
	*/
	public function shortDescription() {
		if ( $this->profile ) {
			if ( mb_strlen($this->profile->desc) <= 200 ) {
				return $this->profile->desc;
			}

			return mb_substr($this->profile->desc, 0, 200, 'UTF-8') . '...';
		}

		return '';
	}

	/**
	* @author: paulz
	* @created: Apr 3, 2016
	*
	* @return boolean: Whether need to refresh total_mins and total_score (interval = 1 day)
	*/
	public function needRefreshMeter() {
		if ( !$this->stat ) {
			return true;
		}

		$m = new \DateTime($this->stat->updated_at);
		$now = new \DateTime('now');
		$diff = $m->diff($now);
		$need_refresh = ($diff->days > 7);

		return $need_refresh;
	}

	/**
	* @author Ro Un Nam
	* @since Oct 19, 2017
	*/
	public function needRefreshSkillPoint() {
		if ( !$this->skillPoint ) {
			return true;
		}

		$m = new \DateTime($this->skillPoint->updated_at);
		$now = new \DateTime('now');
		$diff = $m->diff($now);
		$need_refresh = ($diff->days > 7);

		return $need_refresh;
	}

	/**
	* @author Ro Un Nam
	* @since Oct 26, 2017
	*/
	public function needRefreshConnects() {
		if ( !$this->stat ) {
			return false;
		}

		$m = new \DateTime($this->stat->connects_reset_at);
		$now = new \DateTime('now');
		$diff = $m->diff($now);
		$need_refresh = ($diff->days > 0);

		return $need_refresh;
	}

	/*
	* Get total job posted
	*/
	public function totalJobsPosted() {
		return Project::where('client_id', $this->id)->count();
	}

	/**
	* Get total active contracts
	*/
	public function totalActiveContracts() {
		$params = [
			'status' => [
				Contract::STATUS_OPEN,
				Contract::STATUS_PAUSED,
				Contract::STATUS_SUSPENDED
			],
			'count' => true
		];

		if ( $this->isBuyer() ) {
			$params['buyer_id'] = $this->id;
		} else {
			$params['contractor_id'] = $this->id;
		}

		return Contract::getContracts($params);
	}

	public function totalClosedContracts() {
		$params = [
			'status' => Contract::STATUS_CLOSED,
			'earned' => true,
			'orderby' => 'earning',
			'count' => true
		];

		if ( $this->isBuyer() ) {
			$params['buyer_id'] = $this->id;
		} else {
			$params['contractor_id'] = $this->id;
		}

		return Contract::getContracts($params);
	}

	public function totalClosedHourlyContracts() {
		$params = [
			'type' => Contract::TYPE_HOURLY,
			'status' => Contract::STATUS_CLOSED,
			'count' => true
		];

		if ( $this->isBuyer() ) {
			$params['buyer_id'] = $this->id;
		} else {
			$params['contractor_id'] = $this->id;
		}

		return Contract::getContracts($params);
	}

	/**
	 * @only_opened if true, return opened tickets otherwise all.
	 */
	public function totalDisputedContracts($only_opened = false) {
		$query = Contract::leftJoin('tickets', 'contract_id', '=', 'contracts.id')
						 ->where('tickets.type', Ticket::TYPE_DISPUTE);


		$query->select('contracts.id')
			  ->distinct();

		if ( $this->isBuyer() ) {
			$query->where('contracts.buyer_id', $this->id);
		} else {
			$query->where('contracts.contractor_id', $this->id);
		}

		if ($only_opened)
			$query->whereNotIn('tickets.status', [Ticket::STATUS_SOLVED, Ticket::STATUS_CLOSED]);

		return $query->count();
	}

    /**
    * Total closed contracts that user should leave feedback
    */
    public function totalClosedCanLeaveFeedback() {
        if ( $this->isBuyer() ) {
            $field = 'buyer_id';
            $field_score = 'buyer_score';
        } else {
            $field = 'contractor_id';
            $field_score = 'freelancer_score';
        }

        $total = Contract::leftJoin('contract_meters', 'contracts.id', '=', 'contract_meters.contract_id')
			            ->leftJoin('contract_feedbacks', 'contracts.id', '=', 'contract_feedbacks.contract_id')
			            ->where('contracts.status', Contract::STATUS_CLOSED)
			            ->where('contracts.' . $field, $this->id)
			            ->whereNull('contract_feedbacks.' . $field_score)
			            ->whereRaw('(contract_meters.last_amount + contract_meters.this_amount + contract_meters.total_amount) > 0')
			            ->whereRaw('DATEDIFF(CURDATE(), contracts.ended_at) <= ' . Contract::DAYS_WAITING_FEEDBACK)
			            ->select('contracts.id')
			            ->count();

		return $total;
    }

    public function getHireRate() {
    	$total_projects = Project::where('client_id', $this->id)
									->where(function($query) {
										$query->where('status', Project::STATUS_CLOSED)
											->orWhere('status', Project::STATUS_CANCELLED)
											->orWhere(function($query2) {
												$query2->where('status', Project::STATUS_OPEN)
														->where('contract_limit', Project::CONTRACT_LIMIT_MORE);
											});
									})
									->select('id')
									->count();

    	$total_contracts_closed = Count(Project::leftJoin('contracts', 'projects.id', '=', 'contracts.project_id')
    									->where('projects.client_id', $this->id)
										->where(function($query) {
											$query->where('projects.status', Project::STATUS_CLOSED)
												->orWhere('projects.status', Project::STATUS_CANCELLED)
												->orWhere(function($query2) {
													$query2->where('projects.status', Project::STATUS_OPEN)
															->where('projects.contract_limit', Project::CONTRACT_LIMIT_MORE);
												});
										})
										->whereNotNull('contracts.id')
										->groupBy('projects.id')
										->select('contracts.id')
										->get());

		if ( !$total_projects ) {
			return 0;
		}

		return intval(100 * ($total_contracts_closed / $total_projects));
    }

	/**
	* @author: paulz
	* @created: Mar 31, 2016
	* @author: Ro Un Nam
	* @since Jun 15, 2017
	* @return [total, last6_months]
	*/
	public function howManyHours()
	{  
		$mins = 0;

		$total_mins = 0;
		$last6_total_mins = 0;

		// calculate total_mins & last6_total_mins again
		if ( $this->isBuyer() ) {
			$field = 'buyer_id';
		} else if ( $this->isFreelancer() ) {
			$field = 'contractor_id';
		} else {
			// for Admins, return 0 hour
			return [0, 0];
		}

		// calc total_mins from `contracts`
		$total_mins = Contract::leftJoin('contract_meters', 'contracts.id', '=', 'contract_meters.contract_id')
								->where($field, $this->id)
								->where('contracts.type', Project::TYPE_HOURLY)
								->sum('contract_meters.total_mins');

		// calc last6_total_mins from `hourly_reviews`
		$before6_on = date('Y-m-d', strtotime('-6 months'));
		$last6_total_mins = HourlyReview::where($field, $this->id)
										->where('hourly_from', '>=', $before6_on)
										->sum('hourly_mins');

		return [$total_mins, $last6_total_mins];
	}

	public function totalSuspended() {
		return ActionHistory::where('target_id', $this->id)
							->where('type', ActionHistory::TYPE_USER)
							->where('action_type', 'Suspend')
							->count();
	}

	/**
	* @author: paulz
	* @created Apr 3, 2016
	*/
	public function totalScore()
	{
		$query = Contract::leftJoin('contract_meters', 'contracts.id', '=', 'contract_meters.contract_id')
						->leftJoin('contract_feedbacks', 'contract_feedbacks.contract_id', '=', 'contracts.id');

		if ( $this->isBuyer() ) {
			// Buyer
			$query->whereNotNull('contract_feedbacks.freelancer_score')
					->where('buyer_id', $this->id)
					->selectRaw('SUM(freelancer_score * total_amount) as v1, SUM(total_amount) as v2');
		} else {
			// Freelancer
			$query->whereNotNull('contract_feedbacks.buyer_score')
					->where('contractor_id', $this->id)
					->selectRaw('SUM(buyer_score * total_amount) as v1, SUM(total_amount) as v2');
		}

		$result = $query->first();

		if ( empty($result->v2) ) {
			$score = 0;
		} else {
			if ( $result->v2 != 0 ) {
				$score = $result->v1 / $result->v2;
				$score = intval($score * 100) / 100;
			} else {
				$score = 0;
			}
		}

		return $score;
	}

	/**
	* Get the total raings
	*/
	public function totalRatings() {

        // user_points fields
        $point_fields = [
            'portrait',
            'portfolio',
            'certification',
            'employment_history',
            'education',
            'id_verified',
            'new_freelancer',
            //'job_success',
            'open_jobs',
            'last_12months',
            'life_time',
            //'score',
            'activity',
            'dispute',
        ];

        $ratings = 0;
        foreach ( $point_fields as $field ) {
            $ratings += $this->point->{$field};
        }

        return $ratings;
	}

	public function totalReviews() {
		$query = Contract::leftJoin('contract_feedbacks', 'contract_feedbacks.contract_id', '=', 'contracts.id')
						->where('contracts.status', Contract::STATUS_CLOSED);

		if ( $this->isFreelancer() ) {
			$query = $query->where('contracts.contractor_id', $this->id)
							->where('contract_feedbacks.buyer_score', '>', 0);
		} else {
			$query = $query->where('contracts.buyer_id', $this->id)
							->where('contract_feedbacks.freelancer_score', '>', 0);
		}

		return $query->count();
	}

	public function totalEarnedOpenContracts() {
		return Contract::leftJoin('contract_meters', 'contract_meters.contract_id', '=', 'contracts.id')
						->where('contractor_id', $this->id)
						->where('contracts.status', Contract::STATUS_OPEN)
						->sum('contract_meters.total_amount');
	}

	/**
	* Get the total points for the contracts
	* @author Ro Un Nam
	* @since Sep 04, 2018
	*/
	public function totalContractPoints($last_12months = true) {
		$point = 0;

		$totalClosed = Contract::leftJoin('contract_meters', 'contract_meters.contract_id', '=', 'contracts.id')
								->leftJoin('contract_feedbacks', 'contract_feedbacks.contract_id', '=', 'contracts.id')
								->where('contracts.contractor_id', $this->id)
								->where('contracts.status', Contract::STATUS_CLOSED)
								->where('contract_meters.total_amount', '>', 0);

		if ( $last_12months ) {
			$totalClosed = $totalClosed->where('contracts.ended_at', '>=', date('Y-m-d', strtotime('-1 year')));
		} else {
			$totalClosed = $totalClosed->where('contracts.ended_at', '<', date('Y-m-d', strtotime('-1 year')));
		}

		$totalClosed = $totalClosed->select([
										'contracts.id', 
										'contract_feedbacks.buyer_score', 
										'contract_meters.total_amount'
									])->get();

		if ( count($totalClosed) ) {
			$weight = Settings::get('POINT_LAST_12MONTHS');
			if ( !$last_12months ) {
				$weight = Settings::get('POINT_LIFETIME');
			}

			foreach ( $totalClosed as $c ) {
				if ( !$c->buyer_score ) {
					$score = Settings::get('POINT_SCORE_NON_REVIEW');
				} else {
					$score = $c->buyer_score;
				}

				$point += doubleval($c->total_amount) * (floatval($score) / 5) * ($this->stat->job_success / 100) * $weight * Settings::get('POINT_SCORE_PER_DOLLAR');
			}
		}

		return $point;
	}

	/**
	* Calculate total earned for freelancer.
	* @author Ro Un Nam
	* @since May 21, 2017
	*/
	public function totalEarned($months = 0) {
		$user_id = $this->id;

		$total = TransactionLocal::where(function($query) use ($user_id) {
								$query->where(function($query2) use ($user_id) {
									$query2->where('user_id', $user_id)
											->where('for', TransactionLocal::FOR_FREELANCER);
								})->orWhere(function($query2) use ($user_id) {
									$query2->where('ref_user_id', $user_id)
											->where('for', TransactionLocal::FOR_IJOBDESK);
								});
							})
							->whereIn('status', [
								TransactionLocal::STATUS_DONE,
								TransactionLocal::STATUS_AVAILABLE
							])
							->whereIn('type', [
								TransactionLocal::TYPE_FIXED,
								TransactionLocal::TYPE_HOURLY,
								TransactionLocal::TYPE_BONUS,
								TransactionLocal::TYPE_REFUND,
								TransactionLocal::TYPE_AFFILIATE,
								TransactionLocal::TYPE_AFFILIATE_CHILD,
							]);

		if ( $months == 6 ) {
			$last_6months = date('Y-m-d 00:00:00', strtotime('-6 months'));
			$total = $total->where('updated_at', '>=', $last_6months);
		} else if ( $months == 12 ) {
			$last_12months = date('Y-m-d 00:00:00', strtotime('-1 year'));
			$total = $total->where('updated_at', '>=', $last_12months);
		}

		$total = $total->sum('amount');

		if ( $total < 0 ) {
			$total = 0;
		}

		return $total;
	}

	/**
	* Calculate total spent from buyer
	* @author Ro Un Nam
	* @since Jun 16, 2017
	*/
	public function totalSpent() {
		$total = TransactionLocal::where('user_id', $this->id)
								->where('for', TransactionLocal::FOR_BUYER)
								->where('status', TransactionLocal::STATUS_DONE)
								->whereIn('type', [
									TransactionLocal::TYPE_FIXED,
									TransactionLocal::TYPE_HOURLY,
									TransactionLocal::TYPE_BONUS,
									TransactionLocal::TYPE_REFUND,
									TransactionLocal::TYPE_FEATURED_JOB,
								])
								->sum('amount');

		$total = abs($total);

		return $total;
	}

	/**
	* Calculate total paid hours from buyer
	* @author Ro Un Nam
	* @since Jun 16, 2017
	*/
	public function totalPaidHours() {
		$total = TransactionLocal::where('user_id', $this->id)
								->where('for', TransactionLocal::FOR_BUYER)
								->where('type', TransactionLocal::TYPE_HOURLY)
								->where('status', TransactionLocal::STATUS_DONE)								
								->sum('hourly_mins');

		return round($total / 60);
	}

	/**
	* Calculate total paid amount for hourly
	* @author Ro Un Nam
	* @since Jun 16, 2017
	*/
	public function totalPaidHourly() {
		$total = TransactionLocal::where('user_id', $this->id)
								->where('for', TransactionLocal::FOR_BUYER)
								->where('type', TransactionLocal::TYPE_HOURLY)
								->where('status', TransactionLocal::STATUS_DONE)								
								->sum('amount');

		return abs($total);
	}

	/**
	* Get the job success
	* @author Ro Un Nam
	* @since Jun 15, 2017
	*/
	public function getJobSuccess() {
		$totalClosed = Contract::leftJoin('contract_meters', 'contract_meters.contract_id', '=', 'contracts.id')
								->where('contracts.contractor_id', $this->id)
								->where('contracts.status', Contract::STATUS_CLOSED)
								->sum('contract_meters.total_amount');

		$totalSuccessClosed = Contract::leftJoin('contract_meters', 'contract_meters.contract_id', '=', 'contracts.id')
										->where('contracts.contractor_id', $this->id)
										->where('contracts.status', Contract::STATUS_CLOSED)
										->where('contracts.closed_reason', Contract::CLOSED_REASON_SUCCESS)
										->sum('contract_meters.total_amount');

		if ( $totalClosed <= 0 ) {
			return 0;
		}

		return round($totalSuccessClosed * 100 / $totalClosed);
	}

	/**
	* Calculate total balance
	* @author Ro Un Nam
	* @since Jun 16, 2017
	*/
	public function myBalance($exclude_holding = true) {
		if ( !$this->wallet ) {
			return 0;
		}		

		$balance = doubleval($this->wallet->amount);

		if ( !$exclude_holding ) {
			return $balance;
		}

        $balance -= $this->getTotalAmountUnderWorkAndReview();

        return $balance;
	}

	/**
	* Update the last activity
	* @author Ro Un Nam
	* @since Jun 16, 2017
	*/
	public function updateLastActivity() {
		if ( !$this->stat ) {
			return false;
		}

		$this->stat->last_activity = date('Y-m-d H:i:s');
		$this->stat->save();
	}

	/**
	* Update the user score in user_stats
	* @since Aug 19, 2018
	*/
	public function updateUserScore() {
		$this->stat->score = $this->totalScore();
        $this->stat->total_reviews = $this->totalReviews();

        if ( $this->isFreelancer() ) {
        	$this->stat->job_success = $this->getJobSuccess();

        	// $this->point->updateJobSuccess($this->stat->job_success);
        	// $this->point->updateScore($this->stat->score);
        }
        
        $this->stat->save();
	}

	/*
	 * Update the user ratings
	 */
	public function updateRatings() {
		if (!$this->isFreelancer())
			return;
		
		$this->stat->ratings = $this->totalRatings();
		$this->stat->save();
	}

	/**
	* Get the employments associated with the user.
	*
	* @return mixed
	*/
	public function employments() {
		return $this->hasMany('iJobDesk\Models\UserEmployment', 'user_id');
	}

	/**
	* Get the educations associated with the user.
	*
	* @return mixed
	*/
	public function educations()
	{
		return $this->hasMany('iJobDesk\Models\UserEducation', 'user_id');
	}

	/**
	* Get the educations associated with the user.
	*
	* @return mixed
	*/
	public function certifications()
	{
		return $this->hasMany('iJobDesk\Models\UserCertification', 'user_id');
	}

	public function experiences()
	{
		return $this->hasMany('iJobDesk\Models\UserExperience', 'user_id');
	}

	/**
	* Get the educations associated with the user.
	*
	* @return mixed
	*/
	public function portfolios()
	{
		return $this->hasMany('iJobDesk\Models\UserPortfolio', 'user_id');
	}

	/**
	* Get the skills.
	*
	* @return mixed
	*/
	public function getSkillsAttribute()
	{
		$user_skills = UserSkill::where('user_id', $this->id)
							    ->orderBy('id', 'ASC')
					  			->get();

		$skills = [];
		foreach ($user_skills as $user_skill) {
			$skill = Skill::find($user_skill->skill_id);
			if ($skill)
				$skills[] = $skill;
		}

		return collect($skills);
	}

	/**
	* Get the languages associated with the user.
	*
	* @return mixed
	*/
	public function getLanguagesAttribute() {
		$user_languages = UserLanguage::where('user_id', $this->id)
									  ->orderBy('id', 'ASC')
					     			  ->get();

		$languages = [];
		foreach ($user_languages as $user_language) {
			$languages[] = Language::find($user_language->lang_id);
		}

		return collect($languages);
	}

	public function getLanguageList()
	{
		$k = $this->languages->pluck('name')->toArray();

		return $k;
	}

	public function getTimezoneName() {
		if ( $this->contact && $this->contact->timezone ) {
			return $this->contact->timezone->name;
		}

		return date_default_timezone_get();
	}

	/**
	* Get locale.
	*
	* @return mixed
	*/
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	* Get the timezone info
	* @return array [name, offset] ['America/Los_Angeles', '-08:00']
	*/
	public function getTimezoneInfo() {
		$server_timezone_name = date_default_timezone_get();
		$user_timezone_offset = '+00:00';

		if ( $this->contact && $this->contact->timezone ) {
			$user_timezone_name = $this->contact->timezone->name;

			$gmt_offset = intval($this->contact->timezone->gmt_offset);

	        if ( $gmt_offset != 0 ) {
		        $minutes = $gmt_offset * 60;
		        $symbol = $minutes > 0 ? '+' : '-';
		        
		        $h = intval(abs($minutes) / 60);
		        $m = $minutes % 60;

		        $user_timezone_offset = $symbol . sprintf("%02d:%02d", $h, $m);
		    }
		} else {
			$user_timezone_name = $server_timezone_name;
		}

		return [$user_timezone_name, $user_timezone_offset];
	}

	/**
	 * Get count of users by status
	 */
	public static function getCountByStatus($status) {
		return User::where('status', $status)->count();
	}

	/**
	 * Get count of users by region
	 */
	public static function getCountByRegion($role) {
		$users = User::select('user_contacts.country_code AS code', DB::raw('COUNT(users.id) as nums'))
					 ->join('user_contacts', 'user_id', '=', 'users.id')
					 ->groupBy('user_contacts.country_code');

		if (!empty($role)) {
			$users = $users->where('role', '=', $role);
		}

		$users = $users->whereNotInAdmin();

		$data = [];
		foreach ($users->get() as $user) {
			$data[strtolower($user->code)] = $user->nums;
		}

		return $data;
	}

	public function closed_reason_string() {
		if ( isset(self::$str_closed_reasons[$this->closed_reason]) ) {
			return self::$str_closed_reasons[$this->closed_reason];
		}

		return trans('common.other');
	}

	public static function getByUsername($username) {
		return User::where('username', $username)
					->where('status', self::STATUS_AVAILABLE)
					->first();
	}

	/**
	* Check if the freelancer is stored as saved by buyer
	* @author Ro Un Nam
	* @since May 21, 2017
	*/
	public function isSaved() {
		$user = Auth::user();

		return $this->hasOne('iJobDesk\Models\ProfileViewHistory', 'user_id')
					->where('buyer_id', $user->id);
	}

	/**
	* @author Ro Un Nam
	* @since Jun 08, 2017
	*/
	public function paymentGateways() {
		return $this->hasMany('iJobDesk\Models\UserPaymentGateway', 'user_id')
					->whereIn('user_payment_gateways.status', [
						UserPaymentGateway::IS_STATUS_YES,
						UserPaymentGateway::IS_STATUS_EXPIRED,
					])
					->orderBy('user_payment_gateways.created_at', 'asc');
	}

	/**
	* @author Ro Un Nam
	* @since Jun 08, 2017
	*/
	public function activePaymentGateways() {
		return $this->hasMany('iJobDesk\Models\UserPaymentGateway', 'user_id')
					->where('user_payment_gateways.status', UserPaymentGateway::IS_STATUS_YES)
					->where('user_payment_gateways.is_pending', UserPaymentGateway::IS_PENDING_NO)
					->orderBy('user_payment_gateways.created_at', 'asc');
	}

	/**
	* @author Ro Un Nam
	* @since Aug 13, 2018
	*/
	public function totalActiveWithdrawPaymentGateways() {
		return UserPaymentGateway::leftJoin('payment_gateways', 'user_payment_gateways.gateway', '=', 'payment_gateways.id')
								->where('user_payment_gateways.user_id', $this->id)
								->where('user_payment_gateways.status', UserPaymentGateway::IS_STATUS_YES)
								->where('user_payment_gateways.is_pending', UserPaymentGateway::IS_PENDING_NO)
								->where('payment_gateways.enable_withdraw', 1)
								->count();
	}

	/**
	* @author Ro Un Nam
	* @since Aug 13, 2018
	*/
	public function activePaymentGatewaysOrderWithdraw() {
		return UserPaymentGateway::leftJoin('payment_gateways', 'user_payment_gateways.gateway', '=', 'payment_gateways.id')
								->where('user_payment_gateways.user_id', $this->id)
								->where('user_payment_gateways.status', UserPaymentGateway::IS_STATUS_YES)
								->where('user_payment_gateways.is_pending', UserPaymentGateway::IS_PENDING_NO)
								->where('payment_gateways.enable_withdraw', 1)
								->select([
									'user_payment_gateways.*',
								])
								->orderBy('payment_gateways.enable_withdraw', 'desc')
								->orderBy('user_payment_gateways.created_at', 'asc')
								->get();
	}

	/**
	* @author Ro Un Nam
	* @since Jun 08, 2017
	*/
	public function depositPaymentGateways() {
		return $this->hasMany('iJobDesk\Models\UserPaymentGateway', 'user_id')
					->leftJoin('payment_gateways', 'user_payment_gateways.gateway', '=', 'payment_gateways.id')
					->where('user_payment_gateways.status', UserPaymentGateway::IS_STATUS_YES)
					->where('payment_gateways.enable_deposit', 1)
					->select([
						'user_payment_gateways.*',
					])
					->orderBy('user_payment_gateways.created_at', 'asc');
	}

	/**
	* @author Ro Un Nam
	* @since Jan 18, 2018
	*/
	public function primaryPaymentGateway() {
        return $this->hasOne('iJobDesk\Models\UserPaymentGateway', 'user_id')
					->where('is_primary', UserPaymentGateway::IS_PRIMARY_YES)
					->where('is_pending', UserPaymentGateway::IS_PENDING_NO)
					->where('status', UserPaymentGateway::IS_STATUS_YES)
					->select([
						'user_payment_gateways.id', 
						'user_payment_gateways.gateway', 
						'user_payment_gateways.data', 
						'user_payment_gateways.params',
						'user_payment_gateways.is_primary',
					]);
	}

	/**
	* @author Ro Un Nam
	*/
	public function depositPrimaryPaymentGateway() {
        return $this->hasOne('iJobDesk\Models\UserPaymentGateway', 'user_id')
					->where('is_primary', UserPaymentGateway::IS_PRIMARY_YES)
					->where('status', UserPaymentGateway::IS_STATUS_YES)
					->select([
						'user_payment_gateways.id', 
						'user_payment_gateways.gateway', 
						'user_payment_gateways.data', 
						'user_payment_gateways.params',
						'user_payment_gateways.is_primary',
					]);
	}

	/*
	* Check if current logged in buyer can see the freelancer's earning
	*/
	public function canSeeUserEarning($user) {
		$application = ProjectApplication::leftJoin('projects', 'project_applications.project_id', '=', 'projects.id')
										->where('projects.client_id', $this->id)
									 	 ->where('project_applications.user_id', $user->id)
									 	 ->where('project_applications.status', '<>', ProjectApplication::STATUS_HIRING_CLOSED)
									 	 ->first();

		if ( $application ) {
			return true;
		}

		return false;
	}

	/**
	* Check if the freelancer has been invited from the buyer
	* @author Ro Un Nam
	* @since Dec 27, 2017
	*/
	public function hasInvited($job_id) {
		$invited = ProjectInvitation::where('receiver_id', $this->id)
									->where('project_id', $job_id)
									->first();
		if ( $invited ) {
			return [
				'created_at' => $invited->created_at,
				'updated_at' => $invited->updated_at,
				'status' => $invited->status,
				'message' => $invited->message,
				'reason' => $invited->answer,
			];
		}

		return false;
	}

	/**
	* Check if the freelancer has been hiring
	* @author Ro Un Nam
	* @since Feb 05, 2018
	*/
	public function hasHiring($job_id) {
		$hiring = Contract::where('project_id', $job_id)
							->where('contractor_id', $this->id)
							->whereIn('status', [
								Contract::STATUS_OPEN,
								Contract::STATUS_PAUSED,
								Contract::STATUS_SUSPENDED,								
							])
							->first();
		if ( $hiring ) {
			return true;
		}

		return false;
	}

	/**
	* Get the changed milestones of the current freelancer
	* @author Ro Un Nam
	*/
	public function changedContractMilestones() {
		$user = Auth::user();

		if ( !$user ) {
			return null;
		}
		
		return Contract::where('contractor_id', $user->id)
					   ->where('type', Contract::TYPE_FIXED)
					   ->where('milestone_changed', Contract::MILESTONE_CHANGED_YES)
					   ->select([
						   'contracts.id',
						   'contracts.title'
					   ])
					   ->whereIn('status', [Contract::STATUS_OPEN])
					   ->orderBy('updated_at', 'desc')
					   ->get();
	}

	/**
	* Get the offers received
	* @author Ro Un Nam
	*/
	public function offers() {
		return Contract::leftJoin('user_notifications', 'user_notifications.id', '=', 'contracts.notification_id')
						->where('contracts.contractor_id', $this->id)
						->where('contracts.status', Contract::STATUS_OFFER)
						->whereNull('user_notifications.read_at')
						->select([
							'contracts.id',
							'contracts.title',
							'contracts.notification_id'
						])
						->orderBy('contracts.updated_at', 'desc')
						->get();
	}

	/**
	* Fetch the list of admininistrators having keyword in their full name, username or email
	*
	* @author paulz
	* @create Mar 7, 2015
	*/
	public static function getAdmins($keyword = '')
	{
		if ($keyword) {
			$strKeyword = " AND (user_contacts.`first_name` LIKE '%".$keyword."%' OR user_contacts.`last_name` LIKE '%".$keyword."%')";
		} else {
			$strKeyword = '';
		}

		$users = DB::table("users_roles")
					->leftJoin('user_contacts', 'users_roles.user_id', '=', 'user_contacts.user_id')
					->whereRaw("users_roles.role_id = '".self::ROLE_USER_ADMIN."'".$strKeyword)
					->get();

		return $users;
	}

	/**
	* Search freelancers
	* @author Ro Un Nam
	* @since May 19, 2017
	*/
	public static function searchFreelancers($params = [], $per_page = 10, $page = '', $job_id = 0) {
		$user = Auth::user();

		try {

			$orders = [1];

			if ( $params ) {

				$users = self::leftJoin('user_contacts', 'users.id', '=', 'user_contacts.user_id')
							 ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
							 ->leftJoin('user_stats', 'users.id', '=', 'user_stats.user_id')
							 ->where('users.role', self::ROLE_USER_FREELANCER)
							 ->where('users.status', self::STATUS_AVAILABLE);

				// Filtering by keyword
				if ( $params['keyword'] ) {
					$searched_keyword = '%' . strtolower(trim($params['keyword'])) . '%';
					$filters_by_keyword = [
						'LOWER(username) = "' . $searched_keyword . '"',
						'LOWER(CONCAT_WS(" ", first_name, last_name)) LIKE "' . $searched_keyword . '"',
						'LOWER(user_profiles.title) LIKE "' . $searched_keyword . '"',
						'LOWER(skills.name) LIKE "' . $searched_keyword . '"',
						'LOWER(user_profiles.desc) LIKE "' . $searched_keyword . '"'
					];
				
					$users->leftJoin('user_skills', 'users.id', '=', 'user_skills.user_id')
						  ->leftJoin('skills', 'user_skills.skill_id', '=', 'skills.id')
						  ->where(function ($query) use ($filters_by_keyword) {
								foreach ($filters_by_keyword as $filter) {
									$query->orWhereRaw($filter);
								}
						});

					$orders = [];
					foreach ($filters_by_keyword as $i => $filter) {
						$users->addSelect(DB::raw("IF($filter, 50000-$i, 0) AS order$i"));
						$orders[] = "order$i";
					}
				}

				// Filtering by title
				if ( $params['title'] ) {
					$users->where('user_profiles.title', 'LIKE',  '%' . $params['title'] . '%');
				}

				// Filtering by hourly rate
				if ( $params['hourly_rate'] ) {
					switch ($params['hourly_rate']) {
						case 1:
							$users->where('user_profiles.rate', '<=', 10);
							break;

						case 2:
							$users->where('user_profiles.rate', '>', 10)
								  ->where('user_profiles.rate', '<=', 30);
							break;

						case 3:
							$users->where('user_profiles.rate', '>', 30)
								  ->where('user_profiles.rate', '<=', 60);
							break;

						case 4:
							$users->where('user_profiles.rate', '>', 60);
							break;
						
						default:
							break;
					}
				}

				// Filtering by english level
				if ( $params['english_level'] ) {
					$users->where('user_profiles.en_level', $params['english_level']);
				}

				// Filtering by languages
				if ( $params['languages'] ) {
					$languages_users = UserLanguage::whereIn('lang_id', explode(',', $params['languages']))->get();

					$languages_user_ids = [];
					if ( count($languages_users) ) {
						foreach ( $languages_users as $lang_user ) {
							$languages_user_ids[] = $lang_user->user_id;
						}
					}

					$users->whereIn('users.id', $languages_user_ids);
				}

				// Filtering by locations
				if ( $params['locations'] ) {
					$users->whereIn('country_code', explode(',', $params['locations']));
				}

				// Filtering by users_stats
				if ( $params['hours_billed'] || $params['job_success'] || $params['feedback'] || $params['activity'] ) {

					if ( $params['hours_billed'] ) {
						$users->where('hours', '>=', $params['hours_billed']);
					}
					if ( $params['job_success'] ) {
						$users->where('job_success', '>=', $params['job_success']);
					}
					if ( $params['feedback'] ) {
						$users->where('score', '>=', $params['feedback']);
					}
					if ( $params['activity'] ) {
						switch ($params['activity']) {
							case '2w':
								$users = $users->where('last_activity', '>=', date('Y-m-d', strtotime('-2 weeks')));
								break;

							case '1m':
								$users = $users->where('last_activity', '>=', date('Y-m-d', strtotime('-1 month')));
								break;

							case '2m':
								$users = $users->where('last_activity', '>=', date('Y-m-d', strtotime('-2 months')));
								break;

							default:
								break;
						}
					}
				}

			} else {
				$users = self::leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
						     ->leftJoin('user_stats', 'users.id', '=', 'user_stats.user_id')
							 ->where('users.role', self::ROLE_USER_FREELANCER)
							 ->where('users.status', self::STATUS_AVAILABLE);

			}

			$users->addSelect('users.*');

			if ( $user && $page != '') {
				$users->where('users.id', '<>', $user->id);
			}

			// Get the past hires
			if ( $page == 'past' ) {
				$past_hires_user_ids = Contract::where('buyer_id', $user->id)
												->select('contractor_id')
												->distinct()
												->pluck('contractor_id')
												->toArray();

				$users->whereIn('users.id', $past_hires_user_ids);
			// Get the saved freelancers
			} else if ( $page == 'saved' ) {
				$saved_user_ids = ProfileViewHistory::where('buyer_id', $user->id)
											->select('user_id')
											->distinct()
											->pluck('user_id')
											->toArray();

				$users = $users->whereIn('users.id', $saved_user_ids);
			// Get the invited freelancers
			} else if ( $page == 'invited' ) {
				$invited_user_ids = ProjectInvitation::where('project_id', $job_id)
											->select('receiver_id')
											->distinct()
											->pluck('receiver_id')
											->toArray();

				$users->whereIn('users.id', $invited_user_ids);
			}

			// Check the keyword is in searched skills array
			if ( $params && in_array($params['keyword'], Skill::USER_POINT_SKILLS) ) {
				$skill = strtolower($params['keyword']);
				$users = $users->leftJoin('user_skill_points', 'users.id', '=', 'user_skill_points.user_id')
							   ->orderBy('user_skill_points.c_' . $skill, 'desc');
			}

			$users->where('user_profiles.rate', '>', 0)
			      ->where('users.role', self::ROLE_USER_FREELANCER)
				  ->orderBy(DB::raw('(' . implode('+', $orders) . '+user_stats.ratings)'), 'DESC')
				  ->groupBy('users.id');

			// Don't show users who doesn't complete profile setup when searching user.
			$users->where('users.profile_step', '>=', 3);

			// Check visibility
			if (!$user)
				$users->whereIn('share', [0]); // for public
			else {
				if ($user->isBuyer()) {
					$users->leftJoin('project_applications AS pa', 'pa.user_id', '=', 'users.id')
						  ->leftJoin('projects AS p', 'p.id', '=', 'pa.project_id')
						  ->where(function($query) use ($user) {
						  		$query->where('user_profiles.share', '<>', 2)
						  			  ->orWhere(function($query) use ($user) {
						  			  		$query->where('user_profiles.share', 2)
						  			  			  ->where('p.client_id', $user->id);
						  			  });
						  });
				} else {
					$users->whereIn('share', [0, 1]); // for public
				}
			}

			$users = $users->paginate($per_page);

		} catch (Exception $e) {
			return false; 
		}

		return $users;
	}

	/**
	 * @author KCG
	 * @since June 23, 2017
	 * Force Login
	 */
	public static function forceLogin($user_id) {
		Auth::logout();
		Session::forget('user_secured');
		Auth::loginUsingId($user_id);
	}

	/**
	 * @author KCG
	 * @since June 29, 2017
	 * List of admin users.
	 */
	public static function getAdminUsers($roles = null) {
		$me = Auth::user();

		if (empty($roles)) {
			$roles = [
				self::ROLE_USER_SUPER_ADMIN, 
				self::ROLE_USER_ADMIN, 
				self::ROLE_USER_TICKET_MANAGER, 
				self::ROLE_USER_SITE_MANAGER, 
				self::ROLE_USER_SECURITY_MANAGER
			];
		}

		$users = User::whereIn('role', $roles)
					 ->where('status', User::STATUS_AVAILABLE)
					 ->where(function($query) use ($me) {
					 	if ($me)
					 		$query->orderByRaw("IF(id = $me->id, 1, 0) DESC");
					 })
					 ->orderBy('role', 'DESC')
					 ->get();

		return $users;
	}

	/**
	 * Get count of users by roles
	 */
	public static function getCountByRoles($roles = null, $excluded_status = null, $included_status = null, $include_admins = false) {
		// $users = User::withTrashed();
		$users = User::whereRaw(true);

		if ($roles != null)
			$users->whereIn('role', $roles);

		if ($excluded_status)
			$users->whereNotIn('status', $excluded_status);

		if ($included_status)
			$users->whereIn('status', $included_status);

		// if ($include_admins) {
		// 	$users->whereNotInAdmin();
		// }

		return $users->count();
	}

	public function getAffiliatedUsers() {
		$users = [];

        // Get primary
        $primary = UserAffiliate::where('user_id', $this->id)
		                        ->where('affiliate_id', '<>', 0)
		                        ->get();

		foreach ( $primary as $u ) {
			if ( $u->affiliatedUser ) {
				$users[] = [
					'user' => $u->affiliatedUser,
					'type' => '1st',
					'total' => $this->getTotalAffliatedAmountFromUser($u->affiliate_id),
				];

				// Get secondary
				$secondary = UserAffiliate::where('user_id', $u->affiliate_id)
				                        	->where('affiliate_id', '<>', 0)
				                        	->get();

				foreach ( $secondary as $u_s ) {
					if ( $u_s->affiliatedUser ) {
						$users[] = [
							'user' => $u_s->affiliatedUser,
							'type' => '2nd',
							'total' => $this->getTotalAffliatedAmountFromUser($u_s->affiliate_id),
						];
					}
				}
			}
		}

		return $users;
	}

	public function getTotalAffliatedAmountFromUser($user_id) {
		return TransactionLocal::where('user_id', $this->id)
								->where('ref_user_id', $user_id)
								->whereIn('type', [
									TransactionLocal::TYPE_AFFILIATE,
									TransactionLocal::TYPE_AFFILIATE_CHILD
								])
								->where('status', TransactionLocal::STATUS_DONE)
								->sum('amount');
	}

	public function getTotalAffiliated($type = false) {
        $total = UserAffiliate::leftJoin('users', 'affiliate_id', '=', 'users.id')
		                        ->where('user_id', $this->id);

		if ( $type ) {
			$total = $total->where('users.role', $type);
		} else {
			$total = $total->where('affiliate_id', '<>', 0);
		}
		 
		return $total->count();
	}

	public function getTotalSecondaryAffiliated($type = false) {
		$total = 0;

		$affiliates = UserAffiliate::where('user_id', $this->id)
									->where('affiliate_id', '<>', 0)
			                        ->pluck('affiliate_id')
									->toArray();

		foreach ( $affiliates as $id ) {
			$child = UserAffiliate::leftJoin('users', 'affiliate_id', '=', 'users.id')
			                        ->where('user_id', $id);

			if ( $type ) {
			    $child = $child->where('users.role', $type);
			} else {
				$child = $child->where('affiliate_id', '<>', 0);
			}

			$total += $child->count();
		}

		return $total;
	}

	public function getTotalAffiliatesSent() {
        return UserAffiliate::where('user_id', $this->id)
                            ->select('email')
                            ->count();
	}

	public function getTotalAffiliatesAccepted() {
		return UserAffiliate::where('user_id', $this->id)
                            ->where('affiliate_id', '<>', 0)
                            ->count();
	}

	public function getTotalAffiliatesAmount($params = []) {
		$total = TransactionLocal::where('user_id', $this->id)
							->whereIn('type', [
								TransactionLocal::TYPE_AFFILIATE,
								TransactionLocal::TYPE_AFFILIATE_CHILD
							]);

		if ( isset($params['status']) ) {
			$total->whereIn('status', $params['status']);
		}

		if ( isset($params['created_at']) ) {
			$total->where('created_at', '>=', $params['created_at']);
		}

		return $total->sum('amount');
	}

	public function getTotalAffiliatesUsers($params = []) {
		$total = TransactionLocal::where('user_id', $this->id)
							->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
							->whereIn('type', [
								TransactionLocal::TYPE_AFFILIATE,
								TransactionLocal::TYPE_AFFILIATE_CHILD
							]);

		if ( isset($params['status']) ) {
			$total->where('status', $params['status']);
		}

		if ( isset($params['created_at']) ) {
			$total->where('created_at', '>=', $params['created_at']);
		}

		return count(array_unique($total->pluck('ref_user_id')->toArray()));
	}

	/**
	* If user is buyer, get the total amount under work and review for the hourly contracts
	*/
	public function getTotalAmountUnderWorkAndReview() {
		if ( $this->isFreelancer() ) {
			return 0;
		}

		$totalAmountUnderWork = HourlyLog::getUserTotalAmount($this->id);

		return doubleval($totalAmountUnderWork);
	}

	/**
	* Get the total withdrawal amount under pending
	*/
	public function getTotalWithdrawalAmountUnderPending() {
		$total = TransactionLocal::where('user_id', $this->id)
								->where('type', TransactionLocal::TYPE_WITHDRAWAL)
								->whereIn('status', [
									TransactionLocal::STATUS_PENDING,
									TransactionLocal::STATUS_AVAILABLE,
									TransactionLocal::STATUS_REVIEW,
								])
								->sum('amount');

		return abs($total);
	}

    /**
     * @author KCG
     * @since July 11, 2017
     * Returns query for location
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNotInAdmin($query)
    {
        return $query->whereNotIn('role', [
				User::ROLE_USER_SUPER_ADMIN,
				User::ROLE_USER_ADMIN, 
				User::ROLE_USER_FINANCIAL_MANAGER, 
				User::ROLE_USER_TICKET_MANAGER, 
				User::ROLE_USER_SITE_MANAGER, 
				User::ROLE_USER_SECURITY_MANAGER
		]);
    }

    /**
     * @author KCG
     * @since July 10, 2017
     * Colors by each status. This data will be used graphs and buttons
     */
    public static function colorsByStatus() {
        return [
            self::STATUS_NOT_AVAILABLE => 'default',
            self::STATUS_AVAILABLE => 'primary',
            self::STATUS_SUSPENDED => 'danger',
            self::STATUS_FINANCIAL_SUSPENDED => 'warning',
            self::STATUS_LOGIN_BLOCKED => 'warning',
            self::STATUS_DELETED => 'deleted',
        ];
    }

    public function colorByStatus() {
        return self::colorsByStatus()[$this->status];
    }

    public function stringByStatus() {
    	return array_search($this->status, self::getOptions('status'));
    }

    public static function adminStatus() {
    	return [
    		self::STATUS_AVAILABLE 	=> 'Active',
    		self::STATUS_SUSPENDED 	=> 'Suspended',
    		self::STATUS_DELETED 	=> 'Deleted'
    	];
    }

    public static function adminType() {
    	return [
			User::ROLE_USER_FINANCIAL_MANAGER	=> 'Financial Manager',
			User::ROLE_USER_TICKET_MANAGER		=> 'Ticket Manager',
			// User::ROLE_USER_SECURITY_MANAGER	=> 'Security Manager', 
			// User::ROLE_USER_SITE_MANAGER		=> 'Site Manager', 
			User::ROLE_USER_SUPER_ADMIN 		=> 'Super Admin'
    	];
    }

    public function firstContract() {
    	return Contract::where('contractor_id', $this->id)->orderBy('created_at', 'asc')->first();
    }

	public static function enableStatusChanged($user) {
		$attributes = '';

		if ($user->status == self::STATUS_NOT_AVAILABLE) {
			$attributes .= ' data-status-' . self::STATUS_AVAILABLE . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
		} elseif ($user->status == self::STATUS_AVAILABLE) {
			$attributes .= ' data-status-' . self::STATUS_NOT_AVAILABLE . '=true';
			$attributes .= ' data-status-' . self::STATUS_FINANCIAL_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_LOGIN_BLOCKED . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';

		} elseif ($user->status == self::STATUS_SUSPENDED) {
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
			if (!Ticket::hasIDVerification($user))
				$attributes .= ' data-status-' . self::STATUS_AVAILABLE . '=true';
		} elseif ($user->status == self::STATUS_FINANCIAL_SUSPENDED) {
			$attributes .= ' data-status-' . self::STATUS_AVAILABLE . '=true';
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
		}

		if ($user->login_blocked) {
			$attributes .= ' data-status-' . self::STATUS_LOGIN_ENABLED . '=true';
		}

		if (!$user->id_verified && !Ticket::hasIDVerification($user)) {
			$attributes .= ' data-status-' . self::STATUS_REQUIRE_ID_VERIFIED . '=true';
		}

		return $attributes;
	}

	public function enableAdminStatusChanged() {
		$attributes = '';

		if ($this->status == self::STATUS_SUSPENDED) {
			$attributes .= ' data-status-' . self::STATUS_AVAILABLE . '=true';
			$attributes .= ' data-status-delete=true';
		} elseif ($this->status == self::STATUS_AVAILABLE) {
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
			$attributes .= ' data-status-delete=true';
		} elseif ($this->status == self::STATUS_DELETED) {
		} 

		return $attributes;
	}

	public static function getSuperAdmins() {
		return User::getAdminUsers([
                    	self::ROLE_USER_SUPER_ADMIN
                    ]);
	}

	public static function adminRoles() {
		return [
			'Super Admin' 		=> self::ROLE_USER_SUPER_ADMIN,
			'Financial Manager' => self::ROLE_USER_FINANCIAL_MANAGER,
			'Ticket Manager' 	=> self::ROLE_USER_TICKET_MANAGER,
			// 'Site Manager' 		=> self::ROLE_USER_SITE_MANAGER,
			// 'Security Manager' 	=> self::ROLE_USER_SECURITY_MANAGER
		];
	}

	public static function adminRoleIcons() {
		return [
			self::ROLE_USER_SUPER_ADMIN => 'fa fa-trophy',
			self::ROLE_USER_FINANCIAL_MANAGER => 'fa fa-usd',
			self::ROLE_USER_TICKET_MANAGER => 'fa fa-crosshairs',
			self::ROLE_USER_SITE_MANAGER => 'fa fa-child',
			self::ROLE_USER_SECURITY_MANAGER => 'fa fa-creative-commons'
		];
	}

	public function role_css_class() {
		switch ($this->role) {
			case self::ROLE_USER_SUPER_ADMIN:
				return 'badge-super';
				break;
			case self::ROLE_USER_FINANCIAL_MANAGER:
				return 'badge-ticket';
				break;
			case self::ROLE_USER_TICKET_MANAGER:
				return 'badge-ticket';
				break;
			case self::ROLE_USER_BUYER:
				return 'badge-buyer';
				break;
			case self::ROLE_USER_FREELANCER:
				return 'badge-freelancer';
				break;
			default:
				break;
		}

		return '';
	}

	public function role_name() {
		switch ($this->role) {
			case self::ROLE_USER_SUPER_ADMIN:
				return 'Super Administrator';
				break;
			case self::ROLE_USER_FINANCIAL_MANAGER:
				return 'Financial Manager';
				break;
			case self::ROLE_USER_TICKET_MANAGER:
				return 'Ticket Manager';
				break;
			case self::ROLE_USER_BUYER:
				return 'Buyer';
				break;
			case self::ROLE_USER_FREELANCER:
				return 'Freelancer';
				break;
			default:
				break;
		}

		return '';
	}

	public function role_identifier() {
		if ($this->isBuyer())
			return 'buyer';
		elseif ($this->isFreelancer())
			return 'freelancer';

		$role = $this->role;
        $role_identifier = strtolower(explode(' ', array_search($role, self::adminRoles()))[0]);

        return $role_identifier;
	}

	public function role_short_name() {
		return substr($this->role_name(), 0, 1);
	}

	public function getUserNameWithIcon() {
		$user = Auth::user();

		$name = $this->fullname();

		if ($user->isAdmin())
			$name = '<span class="badge ' . $this->role_css_class() . ' user-role"  data-toggle="tooltip" title="' . $this->role_name() . '">' . $this->role_short_name() . '</span>&nbsp;' . $name;

		return $name;
	}

	/**
	 * Check whether this user can delete.
	 */
	public function canDelete() {
		return !$this->trashed() && $this->totalActiveContracts() == 0 && $this->myBalance() == 0;
	}

    /**
     * Check if user had ignored the warning of "type"
     * @param $type - Suspend, Financial Suspend, Leave feedback
     * @param $target_id The contract id or ...
     */
    public function isIgnoredWarning($type, $target_id = null) {
        return UserIgnoredWarning::where('type', $type)
			                     ->where('user_id', $this->id)
			                     ->where('target_id', $target_id)
			                     ->exists();
    }

    /**
     * Ignore the warning of "type"
     * @param $type - Suspend, Financial Suspend, Leave feedback
     * @param $target_id The contract id or ...
     */
    public function ignoreWarning($type, $target_id = null) {
    	$this->removeIgnoredWarnings($type, $target_id);

    	$ignore_warning = new UserIgnoredWarning();

    	$ignore_warning->type 		= $type;
    	$ignore_warning->user_id 	= $this->id;
    	$ignore_warning->target_id 	= $target_id;

    	$ignore_warning->save();

    	return true;
    }

    /**
     * Remove ignored warning of "type"
     * @param $type - Suspend, Financial Suspend, Leave feedback
     * @param $target_id The contract id or ...
     */
    public function removeIgnoredWarnings($type, $target_id = null) {
    	UserIgnoredWarning::where('type', $type)
	                      ->where('user_id', $this->id)
	                      ->where('target_id', $target_id)
	                      ->delete();

    	return true;
    }

    /**
     * Check if user has avatar image or not...
     */
    public function existAvatar() {
    	$file = File::getAvatar($this->id);

    	return !empty($file);
    }

    /**
     * Require ID Verification
     */
    public function requireIDVerification() {
    	if ($this->id_verified == 1)
    		return false;

    	// Check if user has any contracts
    	if ( $this->isFreelancer() ) {
    		$field = 'contractor_id';
    	} else {
    		$field = 'buyer_id';
    	}

    	if ( !Contract::where($field, $this->id)->count() ) {
    		return false;
    	}

        $_POST['_reason'] = 'Require Identity Verification';

    	$this->id_verified = 0;
        $this->is_auto_suspended = 1;
        $this->status = User::STATUS_SUSPENDED;
        $this->save();

        $ticket = new Ticket();
        $ticket->subject = trans('ticket.id_verification.ticket_title') . ": " . $this->fullname() . " - " . $this->username;

        if ( $this->isCompany() ) {
        	$ticket->content = trans('ticket.id_verification.ticket_content_company');
        } else {
        	$ticket->content = trans('ticket.id_verification.ticket_content');
        }
        
        $ticket->user_id = $this->id;
        $ticket->type    = Ticket::TYPE_ID_VERIFICATION;
        $ticket->priority= Ticket::PRIORITY_HIGH;
        $ticket->save();

        EmailTemplate::send($this, 
            'ID_VERIFICATION_START', 
            0,
            [
                'USER'              => $this->fullname(),
                'VERIFICATION_URL'  => _route('ticket.detail', ['id' => $ticket->id], true, null, $this)
            ]
        );
    }

    /**
     * Retrieve reason for suspended user.
     */
    public function suspendedReason() {
    	$last_action = $this->lastSuspensionAction();
    	if (!$last_action)
    		return '';

    	return $last_action->reason;
    }

    /**
     * Retrieve last suspension action
     */
    public function lastSuspensionAction() {
    	if (!$this->isSuspended() && !$this->isFinancialSuspended())
    		return null;

    	$last_action = ActionHistory::where('type', ActionHistory::TYPE_USER)
    								->where('target_id', $this->id)
    								->whereIn('action_type', ['Suspend', 'Suspend Financial'])
    								->orderBy('created_at', 'DESC')
    								->first();

    	return $last_action;
    }

    public function link() {
    	return '<a href="' . _route('user.profile', ['uid' => $this->id]) . '" target="_blank">' . $this->fullname() . '</a>';
    }
} 