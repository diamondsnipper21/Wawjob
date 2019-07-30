<?php namespace iJobDesk\Models;
/**
* @author paulz
*/


class Timezone extends Model {

  protected $table = 'timezones';

  /**
  * @created Mar 21, 2016
  */
  public static function offsetToName($gmt_offset)
  {
    $row = self::where("gmt_offset", "=", $gmt_offset)
      ->select(['name', 'label'])
      ->first();

    if ( !$row ) {
      return false;
    }

    return $row->name;
  }


  /**
  * @created Mar 24, 2016
  */
  public static function nameToOffset($tz_name)
  {
    $row = self::where("name", "=", $tz_name)
      ->select(['gmt_offset'])
      ->first();

    if ( !$row ) {
      return false;
    }

    return $row->gmt_offset;
  }
}
