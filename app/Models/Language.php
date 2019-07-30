<?php namespace iJobDesk\Models;


class Language extends Model {

	/**
   * The table associated with the model.
   *
   * @var string
   */
	protected $table = 'languages';

	/**
   * Get the all notifications and decode content field.
   *
   * @return array
   */
	public static function getAllCode()
	{
		return self::select("id", "code", "country")->orderBy('code')->get();
	}

}
