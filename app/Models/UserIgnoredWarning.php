<?php namespace iJobDesk\Models;

/**
 * @author KCG
 * @since Jan 28, 2018
 */

use Illuminate\Database\Eloquent\SoftDeletes;

use Session;
use Auth;
use Log;

class UserIgnoredWarning extends Model {

	/**
   	* The table associated with the model.
   	*
   	* @var string
   	*/
	protected $table = 'user_ignored_warnings';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    /* Type */
    const TYPE_SUSPENDED                = 1;
    const TYPE_FINANCIAL_SUSPENDED      = 2;
    const TYPE_LEAVE_FEEDBACK           = 3;
    const TYPE_CHANGED_MILESTONE        = 4;
    const TYPE_RECEIVED_OFFER           = 5;
    const TYPE_LOGIN_BLOCK              = 6;

    public static function msgTypes() {
        return [
            self::TYPE_SUSPENDED            => 'danger',
            self::TYPE_FINANCIAL_SUSPENDED  => 'danger',
            self::TYPE_LEAVE_FEEDBACK       => 'warning',
            self::TYPE_CHANGED_MILESTONE    => 'warning',
            self::TYPE_RECEIVED_OFFER       => 'warning',
            self::TYPE_LOGIN_BLOCK          => 'danger'
        ];
    }

    public static function add_warnings() {
        $user = Auth::user();

        if (!$user)
            return;

        Session::forget('warnings');

        // USER SUSPENDED: User is financial suspended
        if ( $user->status == User::STATUS_SUSPENDED /*&& !$user->isIgnoredWarning(UserIgnoredWarning::TYPE_SUSPENDED)*/) {
            $message = trans('auth.warning_account_suspended');
            add_warning($message, UserIgnoredWarning::TYPE_SUSPENDED);
        }

        // USER FINANCIAL SUSPENDED: User is suspended
        if ( $user->status == User::STATUS_FINANCIAL_SUSPENDED /*&& !$user->isIgnoredWarning(UserIgnoredWarning::TYPE_FINANCIAL_SUSPENDED)*/) {
            $message = trans('auth.warning_financial_account_suspended');
            add_warning($message, UserIgnoredWarning::TYPE_FINANCIAL_SUSPENDED);
        }

        // USER LOGIN BLOCKEDL User is blocked to login.
        if ( $user->isLoginBlocked() && !$user->isIgnoredWarning(UserIgnoredWarning::TYPE_LOGIN_BLOCK)) {
            $message = trans('auth.warning_login_blocked_suspended');
            add_warning($message, UserIgnoredWarning::TYPE_LOGIN_BLOCK);
        }

        // LEAVE FEEDBACK: Check if ended contract exist and doesn't leave feedback.
        if (!$user->isAdmin()) {
            $closed_contracts = Contract::getContracts([
                        'status'        => Contract::STATUS_CLOSED, 
                        'buyer_id'      => $user->isBuyer()?$user->id:null,
                        'contractor_id' => $user->isFreelancer()?$user->id:null
            ]);

            foreach ($closed_contracts as $contract) {
                if (!$contract->canLeaveFeedback($user))
                    continue;

                if ($user->isIgnoredWarning(UserIgnoredWarning::TYPE_LEAVE_FEEDBACK, $contract->id))
                    continue;

                if (!$contract->canLeaveFeedback())
                    continue;

                $message = trans('contract.contract_has_been_ended', ['title' => '<a href="' . _route('contract.contract_view', ['id' => $contract->id]) .'">"' . $contract->title . '"</a>']) . ' ' . trans('common.click') . ' <a href="' . route('contract.feedback', ['id' => $contract->id]) .'">' . trans('common.here') . '</a> ' .  trans('contract.to_leave_feedback');
                add_warning($message, UserIgnoredWarning::TYPE_LEAVE_FEEDBACK, $contract->id);
            }
        }

        // CHANGED MILESTONE: Check whether there are changed milestones...
        foreach ( $user->changedContractMilestones() as $contract ) {
            if ($user->isIgnoredWarning(UserIgnoredWarning::TYPE_CHANGED_MILESTONE, $contract->id))
                continue;

            $message = trans('contract.milestones_have_been_changed') . ' ' . trans('common.click') . ' ' . '<a href="' . _route('contract.contract_view', ['id' => $contract->id]) . '">' . trans('common.here') . '</a> ' . trans('contract.to_check_the_details');
            add_warning($message, UserIgnoredWarning::TYPE_CHANGED_MILESTONE, $contract->id);
        }

        // OFFER::Check if you received offer from buyers
        foreach ( $user->offers() as $offer ) {
            if ($user->isIgnoredWarning(UserIgnoredWarning::TYPE_RECEIVED_OFFER, $offer->id))
                continue;

            $title = $offer->title;
            if ( mb_strlen($title) > 50 )
                $title = mb_substr($title, 0, 50, 'UTF-8') . '...';

            $message = trans('job.you_have_received_an_offer', ['title' => $title]) . ' ' . trans('common.click') . ' <a href="' . route('job.apply_offer', ['id' => $offer->id]) . '">' . ' ' . trans('common.here') . '</a> ' . trans('contract.to_check_the_details');
            add_warning($message, UserIgnoredWarning::TYPE_RECEIVED_OFFER, $offer->id);
        }
    }
}