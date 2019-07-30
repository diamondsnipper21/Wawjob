<?php namespace iJobDesk\Models;


class Role extends Model {

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'roles';

  /**
   * The users that belong to the role.
   *
   * @return mixed
   */
  public function users()
  {
    return $this->belongsToMany('iJobDesk\Models\User', 'users_roles', 'role_id', 'user_id');
  }

  /**
   * Get the role for buyer and freelancer.
   *
   * @return mixed
   */
  public static function getBFRole()
  {
    $bfRole = self::where('slug', 'like', '%buyer%')
                  ->orWhere('slug', 'like', '%freelancer%')
                  ->get();
    return $bfRole;
  }

}