<?php 
namespace iJobDesk\Http\Controllers\Frontend\User;

use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;
use Config;
use DB;
use Exception;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\TransactionLocal;

// ViewModels
use iJobDesk\Models\Views\ViewUser;

class AffiliateController extends Controller {

    /**
    * account/affiliate
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function index(Request $request) {
        $user = Auth::user();        

        $affiliate_buyer_url 		= route('user.signup.user', ['role' => 'buyer', 'ref' => $user->username]);
        $affiliate_freelancer_url 	= route('user.signup.user', ['role' => 'freelancer', 'ref' => $user->username]);

        // If user input affiliate emails
        if ( $request->isMethod('post') ) {
			if ( $user->isSuspended() ) {
				return redirect()->route('user.affiliate');
			}

            $emails = $request->input('emails');

            if ( $emails ) {
                $emails = explode(',', $emails);

                $sent_success = false;
                $sent_already = false;
                $sent_existed = false;
                $sent_own = false;

                $existed_emails = [];

				$user_fullname = $user->fullname();
				$user_avatar = avatar_url($user);
		        $affiliate_url 	= route('user.signup', ['ref' => $user->username]);

                foreach ($emails as $email) {
                    $affiliate = new UserAffiliate;

                    if ( $email ) {

                    	if ( $user->email == $email ) {
							$sent_own = true;

                            continue;
                    	} else if ( $affiliate = UserAffiliate::where('email', $email)->where('user_id', $user->id)->first() ) {
                            if ($affiliate->affiliate_id != 0) {
                                $sent_already = true;
                                continue;
                            }
                        } else if ( User::withTrashed()->where('email', $email)->first() ) {
                            $sent_existed = true;
                            $existed_emails[] = $email;

                            continue;
                        }

                        if (empty($affiliate))
                            $affiliate = new UserAffiliate;

                        $affiliate->user_id = $user->id;
                        $affiliate->email = $email;
                        
                        if ( $affiliate->save() ) {
							EmailTemplate::send(null, 'AFFILIATE', 0, [
								'@#USER#' => $user_fullname,
								'@#USER_PHOTO_URL#' => $user_avatar,
                                '@#SIGNUP_URL#' => $affiliate_url,
							], $email);

							$sent_success = true;
                        }
                    }
                }

                if ( $sent_success ) {
                    add_message( trans('user.affiliate.message_success_sent_invitation'), 'success' );
                } else {
                    if ( $sent_own ) {
                        add_message( trans('user.affiliate.message_failed_sent_invitation_to_own'), 'danger' );
                    }

                    if ( $sent_already ) {
                        add_message( trans('user.affiliate.message_already_sent_invitation'), 'danger' );
                    }

                    if ( $sent_existed ) {
                        add_message( trans('user.affiliate.message_already_email_existed', ['emails' => implode(',', $existed_emails)]), 'danger' );
                    }
                }

                $user->updateLastActivity();
            }
        }

        $acceptedBuyer = $user->getTotalAffiliated(User::ROLE_USER_BUYER);

        $acceptedFreelancer = $user->getTotalAffiliated(User::ROLE_USER_FREELANCER);

        $totalSent = $user->getTotalAffiliatesSent();

        $accepted = $user->getTotalAffiliatesAccepted();

        $earned_lifetime = $user->getTotalAffiliatesAmount([
            'status' => [
                TransactionLocal::STATUS_DONE,
            ]
        ]);

        $total_pending = $user->getTotalAffiliatesAmount([
            'status' => [
                TransactionLocal::STATUS_PENDING,
                TransactionLocal::STATUS_AVAILABLE,
            ]
        ]);

        $affilate_values = [
            'total_sent' => $totalSent,
            'accepted' => $accepted,
            'accepted_buyer' => $acceptedBuyer,
            'accepted_freelancer' => $acceptedFreelancer,
            'earning_lifetime' => $earned_lifetime,
            'payment_pending' => $total_pending,
        ];

        $perPage = Config::get('settings.freelancer.per_page');

        // Calendar
        $dates = ['from' => '', 'to' => ''];

        $user_filter = 0;
        
        // Initialize the date range
        list($dates['from'], $dates['to']) = monthRange();

        // Get transactions
        $transactions = TransactionLocal::leftJoin('view_users AS vu', 'vu.id', '=', 'transactions.ref_user_id')
	                                    ->where('transactions.user_id', $user->id)
	                                    ->whereIn('transactions.type', [
	                                        TransactionLocal::TYPE_AFFILIATE,
	                                        TransactionLocal::TYPE_AFFILIATE_CHILD
	                                    ])
	                                    ->where('transactions.for', '<>', TransactionLocal::FOR_IJOBDESK)
	                                    ->addSelect('transactions.*')
	                                    ->addSelect('vu.email AS email')
	                                    ->addSelect('vu.fullname AS username');

        // If user submit the form
        if ( $request->ajax() && $request->input('_action') == 'filter' ) {

			list($dates['from'], $dates['to']) = parseDateRange($request->input('date_range'));
			
			$user_filter = $request->input('user_type');

            if ( $user_filter ) {
                $transactions = $transactions->where('vu.role', $user_filter);
            }

            $transactions = $transactions->whereBetween('transactions.created_at', [$dates['from'], $dates['to']])
                                            ->orderby('transactions.created_at', 'desc')
                                            ->get();

            return response()->view('pages.user.affiliate_ajax', [
	            'page' => 'user.affiliate_ajax',
	            'transactions' => $transactions,
	        ]);
        }

        $transactions = $transactions->whereBetween('transactions.created_at', [$dates['from'], $dates['to']])
                                     ->orderby('transactions.created_at', 'desc')
                                     ->get();

        $dates['from'] = date('M j, Y', strtotime($dates['from']));
        $dates['to']   = date('M j, Y', strtotime($dates['to']));

        return view('pages.user.affiliate', [
            'page' => 'user.affiliate',
            'user' => $user,
            'affiliate_buyer_url'  => $affiliate_buyer_url,
            'affiliate_freelancer_url' => $affiliate_freelancer_url,
            'values' => $affilate_values,
            'dates' => $dates,
            'user_filter' => $user_filter,
            'transactions' => $transactions,
            'j_trans' => [
                'message_failed_invalid_emails' => trans('user.affiliate.message_failed_invalid_emails'),
            ],
        ]);  
    }
}