<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;

class TicketComment extends Model {

	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'ticket_comments';

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

    const TICKETCOMMENT_PER_PAGE = 10;

    public $timestamps = true;

    function __construct() {
        parent::__construct();
    }

	/**
	* Get the user who commented.
	*
	* @return mixed
	*/
	public function sender()
	{
		return $this->hasOne('iJobDesk\Models\User', 'id', 'sender_id');
	}

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->whereRaw('(type = ' . File::TYPE_TICKET_COMMENT . ' OR type = ' . File::TYPE_ID_VERIFICATION . ')');
    }

    public function ticket() {
        return $this->hasOne('iJobDesk\Models\Ticket', 'id', 'ticket_id');
    }
    
    public function isUnread() {
        $me = Auth::user();

        return (!$this->reader_ids || strpos($this->reader_ids, '[' . $me->id . ']') === FALSE) && $this->sender_id != $me->id;
    }

    /**
     * Marked As read
     */
    public function markedAsRead() {
        $me = Auth::user();

        if (!$this->isUnread())
            return false;

        $this->reader_ids .= '[' . $me->id . ']';
        $this->save();

        return true;
    }

    public static function unreadsCount($id = 't.id') {
        $user = Auth::user();

        return self::join('tickets AS t', 't.id', '=', 'ticket_comments.ticket_id')
                   ->whereRaw("t.id = $id")
                   ->where('t.user_id', $user->id)
                   ->where('ticket_comments.sender_id', '<>', $user->id)
                   ->whereRaw("(ticket_comments.reader_ids IS NULL OR ticket_comments.reader_ids NOT LIKE '%[$user->id]%')")
                   ->count();
    }
}
