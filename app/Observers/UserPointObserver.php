<?php
namespace iJobDesk\Observers;

use Auth;

class UserPointObserver {
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
     * @param  UserPoint  $model
     * @return void
     */
    public function saved($model) {
        if ( $model->user ) {
            $model->user->updateRatings();
        }
    }

    /**
     * Handle the event.
     *
     * @param  UserPoint  $model
     * @return void
     */
    public function deleted($model) {
    }
}