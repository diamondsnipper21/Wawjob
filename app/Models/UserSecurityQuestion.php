<?php namespace iJobDesk\Models;


use DB;

use iJobDesk\Models\User;

/**
* @author Ro Un Nam
*/
class UserSecurityQuestion extends Model {

	protected $table = 'user_security_questions';

	public static function getUserSecurityQueston($user_id) {
		return self::leftJoin('security_questions', 'question_id', '=', 'security_questions.id')
					->where('user_security_questions.user_id', $user_id)
					->whereNull('user_security_questions.deleted_at')
					->orderby('user_security_questions.created_at', 'desc')
					->select(['user_security_questions.id', 'user_security_questions.question_id', 'user_security_questions.answer', 'security_questions.question'])
					->first();
	}

}