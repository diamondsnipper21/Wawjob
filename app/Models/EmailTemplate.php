<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use App;
use File;
use Config;
use Mail;
use Log;

use iJobDesk\Mail\EmailSend;
use iJobDesk\Models\Settings;
use iJobDesk\Models\User;
use iJobDesk\Models\Unsubscribe;
use iJobDesk\Models\Views\ViewUser;

class EmailTemplate extends Model {

	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'email_templates';

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

	public static function getTemplateFromSlug($slug, $for = 0) {
		return self::where('slug', $slug)
					->where('for', $for)
					->first();
	}

	public static function getOptions($cat) {
		if ($cat == 'status') {
			return [
				self::STATUS_ENABLE => 'Enabled',
				self::STATUS_DISABLE => 'Disabled',
				self::STATUS_DELETE => 'Deleted',
			];
		} elseif ($cat == 'for') {
			return [
				0 => 'General',
				User::ROLE_USER_FREELANCER => 'Freelancer',
				User::ROLE_USER_BUYER => 'Buyer',
				// User::ROLE_USER_SITE_MANAGER => 'Site Manager',
				// User::ROLE_USER_ADMIN => 'Admin',
				User::ROLE_USER_SUPER_ADMIN => 'Super Admin',
			];
		}
	}

	public static function sendToSuperAdmin($slug, $for = 0, $params = [], $single_email = '') {
		$users = User::getSuperAdmins();

		$sent = true;
		foreach ($users as $user) {
			$params['super_admin_id'] 	= $user->id;
			$params['super_admin_name'] = $user->fullname();
			$params['USER'] 		    = $user->fullname();

			$sent = self::send($user, $slug, $for, $params, $single_email) && $sent;
		}

		return $sent;
	}

	/**
	 *
	 */
	public static function sendMultiple($users = null, $slug, $for = 0, $params = [], $single_email = '') {
		$sent = false;
		foreach ($users as $user) {
			$sent = self::send($user, $slug, $for, $params, $single_email);
		}

		return $sent;
	}

	/**
	* Send email from template
	*/
	public static function send($user = null, $slug, $for = 0, $params = [], $single_email = '', $single_name = '', $from_email = null, $from_name = null) {
		if ( $user ) {
			if (!is_object($user))
				$user = User::find($user);
			$user_lang = strtoupper($user->getLocale());
			$email = $user->email;
			$name = $user->fullname();
		} else {
			$email = $single_email;
			$name  = $single_name;
		}

		if (!self::needSend($user, $slug)) {
			return true;
		}

		if ( !isset($user_lang) || !$user_lang ) {
			$user_lang = strtoupper(config('app.locale'));
		}

		$template = self::getTemplateFromSlug($slug, $for);

		if ( !$template ) {
			Log::error('Not defined email template. [' . $slug . ', ' . $for . ']');
			return false;
		}

		$subject = parse_json_multilang($template->subject, $user_lang);
		$content = parse_json_multilang($template->content, $user_lang);

		// Attach header & footer into email template.
		$lang = App::getLocale();
		$emt_header = File::get(resource_path('email/' . $lang) . '/header.html');
		$emt_footer = File::get(resource_path('email/' . $lang) . '/footer.html');

		$content = $emt_header . $content . $emt_footer;

		$site_url = config('app.url');

		// Values to be replaced
		$params['SITE_ROOT_URL'] 			= $site_url;
		$params['SITE_LOGO'] 				= $site_url . '/assets/images/common/logo.png';
		$params['UNSUBSCRIBE_URL'] 			= Unsubscribe::url($slug, $email);
		$params['PRIVACY_URL'] 				= $site_url . '/privacy-policy';
		$params['CONTACT_US_URL'] 			= route('frontend.contact_us');
		$params['GET_STARTED_BUYER_URL'] 	= route('job.create');
		$params['SEARCH_FREELANCER_URL'] 	= route('search.user');
		$params['SEARCH_JOB_URL'] 			= route('search.job');
		$params['POST_JOB_URL'] 			= route('job.create');
		$params['LOGIN_URL'] 				= route('user.login');
		$params['CONTACT_US_MAIL'] 			= Settings::get('CONTACT_EMAIL_ADDRESS');
		$params['COMPANY_ADDRESS'] 			= Settings::get('COMPANY_ADDRESS');

		if ( isset($params['AMOUNT']) ) {
			$params['AMOUNT'] = '$' . $params['AMOUNT'];
		}

		if ( isset($params['PAID_TOTAL']) ) {
			$params['PAID_TOTAL'] = '$' . $params['PAID_TOTAL'];
		}

		if ( $params ) {
			$replace = $replace_to = [];

			foreach ( $params as $key => $value ) {
				$replace[] = str_is('@#*#', $key)?$key:'@#' . $key . '#';
				$replace_to[] = $value;
			}

			$subject = str_ireplace($replace, $replace_to, $subject);
			$content = str_ireplace($replace, $replace_to, $content);
		}

		$sent = false;
		try {
			Mail::to($email, $name)
	        	->queue(new EmailSend($subject, $content, $from_email, $from_name));

			$sent = true;

		} catch ( Exception $e ) {
			Log::error('EmailTemplate.php@send:' . $e->getMessage());
			return false;
		}

		if ( $sent ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if need to send email for this user. This will decide by email notifcation settings.
	 */
	private static function needSend($user, $slug) {
		if (!$user)
			return true;

		if ($user->isAdmin())
			return true;

		$settings = $user->userNotificationSetting;
		$mapping = self::mappingToNS();
		if ($user->isBuyer() || $user->isFreelancer()) {
			foreach ($user->isBuyer()?$mapping['Buyer']:$mapping['Freelancer'] as $key_slug => $value_setting) {
				if ($key_slug == $slug && !$settings->$value_setting)
					return false;
			}
		}

		foreach ($mapping['General'] as $key_slug => $value_setting)
			if ($key_slug == $slug && !$settings->$value_setting)
				return false;
		
		return true;
	}

	/**
	* Send normal email
	*/
	public static function contactUs($contact_us, $message) {

		$sent = false;

		// Send contact email to super admin
		$super_admins = ViewUser::getSuperAdmins();

		if ( count($super_admins) ) {
			$config_mails = Config::get('mail');

			$params = [
				'SUBJECT' => strip_tags($contact_us->subject)
			];

			$message = strip_tags(nl2br($message), '<br>');

			foreach ( $super_admins as $admin ) {
				try {
					// To admin
					$params['SUPER_ADMIN_NAME'] = $admin->fullname();
					$params['CUSTOMER_NAME'] 	= $contact_us->fullname;
					$params['MESSAGE'] 			= $message;

					$sent = self::send($admin, 'CONTACT_ADMIN', User::ROLE_USER_SUPER_ADMIN, $params);
				} catch ( Exception $e ) {
					Log::error('EmailTemplate@contactUs:' . $e->getMessage());
				}
			}

			// To user
			$params['USER'] = $contact_us->fullname;
			$sent = self::send(null, 'CONTACT', 0, $params, $contact_us->email, $contact_us->fullname);
		}

		if ( $sent ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns subject from language
	 */
	public function getSubject($lang) {
		$subject = json_decode($this->subject, true);

		if ($subject != null) {
			return array_key_exists($lang, $subject) ? $subject[$lang] : '';
		} else {
			return '';
		}
	}

	/**
	 * Mapping Table for Email Templates and Notification Settings
	 */
	private static function mappingToNS() {
		return [
			'Buyer' => [
				'JOB_POSTED' => 'job_is_posted_or_modified',
				'JOB_UPDATED' => 'job_is_posted_or_modified',
				'NEW_PROPOSAL' => 'proposal_is_received',
				'INVITATION_ACCEPTED' => 'interview_is_accepted_or_offer_terms_are_modified',
				'OFFER_DECLINED' => 'interview_or_offer_is_declined_or_withdrawn',
				'INVITATION_DECLINED' => 'interview_or_offer_is_declined_or_withdrawn',
				'OFFER_ACCEPTED' => 'offer_is_accepted',
				'JOB_EXPIRED_SOON' => 'job_posting_will_expire_soon',
				'JOB_EXPIRED' => 'job_posting_expired',
			],
			'Freelancer' => [
				'OFFER_RECEIVED' => 'offer_or_interview_invitation_is_received',
				'INVITATION_RECEIVED' => 'offer_or_interview_invitation_is_received',
				'OFFER_WITHDRAWN' => 'offer_or_interview_invitation_is_withdrawn',
				'PROPOSAL_REJECTED' => 'proposal_is_rejected',
				'JOB_UPDATED' => 'applied_job_is_modified_or_canceled',
				'JOB_CLOSED' => 'applied_job_is_modified_or_canceled',
				'JOB_CANCELLED' => 'applied_job_is_modified_or_canceled',
				'JOB_EXPIRED' => 'applied_job_is_modified_or_canceled',
				'JOB_RECOMMENDATION' => 'job_recommendations'
			],
			'General' => [
				'CONTRACT_STARTED' => 'hire_is_made_or_contract_begins',
				'CONTRACT_TERM_CHANGED' => 'contract_terms_are_modified',
				'CONTRACT_ENDED' => 'contract_ends',
				'CONTRACT_ENDED_WITHOUT_PAYMENT' => 'contract_ends',
				'CONTRACT_CLOSED_WHEN_DELETING_ACCOUNT' => 'contract_ends',
				'TIMELOG_REVIEW' => 'timelog_is_ready_for_review'
			]
		];
	}

	public static function setting_key($key, $slug) {
		$mappings = self::mappingToNS();

		if (!array_key_exists($key, $mappings))
			return null;

		if (!array_key_exists($slug, $mappings[$key]))
			return null;

		return $mappings[$key][$slug];
	}

	/**
	 * Check whether this email template is for guest or not.
	 */
	public static function isForGuest($slug) {
		return $slug == 'CONTACT' || $slug == 'CONTACT_ADMIN_REPLY';
	}

	public static function isForUser($slug) {
		$mappings = self::mappingToNS();

		if (in_array($slug, array_keys($mappings['Buyer'])))
			return true;

		if (in_array($slug, array_keys($mappings['Freelancer'])))
			return true;

		if (in_array($slug, array_keys($mappings['General'])))
			return true;

		return false;
	}

	public function getSubjectStrings($splitter = ', ') {
		$subjects = [];
		foreach (['EN', 'KP', 'CH'] as $lang) {
			$subject = $this->getSubject($lang);

			if (empty($subject))
				continue;

			$subjects[] = $lang.': '.$subject;
		}

		return implode($splitter, $subjects);
	}

	public function getContent($lang) {
		$content = json_decode($this->content, true);

		if ($content != null) {
			return array_key_exists($lang, $content) ? $content[$lang] : '';
		} else {
			return '';
		}
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