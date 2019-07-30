<?php namespace iJobDesk\Models;

use iJobDesk\Models\Contract;

class ContractAction extends Model {

    protected $table = 'contract_actions';

    protected $dates = ['created_at'];

    public $timestamps = true;

    public function user() {
    	return $this->belongsTo('iJobDesk\Models\User', 'doer_id', 'id');
    }

    public function statusToString() {
    	return array_get(Contract::$str_contract_status, $this->status);
    }
}