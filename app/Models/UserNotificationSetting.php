<?php namespace iJobDesk\Models;


use DB;

use iJobDesk\Models\User;

/**
* @author Ro Un Nam
*/
class UserNotificationSetting extends Model {

	protected $table = 'user_notification_settings';

	public static function getUserNotificationSettings($user_id) {
		return self::where('user_id', $user_id)->first();
	}

}