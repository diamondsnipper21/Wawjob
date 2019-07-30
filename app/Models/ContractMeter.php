<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ContractMeter extends Model {

	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'contract_meters';

	/**
	* The attributes that aren't mass assignable.
	*
	* @var array
	*/
	protected $fillable = [];

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

	public function updateTotal($amount) {
		$this->total_amount = $this->total_amount + $amount;
		$this->save();
	}	
}
