<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use iJobDesk\Models\Notification;
use iJobDesk\Models\UserSavedProject;

use Auth;

class Project extends Model {

	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'projects';

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

	// Summary length of Job description
	const SUMMARY_MAX_LENGTH = 200;
	const DESCRIPTION_MAX_LENGTH = 5000;
	const VIEWABLE_TEXT_LENGTH = 100;

	const TERM_NOT_SURE = 0;
	const TERM_ONE_TIME = 1;
	const TERM_ONGOING = 2;
	protected static $str_project_term;

	const TYPE_FIXED = 0;
	const TYPE_HOURLY = 1;
	public static $str_project_type;

	const STATUS_PRIVATE = 0;
	const STATUS_PUBLIC = 1;
	const STATUS_PROTECTED = 2;

	protected static $str_project_is_public = [
		self::STATUS_PUBLIC   => 'public',
		self::STATUS_PRIVATE  => 'private', 
		self::STATUS_PROTECTED  => 'protected', 
	];

	const STATUS_NOT_FEATURED = 0;
	const STATUS_FEATURED = 1;
	protected static $str_project_is_featured = [
		self::STATUS_NOT_FEATURED => 'not_featured',
		self::STATUS_FEATURED => 'yes_featured', 
	];

	const STATUS_CLOSED     = 0;
	const STATUS_OPEN       = 1;
	const STATUS_CANCELLED  = 2;
	const STATUS_DRAFT      = 3;
	const STATUS_DELETED    = 4; // Not used in database
	const STATUS_SUSPENDED  = 5;

	protected static $str_project_status = [
		self::STATUS_OPEN => 'open',
		self::STATUS_CLOSED => 'closed', 
		self::STATUS_CANCELLED => 'cancelled', 
		self::STATUS_DRAFT => 'draft', 
		self::STATUS_DELETED => 'deleted',
		self::STATUS_SUSPENDED => 'suspended',
	];

	const COVER_LETTER_YES = 1;
	const COVER_LETTER_NO  = 0;

	protected static $str_project_cover_letter = [
		self::COVER_LETTER_YES => 'Yes, required',
		self::COVER_LETTER_NO => 'No, not required',
	];

	const EXPERIENCE_LEVEL_ENTRY = 0;
	const EXPERIENCE_LEVEL_INTERMEDIATE = 1;
	const EXPERIENCE_LEVEL_EXPERT = 2;
	public static $str_project_level;

	const RATE_NOT_SURE = 0;
	const RATE_BELOW_10 = 1;
	const RATE_10_30 = 2;
	const RATE_30_60 = 3;
	const RATE_ABOVE_60 = 4;
	public static $str_project_rate;

	const DUR_MT6M = 'MT6M';
	const DUR_3T6M = '3T6M';
	const DUR_1T3M = '1T3M';
	const DUR_LT1M = 'LT1M';
	const DUR_LT1W = 'LT1W';
	const DUR_NS   = 'NS';
	public static $str_project_duration;

	const WL_MT30H = 'MT';
	const WL_LT30H = 'LT';
	const WL_NS = 'NS';
	public static $str_project_workload;

	// Contract Limit
	const CONTRACT_LIMIT_ONE = 1;
	const CONTRACT_LIMIT_MORE = 0;
	protected static $str_project_contract_limit;

	const ACCEPT_TERM_NO  = 0;
	const ACCEPT_TERM_YES = 1;

	const PRICE_SIMPLE_PROJECT = 1;
	const PRICE_SMALL_PROJECT = 2;
	const PRICE_MEDIUM_PROJECT = 3;
	const PRICE_LARGE_PROJECT = 4;
	const PRICE_HUGE_PROJECT = 5;
	const PRICE_NOT_SURE = 6;

	public static $str_project_price = [
		self::PRICE_NOT_SURE => ['common.not_sure', 0, 100],
		self::PRICE_SIMPLE_PROJECT => ['common.simple_project', 100, 250],
		self::PRICE_SMALL_PROJECT => ['common.small_project', 250, 1000], 
		self::PRICE_MEDIUM_PROJECT => ['common.medium_project', 1000, 5000], 
		self::PRICE_LARGE_PROJECT => ['common.large_project', 5000, 10000], 
		self::PRICE_HUGE_PROJECT => ['common.huge_project', 10000, 0]
	];

  	function __construct() {
        parent::__construct();
        
	  	self::$str_project_type = [
	  		self::TYPE_HOURLY => trans('common.hourly'), 
	  		self::TYPE_FIXED  => trans('common.fixed_price')
	  	];

	  	self::$str_project_workload = [
	  		self::WL_MT30H => trans('job.more_than_30_hours_week'),
	  		self::WL_LT30H => trans('job.less_than_30_hours_week'),
	  		self::WL_NS => trans('common.not_sure'),
	  	];

	  	self::$str_project_duration = [
	  		self::DUR_LT1W => trans('common.lt1w'),
	  		self::DUR_LT1M => trans('common.lt1m'),
	  		self::DUR_1T3M => trans('common.1t3m'),
	  		self::DUR_3T6M => trans('common.3t6m'),
	  		self::DUR_MT6M => trans('common.mt6m'),
	  		self::DUR_NS   => trans('common.not_sure')
	  	];

	  	self::$str_project_rate = [
	  		self::RATE_NOT_SURE => trans('common.not_sure'),
	  		self::RATE_BELOW_10 => trans('job.$10_and_below'), 
	  		self::RATE_10_30 => trans('job.$10_$30'), 
	  		self::RATE_30_60 => trans('job.$30_$60'), 
	  		self::RATE_ABOVE_60 => trans('job.$60_and_above'), 
	  	];

	  	self::$str_project_term = [
	  		self::TERM_NOT_SURE => trans('common.not_sure'),
	  		self::TERM_ONE_TIME => trans('common.one_time_project'),
	  		self::TERM_ONGOING => trans('common.ongoing_project')
	  	];

	  	self::$str_project_level = [
	  		self::EXPERIENCE_LEVEL_ENTRY => trans('job.exp_lv_entry'),
	  		self::EXPERIENCE_LEVEL_INTERMEDIATE => trans('job.exp_lv_intermediate'),
	  		self::EXPERIENCE_LEVEL_EXPERT => trans('job.exp_lv_expert')
	  	];

	  	self::$str_project_contract_limit = [
	  		self::CONTRACT_LIMIT_ONE => trans('job.i_want_to_hire_one_freelancer'),
	  		self::CONTRACT_LIMIT_MORE => trans('job.i_need_to_hire_more_than_one_freelancer')
	  	];
  	}

  	public static function options($cat) {
  		if ($cat == 'type')
  			return [
		  		self::TYPE_HOURLY => trans('common.hourly'), 
		  		self::TYPE_FIXED  => trans('common.fixed')
		  	];

		return [];
  	}

	/**
	* Get the record of the client who posted this job
	*/
	public function client()
	{
		return $this->hasOne('iJobDesk\Models\Views\ViewUser', 'id', 'client_id')->withTrashed();
	}

	/**
	* Get the skills.
	*
	* @return mixed
	*/
	public function skills()
	{
		return $this->belongsToMany('iJobDesk\Models\Skill', 'project_skills', 'project_id', 'skill_id')
					->withPivot('order', 'level')
					->orderBy('order', 'asc');
	}
	
	/**
	* Get the applications.
	*
	* @return mixed
	*/
	public function applications()
	{
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id');
	}

	/**
	* Get the user.
	*
	* @return mixed
	*/
	public function user()
	{
		return $this->client();
	}

    public function isHourly() {
        return $this->type == self::TYPE_HOURLY;
    }

	/**
	* Get all proposals
	* 
	* @author Ro Un Nam
	* @return integer
	*/
	public function allProposals()
	{
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')->get();
	}

	/**
	* Get the total count of all proposals
	* 
	* @author Ro Un Nam
	* @return integer
	*/
	public function allProposalsCount() {
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')->count();
	}

	public function totalNewProposalsCount() {
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')
					->where('is_checked', 0)
					->count();	
	}

	/**
	* Get the total count of proposals
	* 
	* @author Ro Un Nam
	* @return integer
	*/
	public function totalProposalsCount($return_query = false) {
		$builder= ProjectApplication::addSelect(DB::raw('COUNT(*) AS count'))
									->join('projects', 'projects.id', '=', 'project_applications.project_id')
									->whereRaw('project_applications.status IN (' . implode(',', [
										ProjectApplication::STATUS_NORMAL,
										ProjectApplication::STATUS_ACTIVE,
										ProjectApplication::STATUS_HIRED,
									]) . ')')
									->whereRaw('project_applications.is_archived=' . ProjectApplication::IS_ARCHIVED_NO)
									->whereRaw('project_applications.is_declined=' . ProjectApplication::IS_DECLINED_NO)
						;

		if (!empty($this->id))
			$builder->whereRaw('project_applications.project_id=' . $this->id);
		else
			$builder->whereRaw('project_applications.project_id = projects.id');

		$query = $builder->toSql();

		if ($return_query)
			return $query;

		return DB::select($query)[0]->count;
	}

	/**
	* Get the total count of interviews
	* 
	* @author Ro Un Nam
	* @return integer
	*/
	public function totalInterviewsCount($return_query = false) {
		$builder= ProjectApplication::addSelect(DB::raw('COUNT(*) AS count'))
									->join('projects', 'projects.id', '=', 'project_applications.project_id')
									->whereRaw('project_applications.status=' . ProjectApplication::STATUS_ACTIVE)
									->whereRaw('project_applications.is_archived=' . ProjectApplication::IS_ARCHIVED_NO)
									->whereRaw('project_applications.is_declined=' . ProjectApplication::IS_DECLINED_NO);

		if (!empty($this->id))
			$builder->whereRaw('project_applications.project_id=' . $this->id);
		else
			$builder->whereRaw('project_applications.project_id = projects.id');

		$query = $builder->toSql();

		if ($return_query)
			return $query;

		return DB::select($query)[0]->count;
	}

	/**
	* Get the total count of invitations
	* 
	* @author Ro Un Nam
	* @return integer
	*/
	public function totalInvitationsCount()
	{
		return $this->hasMany('iJobDesk\Models\ProjectInvitation', 'project_id')
					->join('view_users', 'view_users.id', '=', 'project_invitations.receiver_id')
					->where('view_users.status', '<>', User::STATUS_SUSPENDED)
					->count();
	}

	/**
	* Get the total count of unanswered invitations
	* 
	* @author Ro Un Nam
	* @return integer
	*/
	public function totalUnansweredInvitationsCount()
	{
		return $this->hasMany('iJobDesk\Models\ProjectInvitation', 'project_id')
					->where('project_invitations.status', ProjectInvitation::STATUS_NORMAL)
					->count();
	}

	/**
	* Get the total count of hired
	*
	* @author Ro Un Nam
	* @return integer
	*/
	public function hiredContractsCount()
	{
		return $this->hasMany('iJobDesk\Models\Contract', 'project_id')
					->whereIn('contracts.status', [
                            Contract::STATUS_OPEN,
                            Contract::STATUS_PAUSED,
                            Contract::STATUS_SUSPENDED,
                            Contract::STATUS_CLOSED,
                        ])
					->count();
	}

	/**
	* Get the total count of normal proposals
	*
	* @author Ro Un Nam
	* @return integer
	*/
	public function normalApplicationsCount()
	{
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')
					->where('project_applications.status', ProjectApplication::STATUS_NORMAL)
					->where('project_applications.is_declined', ProjectApplication::IS_DECLINED_NO)
					->count();
	}

	/**
	* Get the total count of new proposals
	*
	* @author Ro Un Nam
	* @return integer
	*/
	public function newApplicationsCount()
	{
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')
					->whereIn('project_applications.status', [
						ProjectApplication::STATUS_NORMAL,
						ProjectApplication::STATUS_ACTIVE,
					])
					->where('project_applications.is_declined', ProjectApplication::IS_DECLINED_NO)
					->where('project_applications.is_archived', ProjectApplication::IS_ARCHIVED_NO)
					->where('project_applications.is_checked', 0)
					->count();
	}

	/**
	* Get the total count of declined proposals
	*
	* @author Ro Un Nam
	* @return integer
	*/
	public function declinedApplicationsCount()
	{
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')
					->where('project_applications.is_declined', '<>', ProjectApplication::IS_DECLINED_NO)
					->count();
	}

	/**
	* Get the total count of interviews
	*
	* @author Ro Un Nam
	* @return integer
	*/
	public function messagedApplicationsCount()
	{
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')
					->where('project_applications.status', ProjectApplication::STATUS_ACTIVE)
					->where('project_applications.is_declined', ProjectApplication::IS_DECLINED_NO)
					->count();
	}

	/**
	* Get the total count of offer & hires
	*
	* @author Ro Un Nam
	* @return integer
	*/
	public function offerHiredContractsCount()
	{
		return $this->hasMany('iJobDesk\Models\Contract', 'project_id')
					->whereIn('contracts.status', [
						Contract::STATUS_OFFER,
                        Contract::STATUS_OPEN,
                        Contract::STATUS_PAUSED,
                        Contract::STATUS_SUSPENDED,
                        Contract::STATUS_CLOSED,
                    ])
					->count();
	}

	public function archivedApplicationsCount()
	{
		return $this->hasMany('iJobDesk\Models\ProjectApplication', 'project_id')
					->whereIn('project_applications.status', [
						ProjectApplication::STATUS_HIRED, 
						ProjectApplication::STATUS_HIRING_CLOSED
					])
					->orWhere('project_applications.is_declined', '<>', ProjectApplication::IS_DECLINED_NO)
					->count();
	}

	/**
	* Check if User is author(client) of this project
	* 
	* @return mixed
	*/
	public function checkIsAuthor($user_id) {
		return ($this->client_id == $user_id);
	}

	/**
	* Get the contracts.
	*
	* @return mixed
	*/
	public function contracts()
	{
		return $this->hasMany('iJobDesk\Models\Contracts', 'project_id');
	}

	public function contracts_hired_count()
	{
		return $this->hasMany('iJobDesk\Models\Contract', 'project_id')
			->where('contracts.status', Contract::STATUS_OPEN)
			->count();
	}

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('type', File::TYPE_PROJECT);
    }

	/**
	* Hourly | Fixed
	*
	* @return mixed
	*/
	public function type_string() {
		if (isset(self::$str_project_type[$this->type])) {
			return self::$str_project_type[$this->type];
		}
		return "";
	}

	/**
	* Hourly | Fixed
	*
	* @return mixed
	*/
	public function pay_by_string() {
		if ( $this->type == self::TYPE_HOURLY ) {
			return trans('job.pay_by_the_hour');
		} else {
			return trans('job.pay_a_fixed_price');
		}
	}

	/**
	* Public | Private
	*
	* @return mixed
	*/
	public function is_public_string($trans=true) {
		if (isset(self::$str_project_is_public[$this->is_public])) {
			if ($trans) {
				return trans('common.' . self::$str_project_is_public[$this->is_public]);
			} else {
				return self::$str_project_is_public[$this->is_public];
			}
		}
		return "";
	}

	/**
	* Open | Closed
	*
	* @return mixed
	*/
	public function is_open_string($trans=true) {
		if (isset(self::$str_project_status[$this->status])) {
			if ($trans) {
				return trans('job.'.self::$str_project_status[$this->status]);
			} else {
				return self::$str_project_status[$this->status];
			}
			
		}
		return "";
	}

	public function isOpen() {
		return $this->status == self::STATUS_OPEN;
	}

	public function isSuspended() {
        return $this->status == self::STATUS_SUSPENDED; 
    }

	public function isClosed() {
        return $this->status == self::STATUS_CLOSED; 
    }

    public function isCancelled() {
        return $this->status == self::STATUS_CANCELLED; 
    }

    public function isDraft() {
        return $this->status == self::STATUS_DRAFT; 
    }

    public function isPublic() {
    	return $this->is_public == self::STATUS_PUBLIC;
    }

    public function isPrivate() {
    	return $this->is_public == self::STATUS_PRIVATE;
    }

    public function isProtected() {
    	return $this->is_public == self::STATUS_PROTECTED;
    }

    public function isFeatured() {
    	return $this->is_featured == self::STATUS_FEATURED;
    }

    public function isAvailableInvite() {
    	if ( $this->client->isSuspended() ) {
    		return false;
    	}

    	if ( $this->isClosed() || $this->isCancelled() || $this->isSuspended() ) {
    		return false;
    	}

    	return true;
    }

    public function getInvitationJson($freelancer) {
    	$user = Auth::user();

    	if ( !$user ) {
    		return json_encode([]);
    	}

        $array = [
            'id' => $this->id,
            'buyer_name' => $user->fullname(),
            'user_id' => $freelancer->id,
            'user_name' => $freelancer->fullname(),
            'user_title' => $freelancer->profile->title ? $freelancer->profile->title : '',
            'user_avatar' => avatar_url($freelancer),
            'user_url' => _route('user.profile', [$freelancer->id]),
            'action_url' => route('job.send_invitation.ajax'),
            'token' => csrf_token()
        ];

        return json_encode($array);
    }

	/**
	* Public | Private
	*/
	public function status_string($trans=true) {
		if ($this->status == self::STATUS_OPEN) {
			if (isset(self::$str_project_is_public[$this->is_public])) {
				if ($trans) {
					return trans('job.' . self::$str_project_is_public[$this->is_public]);
				} else {
					return self::$str_project_is_public[$this->is_public];
				}
			}
		}

		return $this->is_open_string($trans);
	}

	/**
	* Featured | Not featured
	*/
	public function featured_string($trans=true) {
		if (isset(self::$str_project_is_featured[$this->is_featured])) {
			if ($trans) {
				return trans('job.' . self::$str_project_is_featured[$this->is_featured]);
			} else {
				return trans('job.' . self::$str_project_is_featured[$this->is_featured]);
			}
		}

		return '';
	}

	/* Mar 2, 2016 - paulz
	*
	* Converts the given integer for Open | Closed to string
	*/
	public static function is_open_to_string($open_or_closed, $trans=true)
	{
		if (isset(self::$str_project_status[$open_or_closed])) {
			if ($trans) {
				return trans('job.'.self::$str_project_status[$open_or_closed]);
			} else {
				return self::$str_project_status[$open_or_closed];
			}
		}

		return "";
	}

	/**
	 * Price String
	 * @param $only_range if true, it doesn't display projec type such as small, medium, large and huge project
	 */
	public function price_string($only_range = false) {
		if (array_key_exists($this->price, self::$str_project_price)) {
			return self::price_string_with_param($this->price, $only_range);
		} else {
			$this->price = rand(self::PRICE_SIMPLE_PROJECT, self::PRICE_NOT_SURE);
			$this->save();

			return $this->price_string($only_range);
		}
	}

	public static function pricesWithRange($min, $max) {
		$prices = [];
		foreach (self::$str_project_price as $price => $range) {
			$from = $range[1];
			$to   = $range[2] == 0?9999999:$range[2];

			if ($min >= $from && $min <= $to)
				$prices[] = $price;
			elseif ($min <= $from && $max >= $to)
				$prices[] = $price;
			elseif ($max >= $from && $max <= $to)
				$prices[] = $price;
		}

		return $prices;
	}

	public static function price_string_with_param($price, $only_range = false) {
			$price = self::$str_project_price[$price];

			$str = '';
			if (!$only_range)
				$str = trans($price[0]) . ' ';
			if ($price[1] != 0) {
				$str .= '$' . $price[1];

				if ($price[2] != 0)
					$str .= ' - $' . $price[2];
				else
					$str .= ' +';
			} else {
				if ( $only_range ) {
					$str = trans($price[0]);
				}
			}

			return $str;
	}

	/**
	* Full Time | Part Time | As Needed
	*
	* @return mixed
	*/
	public function affordable_rate_string() {
		if (isset(self::$str_project_rate[$this->affordable_rate])) {
			return self::$str_project_rate[$this->affordable_rate];
		}

		return "";
	}

	/**
	* Full Time | Part Time | As Needed
	*
	* @return mixed
	*/
	public function workload_string() {
		if (isset(self::$str_project_workload[$this->workload])) {
			return self::$str_project_workload[$this->workload];
		}

		return "";
	}

	/**
	* Less than a week | Less than a month | 1 to 3 months | 3 to 6 months | More than 6 months
	*
	* @return mixed
	*/
	public function duration_string() {
		if (isset(self::$str_project_duration[$this->duration])) {
			return self::$str_project_duration[$this->duration];
		}
		
		return "";
	}

	/**
	* Not Sure | One Time | Ongoing
	*
	* @return mixed
	*/
	public function term_string() {
		if (isset(self::$str_project_term[$this->term])) {
			return self::$str_project_term[$this->term];
		}

		return self::$str_project_term[self::TERM_NOT_SURE];
	}

	/**
	* Contract limit string
	* @return mixed
	*/
	public function contract_limit_string() {
		if ( isset(self::$str_project_contract_limit[$this->contract_limit]) ) {
			return self::$str_project_contract_limit[$this->contract_limit];
		}

		return self::$str_project_contract_limit[self::CONTRACT_LIMIT_ONE];
	}

	/**
	* Entry | Intermediate | Expert
	*
	* @return mixed
	*/
	public function exp_lv_string() {
		if (isset(self::$str_project_level[$this->experience_level])) {
			return self::$str_project_level[$this->experience_level];
		}

		return self::$str_project_exp_lv[self::EXPERIENCE_LEVEL_ENTRY];
	}

	/**
	* Returns array for each <select> tag
	*
	* @author paulz
	* @created Mar 10, 2016
	*/
	public static function getOptions($type)
	{
		switch ($type) {
			case "is_public":
				$options = array_flip(self::$str_project_is_public);
				break;

			case "is_open":
				$options = array_flip(self::$str_project_status);
				break;

			default:
				$options = [];
		}

		return $options;
	}

	/**
	* Get the record of the client who posted this job
	*/
	public function category()
	{
		return $this->hasOne('iJobDesk\Models\Category', 'id', 'category_id');
	}

	/**
	* Get the param from $_REQUEST
	*/
	public static function input($key, &$param)
	{
		if (isset($param[$key])) {
			return $param[$key];
		} else {
			return false;
		}
	}

	/**
	 * Check if this job is saved
	 * @author KCG
	 * @since  20170601
	 *
	 */
	public function isSaved() {
		$user = Auth::user();

		if ( !$user ) {
			return false;
		}

		$userSavedProject = UserSavedProject::where('project_id', $this->id)->where('user_id', $user->id)->get();

		return count($userSavedProject) != 0;
	}

	/**
	 * Close all opened project applications
	 * @author RSN
	 * @since 20170615
	 *
	 */

	public function closeAllOpenApplications() {
		$connections = ProjectApplication::JOB_CONNECTIONS;

		// For Featured Job
		if ( $this->is_featured == Project::STATUS_FEATURED ) {
			$connections = Settings::get('CONNECTIONS_FEATURED_PROJECT'); 
		}

		// $applications = $this->applications()->where('provenance', ProjectApplication::PROVENANCE_NORMAL)
		// ->where('status', '<>', ProjectApplication::STATUS_CLIENT_DCLINED)
		// ->where('status', '<>', ProjectApplication::STATUS_FREELANCER_DECLINED);

		$applications = $this->applications()->where('provenance', ProjectApplication::PROVENANCE_NORMAL)
							 ->where('is_declined', ProjectApplication::IS_DECLINED_NO)
							 ->get();
		
		foreach($applications as $application) {
			$connections_case = $connections;
			
			if ( $application->is_featured ) {
				$connections_case = $connections_case * ProjectApplication::FEATURED_PROPOSAL_TIMES;
	    	}
	    	
	    	$application->user->stat->connects += $connections_case;
	    	$application->user->stat->save();
	    }
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeAcceptTerm($query)
	{
		return $query->where('accept_term', Project::ACCEPT_TERM_YES);
	}

	/**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublics($query)
    {
        return $query->where('is_public', Project::STATUS_PUBLIC);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProtected($query)
    {
    	return $query->where(function($query2) {
					$query2->where('is_public', self::STATUS_PUBLIC)
		        		   ->orWhere('is_public', self::STATUS_PROTECTED);
    	});
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->join('users', 'projects.client_id', '=', 'users.id')
        			 ->where('projects.status', Project::STATUS_OPEN)
        			 ->where('users.status', User::STATUS_AVAILABLE);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExceptMine($query)
    {
        $arr = [];

        $user = Auth::user();

        if ( $user ) {
        	$arr[] = Auth::user()->id;
        }

        return $query->whereNotIn('client_id', $arr);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDuration($query, $duration)
    {
        return $query->where('duration', $duration);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByWorkload($query, $workload)
    {
        return $query->where('workload', $workload);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByExperienceLevel($query, $experienceLevel)
    {
        return $query->where('experience_level', $experienceLevel);
    }

    /**
     *  It is available for Fixed Job
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPrice($query, $price)
    {
        return $query->where('price', $price)
        		     ->where('type', self::TYPE_FIXED);
    }

    /**
     * It is available for Hourly Job
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPriceRate($query, $price)
    {
        return $query->where('affordable_rate', $price)
        		     ->where('type', self::TYPE_HOURLY);
    }

    public function visibility_string() {
		if (isset(self::$str_project_is_public[$this->is_public])) {
			return self::$str_project_is_public[$this->is_public];
		}
		return "";
	}

	public function status_admin_string() {
		if (isset(self::$str_project_status[$this->status])) {
			return self::$str_project_status[$this->status];
		}
		return "";
	}

	public function cover_letter() {
		if (isset(self::$str_project_cover_letter[$this->req_cv])) {
			return self::$str_project_cover_letter[$this->req_cv];
		}
		return "";
	}

	public static function enableStatusChanged($project) {
		$attributes = '';

		if ($project->status == self::STATUS_SUSPENDED) {
			$attributes .= ' data-status-' . self::STATUS_OPEN . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
		} elseif ($project->status == self::STATUS_OPEN) {
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
		} elseif ($project->status == self::STATUS_CLOSED) {
			$attributes .= ' data-status-' . self::STATUS_DELETED . '=true';
		} 

		return $attributes;
	}

	public function canViewPrivate($user) {
		if (!$user)
			return false;

		if (!$this->isPrivate())
			return true;

		$can_view = ProjectApplication::where('project_id', $this->id)
									  	->whereIn('provenance', [
									  		ProjectApplication::PROVENANCE_INVITED,
									  		ProjectApplication::PROVENANCE_OFFER
									  	])
									  	->where('user_id', $user->id)
									  	->exists();

		$can_view = $can_view || ProjectInvitation::where('project_id', $this->id)
												  ->whereNotIn('status', [ProjectInvitation::STATUS_DECLINED])
												  ->where('receiver_id', $user->id)
												  ->exists();

		$can_view = $can_view || $this->client_id == $user->id;
		$can_view = $can_view || $user->isSuper();

		return $can_view;
	}

	public function isHired($user) {
		$application = $this->applications()
							->where('user_id', $user->id)
							->orderBy('created_at', 'DESC')
							->first();

		if (!$application)
			return false;

		return $application->isHired();
	}

	public static function openedJobs($user) {
		return self::where('client_id', $user->id)
				   ->whereIn('status', [
					  	self::STATUS_OPEN, 
					  	self::STATUS_SUSPENDED
				   ])
				   ->where('accept_term', self::ACCEPT_TERM_YES);
	}

	public function isSentProposal($user) {
		return ProjectApplication::where('project_id', $this->id)
								 ->where('user_id', $user->id)
								 ->where('status', '<>', ProjectApplication::STATUS_HIRING_CLOSED)
								 ->count();
	}
}

