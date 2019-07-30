<?php namespace iJobDesk\Models;

use DB;

class Ip2Country extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'ip2countries';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    public static function getCountryCode($ip) {
        $codes = self::where('ip1', '<=', $ip)
                        ->where('ip2', '>=', $ip)
                        ->where('code', '<>', 'ZZ')
                        ->pluck('code')
                        ->toArray();

        if ( $codes ) {
        	return implode(', ', $codes);
        } else {
        	return 'None';
        }
    }
}