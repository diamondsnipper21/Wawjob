<?php namespace iJobDesk\Models;

use Auth;
use DB;

class Unsubscribe extends Model {
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'unsubscribes';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    /**
     * Generate unsubscribe url
     * @param $slug The slug of email template.
     */
    public static function url($slug, $email) {
        $token = "{$slug},{$email}";
        return route('email.unsubscribe', ['token' => encrypt_string($token)]);
    }
}
