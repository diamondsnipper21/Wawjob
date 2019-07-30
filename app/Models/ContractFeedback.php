<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ContractFeedback extends Model {

    use SoftDeletes;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'contract_feedbacks';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $fillable = ['contract_id'];

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

    public function contract()
    {
        return $this->hasOne('iJobDesk\Models\Contract', 'id', 'contract_id');
    }
}
