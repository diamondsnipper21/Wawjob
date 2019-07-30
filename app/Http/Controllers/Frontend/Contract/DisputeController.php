<?php namespace iJobDesk\Http\Controllers\Frontend\Contract;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Storage;
use Config;
use Session;
use Exception;
use Validator;
use Log;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Settings;

use iJobDesk\Models\Contract;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\HourlyReview;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\File;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Notification;

class DisputeController extends Controller {

	public function create(Request $request, $contract_id) {
		$user = Auth::user();
        $contract = Contract::find($contract_id);

        if (!$contract->isAvailableDispute()) {
            Log::alert("Frontend/DisputeController@create: You don\'t have permission dispute for this contract #{$contract->id}. User #{$user->id}");
            abort(404);
        }


        $validator = Validator::make($request->all(), [
            'confirm_file_dispute' => 'accepted',
            'message'              => 'required|max:2000'
        ]);

        if ( $validator->fails() ) {
            $errors = $validator->messages();
            if ( $errors->all() ) {
                foreach ( $errors->all() as $error ) {
                    add_message($error, 'danger');
                }
            }

            return redirect()->to(_route('contract.contract_view', ['id' => $contract->id])); 
        }

		if ($contract->isClosed()) { // if contract is closed, you can't dispute. prevent this dispute request.
			return redirect()->to(_route('contract.contract_view', ['id' => $contract->id])); 
		}

		// Suspend current contract
    	$_POST['_reason'] = sprintf('Auto Suspension by Dispute - %s (%s) initiated a dispute.', $user->fullname(), $user->isBuyer()?'Buyer':'Freelancer'); // for action history in super admin. refer to app/Providers/EventServierProvider.php

        $reasons = [
            'me'         => trans('contract.contract_suspension_reason_by_your_dispute', ['user' => $user->fullname()]),
            'buyer'      => trans('contract.contract_suspension_reason_by_client_dispute', ['user' => $user->fullname()]),
            'freelancer' => trans('contract.contract_suspension_reason_by_freelancer_dispute', ['user' => $user->fullname()]),
        ];
    	$contract->suspend($reasons);

        // Make freelancer account finacial suspended
        $_POST['_reason'] = sprintf('Auto Financial Suspension by Contract Dispute for - "%s"', $contract->title);

        $contract->contractor->status            = User::STATUS_FINANCIAL_SUSPENDED;
        $contract->contractor->is_auto_suspended = 1;
        $contract->contractor->save();

    	$ticket = $contract->getOpenedDispute();
		if (!$ticket) { // if the dispute is created again, create new ticket and message room.
            $ticket = new Ticket;
            $ticket->subject        = $contract->title;
            $ticket->content        = $request->input('message');
            $ticket->user_id        = $user->id;
            $ticket->contract_id    = $contract_id;
            $ticket->type           = Ticket::TYPE_DISPUTE;
            $ticket->priority       = Ticket::PRIORITY_HIGH;
    		
    		$ticket->save();
        }

        // Send dispute email
        $current_user = $user;
        $message = $request->input('message');

        $sender = $current_user;
        $receiver = $contract->buyer;

        // Send email for dispute
        if ( $current_user->isBuyer() )
            $receiver = $contract->contractor;

        EmailTemplate::send($receiver, 'SEND_DISPUTE', 0, [
            'USER' 				=> $receiver->fullname(),
            'SENDER' 			=> $sender->fullname(),
            'CONTRACT_TITLE' 	=> $contract->title,
            'CONTRACT_URL' 		=> _route('contract.contract_view', ['id' => $contract->id], true, null, $receiver),
            'MESSAGE' 			=> strip_tags(nl2br($message), '<br>')
        ]);

    	unset($_POST['_reason']);

    	add_message(trans('contract.dispute_alert_message'), 'success', false);

        return $this->send_message($request, $contract->id, $ticket->id);
	}

	public function cancel(Request $request, $contract_id, $ticket_id) {
		$me = Auth::user();

        $archive_type = $request->input('archive_type');
        $reason = $request->input('reason');

        $ticket = Ticket::findOrFail($ticket_id);
        $contract = Contract::findOrFail($contract_id);

        if ($request->isMethod('POST')) {
        	$ticket->archive_type       = $archive_type;
        	$ticket->reason             = $reason;
        	$ticket->dispute_winner_id  = null;
        	$ticket->status  			= Ticket::STATUS_SOLVED;
        	$ticket->ended_at           = date('Y-m-d H:i:s');

        	$_POST['_reason'] = sprintf('Dispute has been cancelled.');

        	$contract->status = Contract::STATUS_OPEN;

        	$contract->save();
        	$ticket->save();

        	$contractor = $contract->contractor; // contractor
        	$buyer 		= $contract->buyer; // buyer
            
            $contractor->status             = User::STATUS_AVAILABLE;
            $contractor->save();

            $receiver = $contractor;
            if ($me->id == $contractor->id)
            	$receiver = $buyer;

             EmailTemplate::send($receiver, 'DISPUTE_CANCEL', 0, [
                 'CONTRACT_TITLE' => $contract->title,
                 'CONTRACT_URL' => _route('contract.contract_view', ['id' => $contract->id], true, null, $receiver),
                 'USER' => $receiver->fullname(),
                 'CANCELLER' => $me->fullname()
             ]);
        }

        return redirect()->to(_route('contract.contract_view', ['id' => $contract->id])); 
	}

	public function send_message(Request $request, $contract_id, $ticket_id) {
		$user = Auth::user();

		$ticket   = Ticket::find($ticket_id);

		if (!$ticket)
			abort(404);

		if (!$contract_id)
			$contract_id = $ticket->contract_id;

		$contract = Contract::find($contract_id);

		if (!$contract) {
			abort(404);
        }

		$message = $request->input('message');

		$ticketcomment = new TicketComment;
        $ticketcomment->ticket_id  = $ticket_id;
        $ticketcomment->sender_id  = $user->id;
        $ticketcomment->message    = $message;
        $ticketcomment->save();

        return redirect()->to(_route('contract.contract_view', ['id' => $contract->id])); 
	}

    public function refund(Request $request, $contract_id) {
        $user = Auth::user();

        $contract = Contract::find($contract_id);

        if ( !$contract )
            abort(404);

        if ( $contract->isClosed() ) {
            return redirect()->to(_route('contract.contract_view', ['id' => $contract->id])); 
        }

        if ( $request->isMethod('post') ) {
	        list($last_week_from, $last_week_to) = weekRange('-1 weeks', 'Y-m-d');
	        
			$existed = HourlyReview::where('contract_id', $contract_id)
                                    ->where('buyer_id', $contract->buyer_id)
                                    ->where('contractor_id', $contract->contractor_id)
                                    ->where('hourly_from', $last_week_from)
                                    ->where('hourly_to', $last_week_to)
                                    ->where('disputed', 0)
                                    ->first();

            if ( $existed ) {

	            // Get the not-qualified or manual logged hours
	            $mins = HourlyLog::where('contract_id', $contract_id)
	                            	->where('taken_at', '>=', $last_week_from . ' 00:00:00')
	                            	->where('taken_at', '<=', $last_week_to . ' 23:59:59')
	                            	->where(function($query) {
	                            		$query->where('is_manual')
	                            				->orWhere('score', '<=', 5);
	                            	})
	                            	->count() * 10;

	            if ( $mins ) {
	            	$total_price = $contract->buyerPrice($mins);

					if ( $existed ) {
						$res = TransactionLocal::pay_hourly_refund([
							'cid' => $contract_id, 
							'amount' => $total_price,
							'hourly_from' => $last_week_from,
							'hourly_to' => $last_week_to,
							'hourly_mins' => $mins,
						]);

						if ( $res['success'] ) {
                            $original_mins = $existed->hourly_mins;

							$existed->hourly_mins = $existed->hourly_mins - $mins;
							$existed->amount = $existed->amount - $total_price;
							$existed->disputed = 1;

							// Update ContractMeter
							$contract->meter->last_mins = $existed->hourly_mins;
							$contract->meter->last_amount = $existed->amount;
							$contract->meter->total_mins = $contract->meter->total_mins - $mins;
							$contract->meter->total_amount = $contract->meter->total_amount - $total_price;

							$contract->meter->save();

							// Update HourlyReview
							if ( $existed->amount <= 0 ) {
								$existed->delete();
							} else {
								$existed->save();

                                // Update original transaction
                                TransactionLocal::where('contract_id', $contract_id)
                                                ->where('for', TransactionLocal::FOR_BUYER)
                                                ->where('hourly_from', $last_week_from)
                                                ->where('hourly_to', $last_week_to)
                                                ->where('hourly_mins', $original_mins)
                                                ->update([
                                                    'ref_amount' => -($existed->amount)
                                                ]);
							}

							add_message(trans('contract.dispute_got_refunded_success'), 'success');
						}
					}
	            }
	        }

            if ( !isset($res) || !$res['success'] ) {
            	add_message(trans('contract.dispute_got_refunded_failure'), 'danger');
            }

        }

        return redirect()->to(_route('contract.contract_view', ['id' => $contract->id]));

    }
}