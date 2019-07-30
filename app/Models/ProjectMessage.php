<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;

class ProjectMessage extends Model {
	use SoftDeletes;

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'project_messages';

	const MESSAGES_PER_PAGE = 10;
	/**
	* The attributes that should be mutated to dates.
	*
	* @var array
	*/
	protected $dates = ['created_at', 'updated_at', 'received_at', 'deleted_at'];

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = true;

	/**
	* Get the sender.
	*
	* @return mixed
	*/
	public function sender()
	{
		return $this->belongsTo('iJobDesk\Models\User', 'sender_id')->withTrashed();
	}

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('type', File::TYPE_MESSAGE);
    }
    
    public function isUnread() {
        $me = Auth::user();

        return !$me->isSuper() && $this->sender_id != $me->id && (!$this->reader_ids || strpos($this->reader_ids, "[$me->id]") === FALSE);
    }

    public function markedAsRead() {
        $me = Auth::user();

        if (!$this->isUnread())
            return false;

        $this->reader_ids .= '[' . $me->id . ']';
        $this->received_at = date('Y-m-d H:i:s');
        $this->save();

        return true;
    }
}