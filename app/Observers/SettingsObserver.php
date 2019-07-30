<?php
/**
 * @author KCG
 * @since Jan 29, 2018
 */

namespace iJobDesk\Observers;

use Auth;

use iJobDesk\Models\User;
use iJobDesk\Models\Settings;
use iJobDesk\Models\Notification;
use iJobDesk\Models\EmailTemplate;

class SettingsObserver {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Settings
     * @return void
     */
    public function saving($setting) {
	}
}