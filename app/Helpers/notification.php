<?php
/**
* @author Brice
* @created Mar 22, 2016
*/

if ( !function_exists('get_notification') ) {
    function get_notification($content, $params) {
        if (!empty($params) && $params != null) {
            foreach ($params as $key => $val)
            {
                // Refunded negotive amount should be positive value.
                if ( is_numeric($val) && $val < 0 ) {
                    $val = abs($val);
                }

                if ( strlen($val) > 50 ) {
                    $val = substr($val, 0, 50) . '...'; 
                }

                if (strpos($key, '@#') !== FALSE) {
                    $content = str_ireplace($key, $val, $content);
                } else {
                    $content = str_ireplace('@#' . $key . '#', $val, $content);
                }
                
                
            }
        }
    	//Replace " with html character
    	$content = str_replace('"', '&quot;', $content);
        return $content;
    }
}

if ( !function_exists('parse_notification') ) {
    /**
    * @created briceyu
    * @update paulz - May 21, 2016
    */
    function parse_notification($notifications, $language, $returns = false) {
        try {
        	foreach ($notifications as $n) {
                // value list language (see `user_notifications`)
                $v_lang = strtolower($language);

                // notification formart language (see `notifications`)
                $f_lang = $v_lang;

                $formats = json_decode($n->content, true);
                if ( !isset($formats[$f_lang])){
                    $f_lang = "EN";
                }

                $values = json_decode($n->notification, true);
                if ( !isset($values[$v_lang]) ) {
                    $v_lang = "EN";
                }

                if (empty($values[$v_lang])) {
                    $values[$v_lang] = [];
                }

                $n->ninfo = iJobDesk\Models\Notification::find($n->notification_id);
                $n->notification = get_notification($formats[$f_lang], $values[$v_lang]);
                $n->params       = $values[$v_lang];
        	}
        } catch ( Exception $e ) {
            error_log('notification.php [parse_notification] Error: ' . $e->getMessage());
        }

        if ($returns)
            return $notifications;
    }
}

/**
 * @author KCG
 * @since June 23, 2017
 * icons and colors for notificatoins for ticket.
 */
if ( !function_exists('ticket_notification_style') ) {
    function ticket_notification_style($slug) {
        $styles = [
            Notification::TICKET_CREATED        => ['fa-plus', '#ff5722'],
            Notification::TICKET_CLOSED         => ['fa-bullhorn', '#89C4F4'],
            Notification::ADMIN_TICKET_ASSIGNED => ['fa-bullhorn', '#c6c6c6'],
        ];
        return $notifications;
    }
}