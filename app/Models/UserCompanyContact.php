<?php namespace iJobDesk\Models;

class UserCompanyContact extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */

    protected $table = 'user_company_contacts';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('iJobDesk\Models\User', 'user_id');
    }

    /**
    * Get the country record associated with the user contact.
    *
    * @return mixed
    */
    public function country() {
        return $this->hasOne('iJobDesk\Models\Country', 'charcode', 'country_code');
    }

    /**
    * Get the timezone record associated with the user contact.
    *
    * @return mixed
    */
    public function timezone() {
        return $this->hasOne('iJobDesk\Models\Timezone', 'id', 'timezone_id');
    }

    public function fullphone() {
        return fullphone($this->phone, $this->country_code);
    }

}
