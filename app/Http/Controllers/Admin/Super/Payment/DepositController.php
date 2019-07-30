<?php namespace iJobDesk\Http\Controllers\Admin\Super\Payment;
/**
 * @author KCG
 * @since July 28, 2017
 * Withdraw Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Config;
use Auth;
use Log;

use iJobDesk\Models\Country;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\PaymentGateway;
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\WalletHistory;
use iJobDesk\Models\SiteWallet;
use iJobDesk\Models\SiteWalletHistory;
use iJobDesk\Models\Reason;
use iJobDesk\Models\ActionHistory;

class DepositController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Deposit';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        $current_user = Auth::user();

        add_breadcrumb('Payments', route('admin.super.payment.transactions'));
        add_breadcrumb('Deposit');

        $countries = Country::all();
        $paymentGateways = PaymentGateway::getAllGateways();

        try {
            if ( $request->input('_action') == 'search_user' ) {
                return $this->buyers($request);
            } else if ( $request->input('_action') == 'edit_deposit' ) {
            	$id = $request->input('_id');
            	$user_id = $request->input('user_id');

            	$old_amount = 0;
            	if ( $id ) {
            		$transaction = TransactionLocal::find($id);
            		$old_amount = $transaction->amount;
            	} else {
            		$transaction = new TransactionLocal;
	            	$transaction->for = TransactionLocal::FOR_BUYER;
	            	$transaction->type = TransactionLocal::TYPE_CHARGE;
	            	$transaction->status = TransactionLocal::STATUS_PROCEEDING;
            	}

            	$transaction->amount = $request->input('amount');
            	$transaction->user_id = $user_id;
            	$transaction->note = strip_tags($request->input('description'));

            	// Payment gateway
            	$payment_gateway = $request->input('user_payment_gateway_type');

                $payment_gateway_obj = PaymentGateway::where('type', $payment_gateway)->first();
                if ( !$payment_gateway_obj ) {
                    throw new Exception('Invalid payment gateway.');
                }

            	if ( $payment_gateway_obj->isWireTransfer() ) {
    				$gateway_data = [
                        'bankName'              => trim($request->input('bankName')),
                        'bankBranch'            => trim($request->input('bankBranch')),
                        'bankCountry'           => trim($request->input('bankCountry')),
                        'beneficiarySwiftCode'  => trim($request->input('beneficiarySwiftCode')),
                        'ibanAccountNo'         => trim($request->input('ibanAccountNo')),
                        'accountName'           => trim($request->input('accountName')),
                        'beneficiaryAddress1'   => trim($request->input('beneficiaryAddress1')),
                        'beneficiaryAddress2'   => trim($request->input('beneficiaryAddress2')),
                    ];

                    $real_id = implode('_', [
                        $data['bankCountry'],
                        $data['ibanAccountNo'],
                        $data['beneficiarySwiftCode'],
                    ]);
                } else if ( $payment_gateway_obj->isCreditCard() ) {
                    $gateway_data = [
                        'firstName' 		=> trim($request->input('firstName')),
                        'lastName' 			=> trim($request->input('lastName')),
                        'cardType' 			=> trim($request->input('cardType')),
                        'expDateYear' 		=> $request->input('expDateYear'),
                        'expDateMonth' 		=> $request->input('expDateMonth'),
                        'cvv' 				=> trim($request->input('cvv')),
                        'cardNumber' 		=> substr(trim($request->input('cardNumber')), -4),
                    ];

                    $real_id = encrypt_string(trim($request->input('cardNumber')));
            	} else if ( $payment_gateway_obj->isPaypal() ) {
    				$gateway_data = [
                        'email' => trim($request->input('paypal_email'))
                    ];

                    $real_id =  trim($request->input('paypal_email'));
            	} else if ( $payment_gateway_obj->isSkrill() ) {
                    $gateway_data = [
                        'email' => trim($request->input('skrill_email'))
                    ];

                    $real_id =  trim($request->input('skrill_email'));
                } else if ( $payment_gateway_obj->isPayoneer() ) {
                    $gateway_data = [
                        'email' => trim($request->input('payoneer_email'))
                    ];

                    $real_id =  trim($request->input('payoneer_email'));
                } else if ( $payment_gateway_obj->isWeixin() ) {
    				$gateway_data = [
                        'phoneNumber' => trim($request->input('wepayPhoneNumber'))
                    ];

                    $real_id =  trim($request->input('wepayPhoneNumber'));
            	}

    			// Update
    			$userPaymentGateway = UserPaymentGateway::where('user_id', $user_id)
    													->where('gateway', $payment_gateway)
    													->where('status', UserPaymentGateway::IS_STATUS_YES)
    													->where('data', json_encode($gateway_data))
    													->first();

    			if ( $userPaymentGateway ) {
    				$transaction->user_payment_gateway_id = $userPaymentGateway->id;
    			} else {
    	        	$userPaymentGateway = new UserPaymentGateway;
    				$userPaymentGateway->user_id = $user_id;
    				$userPaymentGateway->gateway = $payment_gateway;
    				$userPaymentGateway->data = json_encode($gateway_data);
    				$userPaymentGateway->real_id = $real_id;
    				$userPaymentGateway->status = UserPaymentGateway::IS_STATUS_YES;
    	        	
    	        	if ( $userPaymentGateway->save() ) {
    	        		$transaction->user_payment_gateway_id = $userPaymentGateway->id;
    	        	}
    	        }

                $gateway_data['gateway'] = $payment_gateway;
                $transaction->user_payment_gateway_data = json_encode($gateway_data);
            	
            	if ( $id ) {
    	        	if ( $transaction->save() ) {
    	        		add_message('A deposit transaction has been updated successfully.', 'success');

						$action = new ActionHistory;
						$action->doer_id = $this->auth_user->id;
						$action->type = ActionHistory::TYPE_TRANSACTION;
						$action->action_type = 'UPDATE';
						$action->target_id = $transaction->id;
						$action->description = sprintf('Amount has been updated from $%s to $%s', $old_amount, $transaction->amount);

						$action->save();

    	        	} else {
    	        		add_message('An error occured while updating a deposit transaction.', 'danger');
    	        	}
    	        } else {
    	        	if ( $transaction->save() ) {
    	        		add_message('New deposit transaction has been added successfully.', 'success');
    	        	} else {
    	        		add_message('An error occured while creating new deposit transaction.', 'danger');
    	        	}
    	        }

    	        if ( $current_user->isFinancial() )
                    return redirect()->route('admin.financial.deposit');
                else
                    return redirect()->route('admin.super.payment.deposit');
            }

            if ( $request->method('post') ) {
                $action = $request->input('_action');

                if ( $action == 'CHANGE_STATUS' ) {
					$status = $request->input('deposit_status');
					$deposit_ids = $request->input('ids');

					if ( $deposit_ids ) {                    
	                    if ( $status == TransactionLocal::STATUS_PROCEEDING ) {
	                        if ( TransactionLocal::whereIn('id', $deposit_ids)
                            			->update([
                            				'status' => TransactionLocal::STATUS_PROCEEDING
                            			]) ) {
	                        	add_message(sprintf('The %d deposits has been proceeding.', count($deposit_ids)), 'success');
	                    	}
	                    } else if ( $status == TransactionLocal::STATUS_SUSPENDED ) {
                            if ( TransactionLocal::whereIn('id', $deposit_ids)
                            			->update([
                            				'status' => TransactionLocal::STATUS_SUSPENDED
                            			]) ) {
	                        	add_message(sprintf('The %d deposits has been suspended.', count($deposit_ids)), 'success');
	                		}

							// Add reason
		    				$admin = Auth::user();
		    				foreach ($deposit_ids as $t_id) {
		    					$reason = new Reason;
		    					$reason->message = $request->input('_reason');
		    					$reason->admin_id = $admin->id;
		    					$reason->type = Reason::TYPE_TRANSACTION;
		    					$reason->affected_id = $t_id;
		    					$reason->action = Reason::ACTION_SUSPENSION;

		    					$reason->save();
		    				}
	                    } else if ( $status == 'DELETE' ) {
	    					TransactionLocal::whereIn('id', $deposit_ids)->delete();

	                        add_message(sprintf('The %d deposit(s) has been deleted.', count($deposit_ids)), 'success');
	                    }
	                }
                } else if ( $action == 'user_gateway' ) {
                    $user_id = $request->input('user_id');
                    $gateway = $request->input('gateway');
                    
                    $user_gateway = UserPaymentGateway::where('user_id', $user_id)
                                        ->where('gateway', $gateway)
                                        ->where('status', UserPaymentGateway::IS_STATUS_YES)
                                        ->first();

                    $result = [];
                    if ( $user_gateway ) {
                        if ( $user_gateway->isWireTransfer() ) {
                            $result['data'] = json_decode($user_gateway->data, true);
                        } else {
                            $result['data'] = $user_gateway->real_id;
                        }
                    }

                    return response()->json($result);
                }
            }
        } catch ( Exception $e ) {
            Log::error('[Admin - DepositController.php::index()] Error: ' . $e->getMessage());
        }

        $total_in_queue = TransactionLocal::where('type', TransactionLocal::TYPE_CHARGE)
                                            ->where('status', TransactionLocal::STATUS_AVAILABLE)
                                            ->count();

        $total_proceeding = TransactionLocal::where('type', TransactionLocal::TYPE_CHARGE)
                                            ->where('status', TransactionLocal::STATUS_PROCEEDING)
                                            ->count();

        $total_suspended = TransactionLocal::where('type', TransactionLocal::TYPE_CHARGE)
                                            ->where('status', TransactionLocal::STATUS_SUSPENDED)
                                            ->count();

        $deposits = TransactionLocal::where('transactions.type', TransactionLocal::TYPE_CHARGE)
                               ->where('transactions.user_id', '<>', 1)
                               ->where(function($query) {
                                    $query->where(function($query) {
                                            $query->whereNull('transactions.done_at')
                                                  ->orWhere('transactions.status', '!=', TransactionLocal::STATUS_AVAILABLE);
                                          })
                                          ->orWhereNull('transactions.done_at');
                               })
                               ->leftJoin('view_users AS vu', 'vu.id', '=', 'transactions.user_id')
                               ->leftJoin('user_payment_gateways AS upg', 'upg.id', '=', 'transactions.user_payment_gateway_id')
                               ->addSelect('transactions.*')
                               ->addSelect('vu.fullname AS username')
                               ->addSelect('vu.role AS role')
                               ->addSelect('vu.location AS user_location')
                               ->addSelect('vu.status AS user_suspended')
                               ->addSelect('vu.email AS user_email')
                               ->addSelect('upg.gateway AS gateway')
                               ->addSelect(DB::raw('IF(transactions.status = ' . TransactionLocal::STATUS_AVAILABLE . ', 1, IF(transactions.status = ' . TransactionLocal::STATUS_PROCEEDING . ', 2, IF(transactions.status = ' . TransactionLocal::STATUS_SUSPENDED . ', 3, 4))) AS `order`'));;

        $sort     = $request->input('sort', 'order');
        $sort_dir = $request->input('sort_dir', 'asc');

        // Filtering
        $filter = $request->input('filter');

        // By #ID
        if (isset($filter) && $filter['id'] != '') {
            $deposits->where('transactions.id', $filter['id']);
        }

        // By User Name
        if (!empty($filter['username'])) {
            $deposits->where(function($query) use ($filter) {
                if ( is_numeric($filter['username']) ) {
                    $query->where('vu.id', intval($filter['username']));
                } else {
                    $query->whereRaw('LOWER(vu.username) LIKE "%' . trim(strtolower($filter['username'])) . '%"')
                            ->orWhereRaw('LOWER(vu.fullname) LIKE "%' . trim(strtolower($filter['username'])) . '%"');
                }
            });
        }

        // By User Role
        if (!empty($filter['role'])) {
            $deposits->where('vu.role', $filter['role']);
        }

        // By User Location
        if (!empty($filter['user_location'])) {
            $deposits->where('vu.location', 'LIKE', '%'.trim($filter['user_location']).'%');
        }

        // By User Email
        if (!empty($filter['user_email'])) {
            $deposits->whereNull('upg.deleted_at')
                        ->where('upg.data', 'LIKE', '%'.trim($filter['user_email']).'%');
        }

        // By Payment Gateway
        if (!empty($filter['gateway'])) {
            $deposits->where('upg.gateway', $filter['gateway']);
        }

        // By Comment
        if (!empty($filter['note'])) {
            $deposits->where('transactions.note', 'LIKE', '%'.trim($filter['note']).'%');
        }

        // By Amount
        if (!empty($filter['amount'])) {
            $deposits->where('transactions.amount', $filter['amount']);
        }

        // By Deposited At
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $deposits->where('transactions.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $deposits->where('transactions.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // By Updated At
        if (!empty($filter['updated_at'])) {
            if (!empty($filter['updated_at']['from'])) {
                $deposits->where('transactions.updated_at', '>=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['from'])));
            }

            if (!empty($filter['updated_at']['to'])) {
                $deposits->where('transactions.updated_at', '<=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['to']) + 24* 3600));
            }
        }

        // By Status
        if ($filter['status'] != '') {
            $deposits->where('transactions.status', $filter['status']);
        }

        $deposits = $deposits->orderBy($sort, $sort_dir)
                            ->orderBy('transactions.created_at', 'desc');

        $request->flashOnly('filter');

        return view('pages.admin.super.payment.deposit', [
            'page' => 'super.payment.deposit',
            'deposits' => $deposits->paginate($this->per_page),
            'total_in_queue' => $total_in_queue,
            'total_proceeding' => $total_proceeding,
            'total_suspended' => $total_suspended,
            'countries' => Country::all(),
            'sort' => $sort,
            'sort_dir' => '_' . $sort_dir,
            'payment_gateways' => $paymentGateways,
            'countries' => $countries,
        ]);
    }

	private function buyers(Request $request) {
		$term = $request->input('term');
		$id = $request->input('id');

		if ( empty($term) && !empty($id) ) {
			return response()->json(ViewUser::findOrFail($id));
		}

		$users = ViewUser::where('role', User::ROLE_USER_BUYER)
							->where(function($query) use ($term) {
                                if ( is_numeric($term) ) {
								    $query->where('id', intval(trim($term)));
                                } else {
									$query->whereRaw('LOWER(username) LIKE "%' . trim(strtolower($term)) . '%"')
                                            ->orWhereRaw('LOWER(fullname) LIKE "%' . trim(strtolower($term)) . '%"');
                                }
							})
							->get();

		return response()->json(['users' => $users]);
	}
}