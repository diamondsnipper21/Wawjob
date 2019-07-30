<?php namespace iJobDesk\Models;

use Auth;
use DB;

use iJobDesk\Models\Ticket;
use iJobDesk\Models\Todo;
use iJobDesk\Models\ContactUs;

class AdminMessage extends Model {
    const MESSAGES_PER_PAGE = 10;

    const MESSAGE_TYPE_TICKET   = 1;
    const MESSAGE_TYPE_TODO     = 2;
    const MESSAGE_TYPE_DISPUTE  = 3;
    const MESSAGE_TYPE_CONTACT  = 4;
    const MESSAGE_TYPE_THREAD   = 5;
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'admin_messages';

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

    /**
    * Get the sender.
    *
    * @return mixed
    */
    public function sender() {
        return $this->belongsTo('iJobDesk\Models\Views\ViewUser', 'sender_id');
    }

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('type', File::TYPE_ADMIN_MESSAGE);
    }

    public function isTicketMessage() {
        return $this->message_type == self::MESSAGE_TYPE_TICKET;
    }

    public function isCommonMesssage() {
        return $this->message_type == self::MESSAGE_TYPE_THREAD;
    }

	public function isTodoMessage() {
		return $this->message_type == self::MESSAGE_TYPE_TODO;
	}

	public function isDisputeMessage() {
		return $this->message_type == self::MESSAGE_TYPE_DISPUTE;
	}

	public function isContactMessage() {
		return $this->message_type == self::MESSAGE_TYPE_CONTACT;
	}

    public static function getOptions($type) {
        if ($type == 'type') {
            return [
                'Contact'   => self::MESSAGE_TYPE_CONTACT,
                'Ticket'    => self::MESSAGE_TYPE_TICKET,
                'Todo'      => self::MESSAGE_TYPE_TODO,
                'Dispute'   => self::MESSAGE_TYPE_DISPUTE,
                'Common'    => self::MESSAGE_TYPE_THREAD
            ];
        }

        return [];
    }

    /**
     * Get unread messages
     */
    public static function getUnread($user = null) {
        return self::getUnreadQueryBuilder()
                   ->get();
    }

    public static function getAll($user = null) {
        return self::getUnreadQueryBuilder($user);
    }

    public static function getUnreadQueryBuilder($user = null) {
        if (!$user)
            $user = Auth::user();

        return  self::getQueryBuilder($user)
                    ->whereRaw("(admin_messages.reader_ids NOT LIKE '%[{$user->id}]%' OR admin_messages.reader_ids IS NULL)")
                    ->where('admin_messages.sender_id', '<>', $user->id)
                    ->orderBy('admin_messages.created_at', 'DESC');
    }

    public static function getQueryBuilder($user = null) {
        if (!$user)
            $user = Auth::user();

        $query = self::leftJoin('tickets AS tk', function($join) {
                        $join->on('tk.id', '=', 'target_id');
                        $join->on('message_type', '=', DB::raw("'".self::MESSAGE_TYPE_TICKET."'"));
                     })
                     ->leftJoin('todos AS td', function($join) {
                        $join->on('td.id', '=', 'target_id');
                        $join->on('message_type', '=', DB::raw("'".self::MESSAGE_TYPE_TODO."'"));
                     })
                     ->leftJoin('contact_us AS cu', function($join) {
                        $join->on('cu.id', '=', 'target_id');
                        $join->on('message_type', '=', DB::raw("'".self::MESSAGE_TYPE_CONTACT."'"));
                     })
                     ->leftJoin('admin_message_threads AS amt', function($join) {
                        $join->on('amt.id', '=', 'target_id');
                        $join->on('message_type', '=', DB::raw("'".self::MESSAGE_TYPE_THREAD."'"));
                     })
                     ->leftJoin('view_users AS sender', 'sender.id', '=', 'sender_id')
                     // Select Clause
                     ->addSelect('admin_messages.*')
                     ->addSelect(DB::raw('IF(cu.id IS NOT NULL, cu.fullname, sender.fullname) AS fullname'))
                     ->addSelect(DB::raw(self::getColumnIsNew($user) . " AS is_new"))
                     // Where Clause
                     ->whereRaw("(sender_id <> $user->id OR (sender_id IS NULL AND message_type=".self::MESSAGE_TYPE_CONTACT.") OR (amt.creator_id = $user->id AND message_type=".self::MESSAGE_TYPE_THREAD."))");

        if (!$user->isSuper())
            $query->where(function($query) use ($user) {
                      $query->orWhereRaw("tk.admin_id = {$user->id} OR tk.assigner_id = {$user->id}")
                            ->orWhereRaw("td.assigner_ids LIKE '%[$user->id]%' OR td.creator_id = {$user->id}");
                  })
                  ->whereNull('cu.id')
                  ->whereNull('amt.id');
 
        return $query;
    }

    public static function getColumnIsNew($user = null) {
        if (!$user)
            $user = Auth::user();

        return "IF(admin_messages.reader_ids LIKE '%[$user->id]%', 0, 1)";
    }

    public function link() {
        $user = Auth::user();

        $url = '';
        if ($this->message_type == self::MESSAGE_TYPE_TODO)
            $url .= route('admin.'.($user->isSuper()?'super':'ticket').'.todo.detail', ['id' => $this->target_id, 'msg_id' => $this->id]);
        if ($this->message_type == self::MESSAGE_TYPE_TICKET)
            $url .= route('admin.'.($user->isSuper()?'super':'ticket').'.ticket.msg_admin', ['id' => $this->target_id, 'msg_id' => $this->id]);
        if ($this->message_type == self::MESSAGE_TYPE_CONTACT)
            $url .= route('admin.super.contact_us.detail', ['id' => $this->target_id, 'msg_id' => $this->id]);
        if ($this->message_type == self::MESSAGE_TYPE_THREAD)
            $url .= route('admin.super.thread.detail', ['id' => $this->target_id, 'msg_id' => $this->id]);

        // $url .= '#message_'.$this->id;
        return $url;
    }

    public function isUnread() {
        $user = Auth::user();
        return $this->sender_id != $user->id && ($this->reader_ids == null || strpos($this->reader_ids, '[' . $user->id. ']') === FALSE);
    }

    public function ticket() {
        return $this->hasOne('iJobDesk\Models\Ticket', 'id', 'target_id');
    }

    public function todo() {
        return $this->hasOne('iJobDesk\Models\Todo', 'id', 'target_id');
    }

    public function contact_us() {
        return $this->hasOne('iJobDesk\Models\ContactUs', 'id', 'target_id');
    }

    public function thread() {
        return $this->hasOne('iJobDesk\Models\AdminMessageThread', 'id', 'target_id');
    }

    public function markedAsRead() {
        $me = Auth::user();

        if (!$this->isUnread())
            return false;

        $this->reader_ids .= '[' . $me->id . ']';
        $this->save();

        return true;
    }

    /**
     * Return query which returns count of news message
     */
    public static function getColumnNewCount() {
        $user = Auth::user();

        $query = "
            SELECT 
                COUNT(*) 
            FROM 
                admin_messages AS am 
            WHERE 
                am.target_id=view_todos.id AND 
                am.message_type = ".self::MESSAGE_TYPE_TODO." AND 
                am.sender_id != $user->id AND 
                (am.reader_ids NOT LIKE '%[$user->id]%' OR am.reader_ids IS NULL)";

        return $query;

    }
}
