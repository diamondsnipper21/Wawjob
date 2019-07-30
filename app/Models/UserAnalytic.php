<?php namespace iJobDesk\Models;

use iJobDesk\Models\Ip2Country;

class UserAnalytic extends Model {

	protected $table = 'user_analytics';

	const TYPE_LOGIN   	= 0;
    const TYPE_LOGOUT	= 1;

    public function type_string() {
    	if ( $this->type ) {
    		return 'logout';
    	} else {
    		return 'login';
    	}
    }

    public function country() {
        return Ip2Country::getCountryCode($this->login_ipv4);
    }

}
