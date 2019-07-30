<?php namespace iJobDesk\Models;

use DB;
use Auth;
use App;
use Config;

use iJobDesk\Models\UserNotification;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\AdminMessage;

class Ticket extends Model {

    protected $table = 'tickets';

    const FIRST_COMMENT_LOADING_COUNT = 20;
    const FIRST_TICKET_LOADING_COUNT = 5;
    /**
    * Ticket types
    */
    const TYPE_ACCOUNT = 1;
    const TYPE_PAYMENT = 2;
    const TYPE_DISPUTE = 3;
    const TYPE_SUSPENSION = 5;
    const TYPE_OTHER = 6;
    const TYPE_ID_VERIFICATION = 7;

    /**
    * Ticket priorities
    */
    const PRIORITY_HIGH = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_LOW = 3;

    /**
    * Ticket statuses
    */
    const STATUS_OPEN = 1;
    const STATUS_ASSIGNED = 2;
    const STATUS_SOLVED = 3;
    const STATUS_CLOSED = 4;

    /**
     * Ticket Result including dispute
     */
    const RESULT_SOLVED_SUCCESS     = 1;
    const RESULT_PUNISH_BUYER       = 2;
    const RESULT_PUNISH_FREELANCER  = 3;
    const RESULT_SOLVED_THEMSELVES  = 4;
    const RESULT_OTHER              = 5;
    const RESULT_CREATED_BY_ACCIDENT= 6;

    const RESULT_IDV_SUCCESS        = 7;
    const RESULT_IDV_FAILURE        = 8;
    const RESULT_IDV_NORMAL         = 9;

    /**
    * Get the user which create ticket.
    *
    * @return mixed
    */
    public function user() {
        return $this->belongsTo('iJobDesk\Models\User', 'user_id')->withTrashed();
    }

    /**
    * Get the assigner
    *
    * @return mixed
    */
    public function assigner() {
        return $this->belongsTo('iJobDesk\Models\User', 'assigner_id')->withTrashed();
    }

    /**
    * Get the admin which process ticket.
    *
    * @return mixed
    */
    public function admin() {
        return $this->belongsTo('iJobDesk\Models\User', 'admin_id')->withTrashed();
    }

    /**
    * Get the receiver when ticket has been created by admin
    *
    * @return mixed
    */
    public function receiver() {
        return $this->belongsTo('iJobDesk\Models\User', 'receiver_id')->withTrashed();
    }

    /**
    * Get the contract which is related to this ticket.
    *
    * @return mixed
    */
    public function contract() {
        return $this->belongsTo('iJobDesk\Models\Contract', 'contract_id');
    }

    /**
    * Get the comments associated with the ticket.
    *
    * @return mixed
    */
    public function messages() {
        return $this->hasMany('iJobDesk\Models\TicketComment', 'ticket_id');
    }

    /**
    * Get the message to super admin associated with the ticket.
    *
    * @return mixed
    */
    public function admin_messages() {
        return $this->hasMany('iJobDesk\Models\AdminMessage', 'target_id')
                    ->where('message_type', AdminMessage::MESSAGE_TYPE_TICKET);
    }

    /**
    * @author paulz
    * @created Mar 7, 2016
    */
    public function comments_count()
    {
        $n = DB::table('ticket_comments')
               ->where('ticket_id', '=', $this->id)
               ->count();

        return $n;
    }

    /**
    * Returns array for each <select> tag
    *
    * @author paulz
    * @created Mar 7, 2016
    */
    public static function getOptions($type)
    {
        switch ($type) {
            case "status":
                $options = [
                    "Open"     => self::STATUS_OPEN,
                    "Assigned" => self::STATUS_ASSIGNED,
                    "Solved"   => self::STATUS_SOLVED,
                    // "Closed"   => self::STATUS_CLOSED
                ];
                break;

            case "priority":
                $options = [
                    "High"     => self::PRIORITY_HIGH,
                    "Medium"   => self::PRIORITY_MEDIUM,
                    "Low"      => self::PRIORITY_LOW
                ];
                break;

            case "type":
                $options = [
                    "Account Problem"      => self::TYPE_ACCOUNT,
                    "Payment Problem"      => self::TYPE_PAYMENT,
                    "Dispute"              => self::TYPE_DISPUTE,
                    "Suspension"           => self::TYPE_SUSPENSION,
                    "ID Verification"      => self::TYPE_ID_VERIFICATION,
                    "Other"                => self::TYPE_OTHER
                ];
                break;

            case "create_type":
                $options = [
                    'account_problem'      => self::TYPE_ACCOUNT,
                    'payment_problem'      => self::TYPE_PAYMENT,
                    'suspension'           => self::TYPE_SUSPENSION,
                    'id_verification'      => self::TYPE_ID_VERIFICATION,
                    'other'                => self::TYPE_OTHER
                ];

                break;

            case "common_result":
                $options = [
                    self::RESULT_SOLVED_SUCCESS => 'Solved Successfully',
                    self::RESULT_OTHER          => 'Other'
                ];
                break;
            case "id_verification_result":
                $options = [
                    self::RESULT_IDV_SUCCESS => 'Yes, approve and restore account',
                    self::RESULT_IDV_FAILURE => 'No, deactivate permanently',
                    self::RESULT_IDV_NORMAL  => 'Restore account only'
                ];

                break;
            case "dispute_result":
                $options = [
                    // self::RESULT_SOLVED_SUCCESS     => 'Solved Successfully',
                    self::RESULT_SOLVED_THEMSELVES  => 'Solved Personally (Themselves)',
                    self::RESULT_PUNISH_BUYER       => 'Punishment to Buyer',
                    self::RESULT_PUNISH_FREELANCER  => 'Punishment to Freelancer'
                ];
                break;
            case "dispute_cancel_result":
                $options = [
                    self::RESULT_CREATED_BY_ACCIDENT=> 'Created by accident',
                    self::RESULT_SOLVED_THEMSELVES  => 'Solved Successfully'
                ];
                break;
            case "result":
                $options = [
                    self::RESULT_SOLVED_SUCCESS     => 'Solved Successfully',
                    self::RESULT_CREATED_BY_ACCIDENT=> 'Created by accident',
                    self::RESULT_SOLVED_THEMSELVES  => 'Solved Personally (Themselves)',
                    self::RESULT_PUNISH_BUYER       => 'Punishment to Buyer',
                    self::RESULT_PUNISH_FREELANCER  => 'Punishment to Freelancer',
                    self::RESULT_OTHER              => 'Other',
                    self::RESULT_IDV_SUCCESS        => 'Yes, approve and restore account',
                    self::RESULT_IDV_FAILURE        => 'No, deactivate permanently',
                    self::RESULT_IDV_NORMAL         => 'Restore account only'
                ];
                break;

            default:
                $options = [];
        }

        return $options;
    }

    /**
    * Converts constant to human-readable string
    *
    * @author paulz
    * @created Mar 9, 2016
    *
    * @param: $type - Field type
    * @param: $value - Integer value to be converted to string
    */
    public static function toString($type, $value)
    {
        $options = self::getOptions($type);
        if ( !$options ) {
            return false;
        }

        if ( !isset($options[$value]) ) {
            $options = array_flip($options);
            if ( !isset($options[$value]) ) {
                return false;
            }
        }

        return $options[$value];
    }

    /**
    * Get the files associated with the record.
    *
    * @return mixed
    */
    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('type', File::TYPE_TICKET);
    }

    /**
     * @author KCG
     * @since June 11, 2017
     * Get count of opening tickets
     */
    public static function openingCounts() {
        return Ticket::whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED])->count();
    }

    /**
     * @author KCG
     * @since June 11, 2017
     * Get count of critical tickets
     */
    public static function highCounts() {
        return Ticket::where('priority', self::PRIORITY_HIGH)->count();
    }

    /**
     * @author KCG
     * @since June 11, 2017
     * Get count of my tickets for ticket management
     */
    public static function ownCounts() {
        $user = Auth::user();
        return Ticket::where('admin_id', $user->id)->count();
    }

    /**
     * @author KCG
     * @since June 11, 2017
     * Get count of my tickets for ticket management
     */
    public static function archivedCounts() {
        return Ticket::whereIn('status', [Ticket::STATUS_SOLVED])->count();
    }

    /**
     * @author KCG
     * @since June 11, 2017
     * All notifications
     */
    public static function notifications($limit = 0, $user = null) {
        if (!$user)
            $user = Auth::user();

        $notifications = UserNotification::select(['t.type', 'n.*', 'user_notifications.*'])
                                         ->join('notifications AS n', 'n.id', '=', 'notification_id')
                                         ->join('tickets AS t', 't.id', '=', 'data')
                                         ->whereIn('slug', [
                                                Notification::TICKET_CREATED, 
                                                Notification::TICKET_CLOSED])
                                         ->where('receiver_id', $user->id)
                                         ->orderBy('notified_at', 'DESC');

        if (!empty($limit)) {
            $notifications = $notifications->take($limit)->get();
            parse_notification($notifications, App::getLocale());
        }

        return $notifications;
    }

    /**
     * @author KCG
     * @since June 22, 2017
     * My tickets for ticket manager.
     */
    public static function ownTickets($limit = 0, $user = null) {
        $user = Auth::user();

        $tickets = Ticket::where('admin_id', $user->id)
                         ->whereNotIn('status', [self::STATUS_SOLVED, self::STATUS_CLOSED]);
        if (empty($limit))
            return $tickets->get();
            
        return $tickets->take($limit)->get();
    }

    /**
     * @author KCG
     * @since June 22, 2017
     * Colors by each type. This data will be used graphs and buttons
     */
    public static function colorsByType() {
        return [
            self::TYPE_ACCOUNT => 'default',
            self::TYPE_PAYMENT => 'danger',
            self::TYPE_SUSPENSION => 'warning',
            self::TYPE_DISPUTE => 'primary',
            self::TYPE_ID_VERIFICATION => 'id-verification', // link
            self::TYPE_OTHER => 'active'
        ];
    }

    public static function colorByType($type) {
        return self::colorsByType()[$type];
    }

    /**
     * @author KCG
     * @since June 26, 2017
     * icons by each priority.
     */
    public static function iconsByType() {
        return [
            self::TYPE_ACCOUNT => 'fa-user', // user
            self::TYPE_DISPUTE => 'fa-legal', // legal
            self::TYPE_OTHER => 'fa-info', // info
            self::TYPE_SUSPENSION => 'fa-link', // link
            self::TYPE_ID_VERIFICATION => 'fa-id-card-o', // link
            self::TYPE_PAYMENT => 'fa-credit-card', // credit-card
        ];
    }

    public static function iconByType($type) {
        return self::iconsByType()[$type];
    }

    /**
     * @author KCG
     * @since July 6, 2017
     * icons by each priority.
     */
    public static function iconsByPriority() {
        return [
            self::PRIORITY_HIGH => 'fa-bolt', // success
            self::PRIORITY_MEDIUM => 'fa-warning', // info
            self::PRIORITY_LOW => 'fa-clock-o'
        ];
    }

    public static function iconByPriority($pritority) {
        return self::iconsByPriority()[$pritority];
    }

    /**
     * @author KCG
     * @since June 26, 2017
     * colors by each priority.
     */
    public static function colorsByPriority() {
        return [
            self::PRIORITY_HIGH => 'danger',
            self::PRIORITY_MEDIUM => 'success',
            self::PRIORITY_LOW => 'info',
        ];
    }

    public static function colorByPriority($pritority) {
        return self::colorsByPriority()[$pritority];
    }

    public static function lineChartOptions() {
        $types = self::getOptions('type');
        $options = [];

        foreach ($types as $key => $value) {
            $options[] = [
                "bullet" => "square",
                "bulletBorderAlpha" => 1,
                "bulletBorderThickness" => 1,
                "fillAlphas" => 0.3,
                // "fillColorsField" => "lineColor" . $key,
                "legendValueText" => "[[value]]",
                // "lineColorField" => "lineColor" . $key,
                "title" => $key,
                "valueField" => $key
            ];
        }

        return $options;
    }

    /**
     * Send notification to admin
     */
    public function sendNotification($sender_id, $type) {
        $receivers = [];

        if ($this->admin_id)
            $receivers[] = $this->admin;

        if ($this->assigner_id)
            $receivers[] = $this->assigner;

        if (!$receivers)
            $receivers = User::getAdminUsers();

        foreach ($receivers as $receiver) {
            Notification::send($type, $sender_id, $receiver->id, ['ticket_id' => $this->id]);
        }

        return $this;
    }

    public static function getColumnUnreadAdminMessage($id) {
        $me = Auth::user();

        return "
            SELECT 
                COUNT(*) 
            FROM 
                admin_messages AS am 
            WHERE 
                am.target_id = $id AND 
                am.message_type = ".AdminMessage::MESSAGE_TYPE_TICKET." AND
                (am.reader_ids NOT LIKE '%[{$me->id}]%' OR am.reader_ids IS NULL) AND
                (am.sender_id != $me->id)
        ";
    }

    public static function getColumnIsNew($table = null) {
        $me = Auth::user();

        return "
            IF(
                (
                    ($table.assigner_id IS NULL AND $table.admin_id IS NULL) OR                 -- unassigned to anyone
                    ($table.assigner_id = $me->id OR $table.admin_id = $me->id)
                ) AND 
                (
                    $table.reader_ids IS NULL OR                                                -- unread ticket
                    $table.reader_ids NOT LIKE '%[$me->id]%' 
                )
            , 1, 0)
        ";
    }

    public static function getColumnUnreadMessage($id) {
        $me = Auth::user();

        return "
            SELECT 
                COUNT(*) 
            FROM 
                ticket_comments AS tc 
            WHERE 
                tc.ticket_id = $id AND 
                (tc.reader_ids NOT LIKE '%[{$me->id}]%' OR tc.reader_ids IS NULL) AND
                tc.sender_id != $me->id
        ";

        return $query;
    }

    /**
     * Count of tickets as new
     * 1. unread ticket comments
     * 2. unread admin messages
     */
    public static function getCountUnreadMsg($id = null) {
        $me = Auth::user();

        if (empty($id))
            $id = 't.id';

        $query = "
            SELECT
                COUNT(*) AS count
            FROM
                tickets AS t
            WHERE
                (
                    /* (".self::getColumnIsNew('t').") = 1 OR */                            -- is new
                    (
                        t.admin_id = {$me->id} AND 
                        (".self::getColumnUnreadMessage($id).") != 0                        -- unread ticket comments
                    ) OR
                    (
                        (t.admin_id = {$me->id} OR ".($me->isSuper()?'1':'0')."=1) AND 
                        (".self::getColumnUnreadAdminMessage($id).") != 0                   -- unread admin messages
                    )                       
                ) AND
                t.id = $id AND
                t.status NOT IN (".self::STATUS_SOLVED.", ".self::STATUS_CLOSED.")
        ";

        $result = DB::select($query);

        return $result[0]->count;
    }

    /**
     * New Ticket
     */
    public static function getNewCount() {
        $me = Auth::user();

        $id     = 't.id';
        $table  = 't';

        $query = "
            SELECT
                COUNT(*) AS count
            FROM
                tickets AS t
            WHERE
                (
                    (
                        IF(
                            (
                                $table.reader_ids IS NULL OR                       -- unread ticket
                                $table.reader_ids NOT LIKE '%[$me->id]%' 
                            ) AND
                            (
                                ($table.assigner_id IS NULL AND $table.admin_id IS NULL)    -- unassigned to anyone
                            )
                        , 1, 0)
                    ) = 1
                ) AND
                t.id = $id AND
                t.status NOT IN (".self::STATUS_SOLVED.", ".self::STATUS_CLOSED.")
        ";

        if (!$me->isSuper())
            $query .= ' AND type <> ' . Ticket::TYPE_ID_VERIFICATION;

        $result = DB::select($query);

        return $result[0]->count;
    }

    /**
     * Unassigned Tickets
     */
    public static function getUnassignedCount() {
        $me = Auth::user();

        $id     = 't.id';
        $table  = 't';

        $query = "
            SELECT
                COUNT(*) AS count
            FROM
                tickets AS t
            WHERE
                (
                    (
                        IF(
                            (
                                ($table.assigner_id IS NULL AND $table.admin_id IS NULL)    -- unassigned to anyone
                            )
                        , 1, 0)
                    ) = 1
                ) AND
                t.id = $id AND
                t.status NOT IN (".self::STATUS_SOLVED.", ".self::STATUS_CLOSED.")
        ";

        if (!$me->isSuper())
            $query .= ' AND type <> ' . Ticket::TYPE_ID_VERIFICATION;

        $result = DB::select($query);

        return $result[0]->count;
    }
    
    public function isUnread() {
        $me = Auth::user();

        return !$this->reader_ids || strpos($this->reader_ids, "[$me->id]") === FALSE;
    }

    public function markedAsRead() {
        $me = Auth::user();

        if (!$this->isUnread())
            return false;

        $this->reader_ids .= '[' . $me->id . ']';
        $this->save();

        return true;
    }

    public function isDispute() {
        return $this->type == self::TYPE_DISPUTE && $this->contract;
    }

    public function isClosed() {
        return $this->status == self::STATUS_SOLVED || $this->status == self::STATUS_CLOSED;
    }

    public function isUnreadByAdmin() {
        return $this->getUnreadAdminMessages() > 0;
    }

    public function isAssigned() {
        return $this->admin_id && $this->assigner_id;
    }

    public function getUnreadAdminMessages() {
        return AdminMessage::getUnreadQueryBuilder()
                           ->where('tk.id', $this->id)
                           ->count();
    }

    public static function hasIDVerification($user) {
        return Ticket::where('user_id', $user->id)
                     ->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_ASSIGNED])
                     ->where('type', Ticket::TYPE_ID_VERIFICATION)
                     ->exists();
    }

    public static function enableStatusChanged($ticket, $me) {
        $attributes = '';

        if ( $ticket->status != self::STATUS_SOLVED && $ticket->type != self::TYPE_DISPUTE ) {
            if ( $me->isSuper() ) {
                $attributes .= ' data-status-' . self::STATUS_SOLVED . '=true';
            } else {
                if ( $ticket->admin_id == $me->id ) {
                    if ( $ticket->type != self::TYPE_DISPUTE ) {
                        $attributes .= ' data-status-' . self::STATUS_SOLVED . '=true';
                    }
                }
            }

            return $attributes;
        }

        return $attributes;
    }

    public function file_type() {
        if ($this->type == TIcket::TYPE_ID_VERIFICATION)
            return File::TYPE_ID_VERIFICATION;
        else
            return File::TYPE_TICKET_COMMENT;
    }
}