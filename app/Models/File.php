<?php namespace iJobDesk\Models;

use Config;
use Auth;
use Log;

class File extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'files';

	const TYPE_MESSAGE 				= 1;
	const TYPE_PROJECT_APPLICATION 	= 2;
	const TYPE_CONTRACT 			= 3;
	const TYPE_TODO 				= 4;
	const TYPE_TICKET 				= 5;
	const TYPE_TICKET_COMMENT 		= 6;
	const TYPE_ADMIN_MESSAGE 		= 7;
	const TYPE_USER_AVATAR 			= 8;
	const TYPE_USER_PORTFOLIO 		= 9;
	const TYPE_USER_QRCODE 			= 10;
	const TYPE_PROJECT 				= 11;
	const TYPE_ID_VERIFICATION 		= 12;

	const TYPE_TEMP 				= 9999999;

	public function hash() {
		$this->hash = md5($this->user_id . '_' . $this->name . '_' . $this->type);
	}

	public function isCorrect($hash) {
		// return $this->hash == $hash && (($this->type == self::TYPE_USER_AVATAR || $this->type == self::TYPE_USER_PORTFOLIO) || $this->user_id == Auth::user()->id);
		return $this->hash == $hash;
	}

	public static function getTypeByClass($class) {
		$options = self::getOptions();
		$types = array_keys($options);
		$classes = array_pluck($options, 'class');

		return $types[array_search($class, $classes)];
	}

	public static function getOptions() {
		$settings = Config::get('settings');

		return [
			self::TYPE_PROJECT => [
				'prefix' 			=> 'jb',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'Project'
			],
			self::TYPE_MESSAGE => [
				'prefix' 			=> 'msg',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'ProjectMessage'
			],
			self::TYPE_PROJECT_APPLICATION => [
				'prefix' 			=> 'jb',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'ProjectApplication'
			],
			self::TYPE_CONTRACT => [
				'prefix' 			=> 'ctt',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'Contract'
			],
			self::TYPE_TODO => [
				'prefix' 			=> 'td',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'Todo'
			],
			self::TYPE_TICKET => [
				'prefix' 			=> 'tkt',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'Ticket'
			],
			self::TYPE_TICKET_COMMENT => [
				'prefix' 			=> 'tktcmt',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'TicketComment'
			],
			self::TYPE_ADMIN_MESSAGE => [
				'prefix' 			=> 'admmsg',
				'file_types' 		=> $settings['uploads']['file_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'AdminMessage'
			],
			self::TYPE_USER_AVATAR => [
				'prefix' 			=> 'uavat',
				'file_types' 		=> $settings['uploads']['image_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> true,
				'class'				=> 'User'
			],
			self::TYPE_USER_PORTFOLIO => [
				'prefix' 			=> 'uprtf',
				'file_types' 		=> $settings['uploads']['image_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> true,
				'class'				=> 'UserPortfolio'
			],
			self::TYPE_USER_QRCODE => [
				'prefix' 			=> 'uqr',
				'file_types' 		=> $settings['uploads']['image_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> true,
				'class'				=> 'UserPaymentGateway'
			],
			self::TYPE_ID_VERIFICATION => [
				'prefix' 			=> 'idvrft',
				'file_types' 		=> $settings['uploads']['id_verification_types'],
				'file_size' 		=> $settings['uploads']['file_size'],
				'image'				=> false,
				'class'				=> 'TicketCommentIDVerification'
			],
		];
	}

	public function user() {
		return $this->hasOne('iJobDesk\Models\User', 'id', 'user_id')->withTrashed();
	}

	/**
	 * Unused Files
	 */
	public static function getUnusedFiles($type) {
		$me = Auth::user();

		$unused_files = self::where('type', $type)
							->where('user_id', $me->id)
							->whereNull('is_approved')
							->get();

		return $unused_files;
	}

	public function isApproved() {
		return $this->is_approved == 1;
	}

	public function remove() {
		$user = Auth::user();

		if (!$user->isAdmin() && $this->user_id != $user->id)
	        return false;

        $this->delete();

        return true;
	}

	public function getPath($thumb = null, $need_orig = true) {
		$prefix = getRoot() . '/';

		if (strpos($this->path, ':') === 1 || strpos($this->path, ':') === 0)
			$prefix = '';

		$file_path = $prefix . $this->path . $this->name;
		if ($thumb) {
			$thumb_file_path = $prefix . $this->path . (str_replace('.' . $this->ext, '', $this->name) . '_thumbnail.' . $this->ext);

			if ($need_orig && !file_exists($thumb_file_path))
				$file_path = $file_path;
			else
				$file_path = $thumb_file_path;
		}

		return $file_path;
	}

	/**
	 * @param $id The user id
	 */
	public static function getAvatar($id) {
		return self::where('target_id', $id)
				   ->where('type', self::TYPE_USER_AVATAR)
				   ->orderBy('id', 'DESC')
				   ->first();
	}

	/**
	 * @param $id The portfolio id
	 */
	public static function getPortfolio($id) {
		return self::where('target_id', $id)
				   ->where('type', self::TYPE_USER_PORTFOLIO)
				   ->orderBy('id', 'DESC')
				   ->first();
	}

	public function icon() {
		$icon = 'fa-file-o';

		if (strpos($this->mime_type, 'image/') !== FALSE)
			$icon = 'fa-file-image-o';

		if (strpos($this->mime_type, 'text/') !== FALSE)
			$icon = 'fa-file-text-o';

		if (strpos($this->mime_type, 'word') !== FALSE)
			$icon = 'fa-file-word-o';

		if (strpos($this->mime_type, 'sheet') !== FALSE)
			$icon = 'fa-file-excel-o';

		if (strpos($this->mime_type, 'powerpoint') !== FALSE)
			$icon = 'fa-file-powerpoint-o';

		if (strpos($this->mime_type, 'pdf') !== FALSE)
			$icon = 'fa-file-pdf-o';

		if (strpos($this->mime_type, 'zip') !== FALSE)
			$icon = 'fa-file-zip-o';

		return $icon . ' fa-file';
	}
}