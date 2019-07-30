<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserContact extends Model {

	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'user_contacts';

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = ['user_id'];

	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = true;

    function __construct() {
        parent::__construct();
    }

	/**
	* Get the country record associated with the user contact.
	*
	* @return mixed
	*/
	public function country()
	{
		return $this->hasOne('iJobDesk\Models\Country', 'charcode', 'country_code');
	}

	/**
	* Get the invoice country record associated with the user contact.
	*
	* @return mixed
	*/
	public function invoice_country()
	{
		return $this->hasOne('iJobDesk\Models\Country', 'charcode', 'invoice_country_code');
	}

	/**
	* Get the timezone record associated with the user contact.
	*
	* @return mixed
	*/
	public function timezone()
	{
		return $this->hasOne('iJobDesk\Models\Timezone', 'id', 'timezone_id');
	}

	public function user()
	{
		return $this->hasOne('iJobDesk\Models\User', 'id', 'user_id');
	}

	public function fullphone() {
		return fullphone($this->phone, $this->country_code);
	}
}