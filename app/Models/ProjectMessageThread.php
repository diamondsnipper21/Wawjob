<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use DB;

use iJobDesk\Models\User;

class ProjectMessageThread extends Model {

    use SoftDeletes;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'project_message_threads';


    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = array('unreads');

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    const MESSAGE_THREAD_PER_PAGE = 10;
    const STATUS_NOT_FAVOURITE   = 0;
    const STATUS_FAVOURITE  = 1;

    /**
    * Get the messages.
    *
    * @return mixed
    */
    public function messages()
    {
        return $this->hasMany('iJobDesk\Models\ProjectMessage', 'thread_id')->orderBy('created_at', 'asc');
    }

	public function message()
    {
        return $this->hasOne('iJobDesk\Models\ProjectMessage', 'thread_id')->orderBy('created_at', 'asc');
    }

    /**
    * Get the application.
    *
    * @return mixed
    */
    public function application()
    {
        return $this->hasOne('iJobDesk\Models\ProjectApplication', 'id', 'application_id');
    }

    /**
    * Get the sender.
    *
    * @return mixed
    */
    public function sender()
    {
        return $this->hasOne('iJobDesk\Models\User', 'id', 'sender_id');
    }

    /**
    * Get the receiver.
    *
    * @return mixed
    */
    public function receiver()
    {
        return $this->hasOne('iJobDesk\Models\User', 'id', 'receiver_id');
    }

    /**
    * Get the count of unread messages.
    *
    * @return mixed
    */
    public function getUnreadsAttribute() {
        $result = DB::select(self::getUnreadColumn($this->id));

        return $result[0]->unreads;
    }

    /**
     * Get the column for count of unread messages for thread_id
     * @return mixed
     */
    public static function getUnreadColumn($thread_id) {
        $user = Auth::user();

        $column = "
            SELECT 
                COUNT(*) AS unreads
            FROM 
                project_messages AS pm 
            WHERE 
                pm.thread_id = $thread_id AND
                sender_id <> $user->id AND
                (reader_ids NOT LIKE '%[$user->id]%' OR reader_ids IS NULL)
        ";

        return $column;
    }

    /**
     * Marked as all message in thread
     */
    public function markedAsRead() {
        $user = Auth::user();

        $messages = ProjectMessage::where('thread_id', $this->id)
                                  ->where('sender_id', '<>', $user->id)
                                  ->get();

        foreach ( $messages as $message ) {
            if (!$message->isUnread())
                continue;

            $message->reader_ids .= "[$user->id]";
            $message->save();
        }

        if ($user->id == $this->sender_id)
            $this->sender_read_at = date('Y-m-d H:i:s');
        else
            $this->receiver_read_at = date('Y-m-d H:i:s');

        $this->save();
    }

    public function unreadsIncludeMine() {
        $user = Auth::user();
        if ($user->id == $this->sender_id)
            $read_at_field = 'sender_read_at';
        else
            $read_at_field = 'receiver_read_at';

        $read_at = $this->$read_at_field;

        return ProjectMessage::where('thread_id', $this->id)
                             ->where(function($query) use ($read_at) {
                                if ($read_at)
                                    $query->where('created_at', '>=', $read_at);
                             })
                             ->count();
    }

    /**
     * All attachments in thread
     */
    public function attachments() {
        $user = Auth::user();

        $files = File::join('project_messages AS pm', function($join) {
                        $join->on('pm.id', '=', 'target_id');
                        $join->on('type', '=', DB::raw("'".File::TYPE_MESSAGE."'"));
                     })
                     ->join('project_message_threads AS pmt', 'pmt.id', '=', 'pm.thread_id')
                     ->where(function($query) use ($user) {
                        $query->where('pmt.sender_id', $user->id)
                              ->orWhere('pmt.receiver_id', $user->id);
                     })
                     ->where('pmt.id', $this->id)
                     ->addSelect('files.*')
                     ->get();

        return $files;
    }

    /**
    * Get the count of total messages.
    *
    * @return mixed
    */
    public function totals()
    {
        return $this->hasMany('iJobDesk\Models\ProjectMessage', 'thread_id')->count();
    }

    /**
     * Check whether users can send/receive in this message room.
     */
    public function canSendMessage() {
        $me = Auth::user();

        if ($me->isAdmin())
            return false;
        
        $application = $this->application;
        $sender = $this->sender;
        $receiver = $this->receiver;
        $project = $application ? $application->project : null;
        $contract = $application ? $application->contract : null;

        $sending_available = $sender && ($this->sender_id == $me->id || $this->receiver_id == $me->id);
        $sending_available = $sending_available && (!$sender->isSuspended() && !$sender->isDeleted());
        $sending_available = $sending_available && $receiver && (!$receiver->isSuspended() && !$receiver->isDeleted());
        $sending_available = $sending_available && $application;
        $sending_available = $sending_available && $project && (!$project->isSuspended());
        $sending_available = $sending_available && (($contract && !$contract->isSuspended()) || !$contract);

        return $sending_available;
    }

    /**
     * Chekc if archived
     */
    public function isArchived() {
        $user = Auth::user();
        
        return $this->is_archived != null && strpos($this->is_archived, "[$user->id]") !== FALSE;
    }

    /**
     * Archived
     */
    public function archived() {
        $user = Auth::user();
        if ($this->isArchived())
            return;

        $this->is_archived .= "[$user->id]";
        $this->save();
    }

    /**
     * UnArchived
     */
    public function unArchived($meOnly=true) {
    	if (!$meOnly) {
    		$this->is_archived = ''; //We are going to move it to inbox for sender & receiver.
        	$this->save();
    	}
    	else {
    		$user = Auth::user();
	        if (!$this->isArchived())
	            return;

	        $this->is_archived = str_replace("[$user->id]", '', $this->is_archived);
        	$this->save();

    	}

        
    }
}