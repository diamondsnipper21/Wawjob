<?php namespace iJobDesk\Models;


use Config;
use App;

class UserNotification extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_notifications';

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['notified_at', 'read_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;



    /**
    * Get the notification record associated with the user notification.
    *
    * @return mixed
    */
    public function notificate()
    {
        return $this->belongsTo('iJobDesk\Models\Notification', 'notification_id');
    }

    /**
    * Read a notification.
    * @return bool
    */
    public static function read($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            return self::where(['id' => $id])
            ->update(['read_at' => $now]);
        }
        catch(Exception $e) {
            return false;
        }
    }

    /**
    * Delete notification(s).
    * @return bool
    */
    public function del($id, $is_notification = false)
    {
        try {
            if ($is_notification) {
                return self::where(['notification_id' => $id])
                ->delete();
            } else {
                return self::where(['id' => $id])
                ->delete();  
            }

        }
        catch(Exception $e) {
            return false;
        }
    }

    /**
    * Get the system notifications for current user.
    * @return mixed
    */
    public static function getSystem($user_id) {
        return self::getAll($user_id, [Notification::NOTIFICATION_TYPE_SYSTEM], false, false, true);
    }

    /**
    * Get the unread notifications for current user.
    * @return mixed
    */
    public static function getUnread($user_id, $unlimit = false) {
        $user = User::find($user_id);

        if ($user->isAdmin())
            return self::getAll($user_id, [Notification::NOTIFICATION_TYPE_SYSTEM, Notification::NOTIFICATION_TYPE_NORMAL], false, false, true);
        else
            return self::getAll($user_id, [Notification::NOTIFICATION_TYPE_NORMAL], false, false, true, $unlimit?false:5, true);
    }

    /**
    * Get the notifications for current user.
    * @return mixed
    */
    public static function getAll($user_id, $types = [Notification::NOTIFICATION_TYPE_NORMAL], $is_paginate = true, $can_parse = false, $unread = false, $limit = false, $count = false) {
        try {
            $notification = self::join('notifications AS n', 'n.id', '=', 'user_notifications.notification_id')
                                ->where('receiver_id', $user_id)
                                ->where(function($query){
                                    $now = date('Y-m-d H:i:s');
                                    $query->orWhere('user_notifications.valid_date', NULL)
                                          ->orWhere('user_notifications.valid_date', '>', $now);
                                })
                                ->select('user_notifications.*', 'n.content')
                                ->whereNull('n.deleted_at');

            if ($types)
                $notification->whereIn('n.type', $types);

            if ($unread) {
                $notification->whereNull('read_at');
            }

            if ( $count ) {
                $count = $notification->count();
            }

            if (!$can_parse) {
                $notification->orderBy('notified_at', 'desc');

                if ( $limit ) {
                    $notification = $notification->paginate($limit);
                } else {
                    if ($is_paginate) {
                        $per_page = Config::get('settings.frontend.per_page');
                        $notification = $notification->paginate($per_page);
                    } else {
                        $notification = $notification->get();
                    }
                }
                
                parse_notification($notification, App::getLocale()); //if we use the multi languages, change 'EN' with current language code
            }

            if ( $count ) {
                return [$notification, $count];
            }

            return $notification;
        }
        catch(Exception $e) {
            return [];
        }
    }

    /**
    * Add the notification for users.
    * @return mixed
    */
    public static function add($notification_id, $notification, $sender_id, $receiver_id, $valid_date = null) {
        $id = 0;
        try {
            $now = date('Y-m-d H:i:s');
            $id = self::insertGetId(['notification_id' => $notification_id, 'notification' => $notification, 'sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'notified_at' => $now, 'valid_date' => $valid_date]);
        } catch(Exception $e) {
            Log::error("UserNotification@add:[$notification_id][$sender_id][$receiver_id]");
        }

        return $id;
    }

    /**
     * @author KCG
     * @since June 26, 2017
     * Ticket for notification
     */
    public function ticket() {
        return Ticket::findOrFail($this->params['ticket_id']);
    }

    /**
     * @author KCG
     * @since Dec 29, 2017
     * icons by priority.
     */
    public static function iconsByPriority() {
        return [
            Notification::PRIORITY_URGENT   => 'fa-bolt',
            Notification::PRIORITY_NORMAL   => 'fa-bell-o',
            Notification::PRIORITY_LOW      => 'fa-bullhorn'
        ];
    }

    public static function iconByPriority($priority) {
        return self::iconsByPriority()[$priority];
    }
}