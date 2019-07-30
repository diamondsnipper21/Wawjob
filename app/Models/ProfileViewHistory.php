<?php namespace iJobDesk\Models;


class ProfileViewHistory extends Model {

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'profile_view_history';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['buyer_id', 'user_id'];

  /**
   * Check 
   *
   * @author sg
   * @since Apr 18, 2017
   * @version 1.0
   * @return 
   */
  public static function isSaved($buyer_id, $user_id) {
    $object = self::where('buyer_id', '=', $buyer_id)
                    ->where('user_id', '=', $user_id)
                    ->first();
    if ($object) {
      return true;
    }
    return false;
  }
   
}