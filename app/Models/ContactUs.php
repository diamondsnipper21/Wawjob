<?php namespace iJobDesk\Models;

use Auth;
use DB;

class ContactUs extends Model {
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'contact_us';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;
}
