<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserStat extends Model {

	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'user_stats';

	protected $dates = ['deleted_at'];

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = ['user_id'];

	public function user() {
		return $this->hasOne('iJobDesk\Models\User', 'id', 'user_id');
	}

	public function total_spent_string() {
		if ($this->total_spent < 100)
			return trans('job.about_n', ['n' => 100]);
		elseif ($this->total_spent >= 100 && $this->total_spent < 500)
			return trans('job.over_n_total_spent', ['n' => number_format(100)]);
		elseif ($this->total_spent >= 500 && $this->total_spent < 1000)
			return trans('job.over_n_total_spent', ['n' => number_format(500)]);
		elseif ($this->total_spent >= 1000 && $this->total_spent < 2000)
			return trans('job.over_n_total_spent', ['n' => number_format(1000)]);
		elseif ($this->total_spent >= 2000 && $this->total_spent < 3000)
			return trans('job.over_n_total_spent', ['n' => number_format(2000)]);
		elseif ($this->total_spent >= 3000 && $this->total_spent < 4000)
			return trans('job.over_n_total_spent', ['n' => number_format(3000)]);
		elseif ($this->total_spent >= 4000 && $this->total_spent < 5000)
			return trans('job.over_n_total_spent', ['n' => number_format(4000)]);
		elseif ($this->total_spent >= 5000 && $this->total_spent < 10000)
			return trans('job.over_n_total_spent', ['n' => number_format(5000)]);
		elseif ($this->total_spent >= 10000 && $this->total_spent < 20000)
			return trans('job.over_n_total_spent', ['n' => number_format(10000)]);
		elseif ($this->total_spent >= 20000 && $this->total_spent < 30000)
			return trans('job.over_n_total_spent', ['n' => number_format(20000)]);
		elseif ($this->total_spent >= 30000 && $this->total_spent < 40000)
			return trans('job.over_n_total_spent', ['n' => number_format(30000)]);
		elseif ($this->total_spent >= 40000 && $this->total_spent < 50000)
			return trans('job.over_n_total_spent', ['n' => number_format(40000)]);
		else
			return trans('job.over_n_total_spent', ['n' => number_format(50000)]);
	}
	
}