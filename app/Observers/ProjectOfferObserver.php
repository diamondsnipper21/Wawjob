<?php
/**
 * @author KCG
 * @since Jan 29, 2018
 */

namespace iJobDesk\Observers;

use Auth;

use iJobDesk\Models\ProjectOffer;
use iJobDesk\Models\UserIgnoredWarning;

class ProjectOfferObserver {
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
     * @param  ProjectOffer  $offer
     * @return void
     */
    public function saved($offer) {
    	$user = Auth::user();

    	if (!$user->isSuper()) {
    		if ($offer->isDirty('status') && $offer->status == ProjectOffer::STATUS_NORMAL) {
				$user->removeIgnoredWarnings(UserIgnoredWarning::TYPE_RECEIVED_OFFER, $offer->id);
    		}
    	}
	}
}