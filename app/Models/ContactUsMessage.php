<?php namespace iJobDesk\Models;

use Auth;
use DB;

class ContactUsMessage extends Model {
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'contact_us_messages';

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
                    ->whereRaw("(contact_us_messages.reader_ids NOT LIKE '%,{$user->id},%' OR contact_us_messages.reader_ids IS NULL)")
                    ->where('contact_us_messages.sender_id', '<>', $user->id)
                    ->orderBy('contact_us_messages.created_at', 'DESC');
    }

    private static function getQueryBuilder($user = null) {
        if (!$user)
            $user = Auth::user();

        $query = self::leftJoin('tickets AS tk', 'tk.id', '=', 'contact_us_messages.ticket_id')
                     ->leftJoin('todos AS td', 'td.id', '=', 'contact_us_messages.todo_id')
                     ->join('view_users AS sender', 'sender.id', '=', 'contact_us_messages.sender_id')
                     // Select Clause
                     ->addSelect('contact_us_messages.*')
                     ->addSelect('sender.fullname AS fullname')
                     ->addSelect(DB::raw(self::getColumnIsNew($user) . " AS is_new"))
                     // Where Clause
                     ->where('sender.id', '<>', $user->id);

        if (!$user->isSuper())
            $query->where(function($query) use ($user) {
                      $query->orWhereRaw("(contact_us_messages.ticket_id IS NOT NULL AND tk.admin_id = {$user->id})")
                            ->orWhereRaw("(contact_us_messages.todo_id IS NOT NULL AND (td.assigner_ids LIKE '%[$user->id]%' OR td.creator_id = {$user->id}))");
                  });
                    
        return $query;
    }

    public static function getColumnIsNew($user = null) {
        if (!$user)
            $user = Auth::user();

        return "IF(contact_us_messages.reader_ids LIKE '%{$user->id},%', 0, 1)";
    }

    public function link() {
        $user = Auth::user();

        return route('admin.super.contact_us.detail', ['id' => $this->id]);
    }

    public function isUnread() {
        $user = Auth::user();
        return $this->sender_id != $user->id && ($this->reader_ids == null || strpos($this->reader_ids, ',' . $user->id. ',') === FALSE);
    }

    public function markedAsRead() {
        $user = Auth::user();

        $this->reader_ids .= ',' . $user->id . ',';
        $this->save();
    }
}
