<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;


class UserCompany extends Model {

    use SoftDeletes;

    /**
    * The table associated with the model.
    *
    * @var string
    */

    protected $table = 'user_companies';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('iJobDesk\Models\User', 'user_id');
    }

    public function fullphone() {
        $user_contact = $this->user->contact;
        return fullphone($this->phone, $user_contact->country_code);
    }
}
