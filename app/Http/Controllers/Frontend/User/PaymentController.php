<?php 
namespace iJobDesk\Http\Controllers\Frontend\User;

use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;
use Config;
use DB;
use Session;
use Exception;
use Log;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\UserAffiliate;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Country;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\PaymentGateway;
use iJobDesk\Models\Payment\PaymentPaypalGateway;
//use iJobDesk\Models\Payment\PaymentCreditCardGateway;
use iJobDesk\Models\Payment\PaymentWePayGateway;
use iJobDesk\Models\Payment\PaymentSkrillGateway;
//use iJobDesk\Models\Payment\PaymentPaySafeGateway;
//use iJobDesk\Models\Payment\PaymentWireTransferGateway;
use iJobDesk\Models\Payment\PaymentAdyenGateway;
use iJobDesk\Models\UserDeposit;
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\QueueWechatDeposit;
use iJobDesk\Models\Settings;
use iJobDesk\Models\File;

// ViewModels
use iJobDesk\Models\Views\ViewUser;

use iJobDesk\Models\PdfManager;

use Inacho\CreditCard;

class PaymentController extends Controller {

    /**
    * user/withdraw
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function withdraw(Request $request) {
        $user = Auth::user();

        $withdrawAmount = '';

        $payment_gateway_id = 0;

        $wallet = $user->myBalance(false);

        $cny_exchange_rate = Settings::get('CNY_EXCHANGE_RATE_SELL');
        $eur_exchange_rate = Settings::get('EUR_EXCHANGE_RATE_SELL');

        $amountUnderHolding = $user->isBuyer() ? $user->getTotalAmountUnderWorkAndReview() : 0;

        $balance = $newBalance = $wallet - $amountUnderHolding;

        $action = $request->input('_action');

        if ( $action ) {
			if ( $user->isSuspended() || $user->isFinancialSuspended() ) {
				return redirect()->route('user.withdraw');
			}

            switch ( $action ) {
                case 'requestGetPaid':
                    if ( $request->input('withdraw_amount') ) {
                        $withdrawAmount = floatval($request->input('withdraw_amount'));
                    }

                    if ( $request->input('payment_gateway') ) {
                        $payment_gateway_id = floatval($request->input('payment_gateway'));
                    }

                    break;

                case 'previewGetPaid':
                    $withdrawAmount = floatval($request->input('withdraw_amount'));
					$withdraw_min_amount = doubleval(Settings::get('WITHDRAW_MIN_AMOUNT'));
					$withdraw_max_amount = doubleval(Settings::get('WITHDRAW_MAX_AMOUNT'));

                    if ( $withdrawAmount < $withdraw_min_amount || $withdrawAmount > $withdraw_max_amount ) {
                        add_message(trans('user.withdraw.message_failed_withdrawn_from_amount', ['from' => formatCurrency($withdraw_min_amount), 'to' => formatCurrency($withdraw_max_amount)]), 'danger');

                        return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
                    }

                    $newBalance = $balance - $withdrawAmount;

                    if ( $withdrawAmount > $balance || $newBalance < 0 ) {
                        add_message(trans('user.withdraw.message_failed_withdrawn_not_enough', ['total_amount' => formatCurrency($balance)]), 'danger');

                        return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
                    }

                    $newBalance = $balance - $withdrawAmount;
                    $userPaymentGateway = UserPaymentGateway::find($request->input('payment_gateway'));
			        
			        if ( !$userPaymentGateway ) {
                        add_message(trans('user.withdraw.message_failed_no_payment_method'), 'danger');
                        return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
                    } else {
				        if ( !$userPaymentGateway->isEnabledWithdraw() ) {
				            add_message(trans('user.withdraw.message_wait_until_pending_days', ['days' => Settings::get('DAYS_AVAILABLE_PAYMENT_METHOD')]), 'danger');
				            return redirect()->route('user.withdraw', ['_action' => $action]);
				        }

                        // Check disabled withdrawal
                        if ( !$userPaymentGateway->paymentGateway->enable_withdraw ) {
                            add_message(trans('user.withdraw.message_failed_withdrawn_disabled_method'), 'danger');
                            return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
                        }

                        $withdraw_fee = $userPaymentGateway->withdrawFeeAmount($withdrawAmount);
	                    if ( $withdrawAmount - $withdraw_fee < 0 ) {
	                        add_message(trans('user.withdraw.message_failed_withdrawn_not_enough', ['total_amount' => formatCurrency($balance)]), 'danger');

	                        return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
	                    }

				        // Check the amount if user can withdraw from selected gateway.
                        $gatewayAmount = UserDeposit::getAmount($user->id, $userPaymentGateway->gateway, $userPaymentGateway->real_id);
                        if ( $user->isBuyer() && $gatewayAmount < $withdrawAmount ) {
				            add_message(trans('user.withdraw.message_failed_withdrawn_from_gateway', ['total_amount' => formatCurrency($gatewayAmount)]), 'danger');
				            return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
				        }

	                    return view('pages.user.withdraw', [
	                        'page' => 'user.withdraw',
	                        'withdraw_amount' => $withdrawAmount,
	                        'withdraw_fee' => $withdraw_fee,
                            'holding_amount' => $amountUnderHolding,
                            'wallet' => $wallet,
	                        'new_balance' => $newBalance,
	                        'payment_gateway' => $userPaymentGateway,
	                        'action' => $action,
                            'cny_exchange_rate' => $cny_exchange_rate,
                            'eur_exchange_rate' => $eur_exchange_rate,
                            'j_trans' => [
                                'free' => trans('common.free'),
                                'tip_fee_of_withdraw_amount' => trans('user.withdraw.tip_fee_of_withdraw_amount')
                            ],
	                    ]);
	                }

                    break;

                case 'withdraw':

                    $amount = floatval($request->input('withdraw_amount'));
                    $amount = round($amount * 100) / 100;

                    $userPaymentGateway = UserPaymentGateway::find($request->input('payment_gateway'));

                    if ( !$userPaymentGateway ) {
                        add_message(trans('user.withdraw.message_failed_no_payment_method'), 'danger');
                    } else {
				        if ( !$userPaymentGateway->isEnabledWithdraw() ) {
				            add_message(trans('user.withdraw.message_wait_until_pending_days', ['days' => Settings::get('DAYS_AVAILABLE_PAYMENT_METHOD')]), 'danger');
				            return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
				        }

                        // Check disabled withdrawal
                        if ( !$userPaymentGateway->paymentGateway->enable_withdraw ) {
                            add_message(trans('user.withdraw.message_failed_withdrawn_disabled_method'), 'danger');
                            return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
                        }

                        // Check country available for withdrawal
                        if ( !$userPaymentGateway->isEnabledCountry() ) {
                        	add_message(trans('user.payment_method.message_not_support_country'), 'danger');
                            return redirect()->route('user.withdraw');
                        }

                        $withdraw_fee = $userPaymentGateway->withdrawFeeAmount($amount);

				        // Check the amount if user can withdraw from selected gateway.
                        $gatewayAmount = UserDeposit::getAmount($user->id, $userPaymentGateway->gateway, $userPaymentGateway->real_id);
                        if ( $user->isBuyer() && $gatewayAmount < $amount ) {
				            add_message(trans('user.withdraw.message_failed_withdrawn_from_gateway', ['amount' => formatCurrency($check_amount), 'total_amount' => formatCurrency($gatewayAmount)]), 'danger');
				            return redirect()->route('user.withdraw', ['_action' => 'requestGetPaid']);
				        }

                        $gateway_data = json_decode($userPaymentGateway->data, true);

                    	// Process payment depending to user's payment method
                    	$paid = false;
                    	$order_id = '';
                    	
                    	if ( $userPaymentGateway->paymentGateway->isPaypal() ) {
                    		/*
                    		$paypalGateway = new PaymentPaypalGateway();
                    		$paymentResult = $paypalGateway->withdraw($gateway_data['email'], $amount);

                    		if ( $paymentResult['success'] ) {
                    			$paid = true;
                    			$order_id = $paymentResult['transaction_id'];
                    		} else {
                    			$error = $paymentResult['error'];
                    		}
                    		*/
                        } else if ( $userPaymentGateway->paymentGateway->isSkrill() ) {
                    		/*
                            $skrillGateway = new PaymentSkrillGateway();
                    		$paymentResult = $skrillGateway->withdraw($gateway_data['email'], $amount);

                    		if ( $paymentResult ) {
                    			$paid = true;
                    			$order_id = $paymentResult['id'];
                    		} else {
                    			$error = true;
                    		}
                            */
                        } else if ( $userPaymentGateway->paymentGateway->isCreditCard() ) {
                        	/*
                        	$params = $userPaymentGateway->dataArray();
                        	$params['amount'] = $amount;

                    		$adyenGateway = new PaymentAdyenGateway();
                    		$paymentResult = $adyenGateway->payout($params);

                    		if ( $paymentResult ) {
                				$paid = true;
                    			$order_id = $paymentResult['id'];
                    		} else {
                    			$error = true;
                    		}
                    		*/
                        }

	                    if ( !isset($error) ) {
		                    if ( $transaction_id = TransactionLocal::withdraw($user->id, $amount, $userPaymentGateway->id, ($paid ? TransactionLocal::STATUS_DONE : TransactionLocal::STATUS_AVAILABLE), $order_id) ) {
		                        
		                        add_message(trans('user.withdraw.message_success_withdrawn', ['amount' => formatCurrency($amount)]), 'success');

		                        $user->updateLastActivity();

								// Generate pdf document for bank information
								/*
                                if ( $userPaymentGateway->paymentGateway->isWireTransfer() ) {
									$bank_data = json_decode($userPaymentGateway->data, true);
									$pdfFileName = 'withdraw_bank_' . $user->id . '_' . $userPaymentGateway->id . '_' . $transaction_id . '.pdf';

									PdfManager::generate('withdraw', $pdfFileName, [
										'bankName' => $bank_data['bankName'],
                                        'bankCountry' => Country::getCountryNameByCode($bank_data['bankCountry']),
                                        'bankBranch' => $bank_data['bankBranch'],
										'beneficiaryAddress1' => $bank_data['beneficiaryAddress1'],
                                        'beneficiaryAddress2' => $bank_data['beneficiaryAddress2'],
										'beneficiarySwiftCode' => $bank_data['beneficiarySwiftCode'],
										'ibanAccountNo' => $bank_data['ibanAccountNo'],
										'accountName' => $bank_data['accountName'],
										'amount' => ($amount - $withdraw_fee),
									]);
								}
                                */
		                    }
		                } else {
    		                add_message(trans('user.withdraw.message_failed_withdrawn'), 'danger');

    		                if ( isset($error) ) {
    		                	add_message($error, 'danger');
    		                }
                        }
	                }

                    return redirect()->route('user.withdraw');
                
                default:
                    break;
            }
        }

        // Get the last payment
        $lastPayment = TransactionLocal::getLastWithdraw($user->id);

        if ( !count($user->activePaymentGateways) ) {
        	add_message(trans('user.payment_method.message_non_setup_payment_method_for_withdraw', ['url' => route('user.payment_method')]), 'danger');
        } else if ( !$user->primaryPaymentGateway ) {
        	add_message(trans('user.payment_method.message_non_setup_primary_payment_method', ['url' => route('user.payment_method')]), 'danger');
        }

        return view('pages.user.withdraw', [
            'page'         => 'user.withdraw',
            'wallet' => $wallet,
            'balance'      => $balance,
            'last_payment' => $lastPayment,
            'withdraw_amount' => $withdrawAmount,
            'holding_amount' => $amountUnderHolding,
            'payment_gateway_id' => $payment_gateway_id,
            'action' => $action,
            'cny_exchange_rate' => $cny_exchange_rate,
            'eur_exchange_rate' => $eur_exchange_rate,
            'j_trans' => [
                'free' => trans('common.free'),
                'cny' => trans('common.cny'),
                'eur' => trans('common.eur'),
                'cny_exchange_rate' => $cny_exchange_rate,
                'eur_exchange_rate' => $eur_exchange_rate,
                'tip_fee_of_withdraw_amount' => trans('user.withdraw.tip_fee_of_withdraw_amount')
            ],
        ]);
    }

    /**
    * user/payment_method
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function payment_method(Request $request)
    {
        $user = Auth::user();

        $countries = Country::all();

        $currentUrl = route('user.payment_method');

        try {

			// Redirected from PayPal with token value
			/*
            if ( $id = $request->input('id') ) {			

                if ( $token = $request->input('token') ) {
                    $paypalGateway = new PaymentPaypalGateway();

    				// This will return PayerID for success
    				$resultAuthenticate = $paypalGateway->checkAuthenticate($token);

    				if ( $resultAuthenticate ) {
    					$userPaymentGateway = UserPaymentGateway::findOrFail($id);
                        $userPaymentGateway->real_id = $resultAuthenticate;
    					$userPaymentGateway->params = json_encode(['PayerID' => $resultAuthenticate]);
    					$userPaymentGateway->status = UserPaymentGateway::IS_STATUS_YES;
                        $userPaymentGateway->is_pending = UserPaymentGateway::IS_PENDING_YES;

    					if ( $userPaymentGateway->save() ) {
                            add_message(trans('user.payment_method.message_success_add_payment_method'), 'success');

                            EmailTemplate::send($user, 'PAYMENT_METHOD_ADDED', 0, [
                                'USER' => $user->fullname(),
                                'CONTACT_US_URL' => route('frontend.contact_us'),
                            ]);
    	                }

                        return redirect()->route('user.payment_method');
    				}
                } else {
                    UserPaymentGateway::where('id', $id)->forceDelete();

                    add_message(trans('user.payment_method.message_failed_add_payment_method'), 'danger');
                    
                    return redirect()->route('user.payment_method');
                }
			}
            */

            if ( $request->isMethod('post') ) {

            	if ( $user->isSuspended() || $user->isFinancialSuspended() ) {
					return redirect()->route('user.payment_method');
				}

                switch ( $request->input('_action') ) {
                    case 'editPaymentGateway':

                        $checkDuplicated = UserPaymentGateway::isDuplicatedGateway($request, $user);
                        if ( $checkDuplicated == 'same_duplicated' ) {
                            add_message(trans('user.payment_method.message_same_duplicated_payment_method'), 'danger');
                            break;
                        } else if ( $checkDuplicated == 'another_duplicated' ) {
                            add_message(trans('user.payment_method.message_another_duplicated_payment_method'), 'danger');
                            break;
                        } else if ( $checkDuplicated == 'deleted_duplicated' ) {
                        	add_message(trans('user.payment_method.message_deleted_duplicated_payment_method'), 'danger');
                        	break;
                        }

                        $gateway = $request->input('_gateway');

                        // For Update
                        $userPaymentGatewayId = $request->input('_id');
                        if ( $userPaymentGatewayId ) {
                            $userPaymentGateway = UserPaymentGateway::where('id', $userPaymentGatewayId)
                                                                    ->where('user_id', $user->id)
                                                                    ->first();
                            if ( !$userPaymentGateway ) {
								add_message(trans('user.payment_method.message_failed_add_payment_method'), 'danger');
                        		break;
                            }
                        } else {
	                        $userPaymentGateway = new UserPaymentGateway;
	                        $userPaymentGateway->user_id = $user->id;
	                        $userPaymentGateway->gateway = $gateway;
	                    }

						// Set as primary if created at first
						if ( !$user->paymentGateways->count() ) {
                        	$userPaymentGateway->is_primary = UserPaymentGateway::IS_PRIMARY_YES;
                        }

                        if ( $gateway == PaymentGateway::GATEWAY_PAYPAL ) {
                            if ( !trim($request->input('paypalEmail')) ) {
                            	break;
                            }

                            $data = [
                                'email' => trim($request->input('paypalEmail'))
                            ];

                            $userPaymentGateway->real_id = trim($request->input('paypalEmail'));

                            $country_field = 'paypal_enabled';
                        } else if ( $gateway == PaymentGateway::GATEWAY_WEIXIN ) {
                        	if ( !trim($request->input('weixinNumber')) ) {
                            	break;
                            }

                            $data = [
                                'phoneNumber' => trim($request->input('weixinNumber'))
                            ];

                            $userPaymentGateway->real_id = trim($request->input('weixinNumber'));
                            $userPaymentGateway->file_id = intval($request->input('file_id'));

                            $country_field = 'wechat_enabled';
                        } else if ( $gateway == PaymentGateway::GATEWAY_PAYONEER ) {
                            if ( !trim($request->input('payoneerEmail')) ) {
                                break;
                            }

                            $data = [
                                'email' => trim($request->input('payoneerEmail'))
                            ];

                            $userPaymentGateway->real_id = trim($request->input('payoneerEmail'));

                            $country_field = 'payoneer_enabled';
                        } else if ( $gateway == PaymentGateway::GATEWAY_SKRILL ) {
                            if ( !trim($request->input('skrillEmail')) ) {
                            	break;
                            }

                            $email = trim($request->input('skrillEmail'));

                            $data = [
                                'email' => $email
                            ];

                            $userPaymentGateway->real_id = $email;

                            /*
                            $skrillGateway = new PaymentSkrillGateway();
                            if ( !$skrillGateway->authenticate($email) ) {
                            	add_message(trans('user.payment_method.message_failed_add_payment_method'), 'danger');
                        		break;
                            }
                            */

                            $country_field = 'skrill_enabled';
                        } else if ( $gateway == PaymentGateway::GATEWAY_WIRETRANSFER ) {
                        	if ( !trim($request->input('ibanAccountNo')) ) {
                        		break;
                        	}

                            // Check country if available for bank
                            $country = Country::getCountryByCode($request->input('bankCountry'));
                            if ( !$country ) {
                                break;
                            }

                            if ( !$country->bank_enabled ) {
                                add_message(trans('user.payment_method.message_not_support_country'), 'danger');
                                
                                break;
                            }

                        	$data = [
                                'bankName'              => trim($request->input('bankName')),
                                'bankBranch'            => trim($request->input('bankBranch')),
                                'bankCountry'           => $request->input('bankCountry'),
                                'beneficiarySwiftCode'  => trim($request->input('beneficiarySwiftCode')),
                                'ibanAccountNo'         => trim($request->input('ibanAccountNo')),
                                'accountName'           => trim($request->input('accountName')),
                                'beneficiaryAddress1'   => trim($request->input('beneficiaryAddress1')),
                                'beneficiaryAddress2'   => trim($request->input('beneficiaryAddress2')),
                            ];

                            $userPaymentGateway->real_id = implode('_', [
                                $data['bankCountry'],
                                $data['ibanAccountNo'],
                                $data['beneficiarySwiftCode'],
                            ]);

                            // Wiretransfer is currently operated manually.
                            /*
                            $wireTransferGateway = new PaymentWireTransferGateway();
                            $dwollaAccessToken = $wireTransferGateway->getAccessToken();

                            if ( $dwollaAccessToken ) {
                                $account = $wireTransferGateway->getAccount($dwollaAccessToken);

                                $fundingSource = $wireTransferGateway->createFundingSource($dwollaAccessToken, $data);

                                if ( !$fundingSource ) {
                                	$bankResult = false;                                	
                                } else {
                                    $userPaymentGateway->params = json_encode([
                                        'funding_source' => $fundingSource,
                                        'account' => $account->url
                                    ]);

                                    $userPaymentGateway->real_id = $wireTransferGateway->getFundingSourceId($fundingSource);
                                }
                            } else {
                                $bankResult = false;
                            }
                            */

                            $country_field = 'bank_enabled';
                        } else {
                            $data = [
                                'firstName' 		=> trim($request->input('firstName')),
                                'lastName' 			=> trim($request->input('lastName')),
                                'cardType' 			=> trim($request->input('cardType')),
                                'expDateYear' 		=> $request->input('expDateYear'),
                                'expDateMonth' 		=> $request->input('expDateMonth'),
                                'cvv' 				=> trim($request->input('cvv')),
                            ];

                            if ( $request->input('expDateYear') . $request->input('expDateMonth') < date('Ym') ) {
                            	add_message(trans('user.payment_method.message_invalid_expired_date'), 'danger');

	                            break;
                            }

                            if ( $userPaymentGatewayId && !is_numeric($request->input('cardNumber')) ) {
                            	$data['cardNumber'] = ($userPaymentGateway->dataArray())['cardNumber'];
                            } else {
	                            $card = CreditCard::validCreditCard($request->input('cardNumber'), strtolower($request->input('cardType')));

	                            if ( !$card['valid'] ) {
	                                add_message(trans('user.payment_method.message_invalid_credit_cardnumber'), 'danger');

	                                break;
	                            }

	                            $data['cardNumber'] = substr(trim($request->input('cardNumber')), -4);

                            	$card_encrypted = encrypt_string(trim($request->input('cardNumber')));

                            	$userPaymentGateway->real_id = $card_encrypted;
                            }

                            /*
                            $paySafeGateway = new PaymentPaySafeGateway();
                            if ( $card = $paySafeGateway->createCard($data) ) {
                                $userPaymentGateway->params = json_encode([
                                    'card_id' => $card->id,
                                    'payment_token' => $card->paymentToken
                                ]);

                                $userPaymentGateway->real_id = $card->id;
                            } else {
                            	add_message(trans('user.payment_method.message_failed_add_payment_method'), 'danger');

                            	break;
                            }
                            */

                            /*
                            $creditCardGateway = new PaymentCreditCardGateway();
                            if ( $btCustomerId = $creditCardGateway->btCreateCustomer($data) ) {
                                $userPaymentGateway->params = json_encode([
                                    'customer_id' => $btCustomerId
                                ]);

                                $userPaymentGateway->real_id = $btCustomerId;
                            } else {
                            	add_message(trans('user.payment_method.message_failed_add_payment_method'), 'danger');

                            	break;
                            }
                            */

                            $country_field = 'creditcard_enabled';
                        }
						
                        // Check country available
                        if ( $user->contact->country && !$user->contact->country->{$country_field} ) {
                        	add_message(trans('user.payment_method.message_not_support_country'), 'danger');
                            
                            break;
                        }

						$userPaymentGateway->data = json_encode($data);
                        $userPaymentGateway->is_pending = UserPaymentGateway::IS_PENDING_NO; // ## The pending status will be "NO": CHANGED BY KCG
                        $userPaymentGateway->status = UserPaymentGateway::IS_STATUS_YES;

                        // For Paypal gateway
                        /*
                        if ( !$userPaymentGatewayId ) {
	                        if ( $gateway == PaymentGateway::GATEWAY_PAYPAL ) {
	                        	$userPaymentGateway->status = UserPaymentGateway::IS_STATUS_NO;
	                        } else {
	                        	$userPaymentGateway->status = UserPaymentGateway::IS_STATUS_YES;
	                        }
	                    }
                        */

                        if ( $userPaymentGateway->save() ) {

                        	/*
                            if ( $userPaymentGateway->paymentGateway->isPaypal() ) {
								// Authenticate an account through payment gateway
								$params = [
									'ReturnURL' => route('user.payment_method', ['id' => $userPaymentGateway->id]),
									'CancelURL' => $currentUrl,
									'LogoutURL' => $currentUrl,
								];

								$paypalGateway = new PaymentPaypalGateway();
								return $paypalGateway->getAuthenticate($params);
                        	}
                            */

                            // Update file
                            if ( $userPaymentGateway->file_id ) {
                                File::where('id', $userPaymentGateway->file_id)
                                    ->update([
                                        'target_id' => $userPaymentGateway->id,
                                        'is_approved' => 1
                                    ]);
                            }

                    		$emailKey = 'PAYMENT_METHOD_ADDED';

                    		if ( $userPaymentGatewayId ) {
                    			add_message(trans('user.payment_method.message_success_update_payment_method'), 'success');

                                $emailKey = 'PAYMENT_METHOD_UPDATED';
                    		} else {
                    			add_message(trans('user.payment_method.message_success_add_payment_method'), 'success');
                    		}

                            EmailTemplate::send($user, $emailKey, 0, [
	                            'USER' => $user->fullname(),
                                'CONTACT_US_URL' => route('frontend.contact_us'),
	                        ]);

                            $user->updateLastActivity();
                        } else {
                        	if ( $userPaymentGatewayId ) {
                        		add_message(trans('user.payment_method.message_failed_update_payment_method'), 'danger');
                        	} else {
                        		add_message(trans('user.payment_method.message_failed_add_payment_method'), 'danger');
                        	}
                        }

                        break;

                    case 'deletePaymentGateway':
                        if ( UserPaymentGateway::where('id', $request->input('_gatewayId'))
                        						->where('user_id', $user->id)
                        						->delete() ) {

                        	EmailTemplate::send($user, 'PAYMENT_METHOD_REMOVED', 0, [
	                            'USER' => $user->fullname(),
                                'CONTACT_US_URL' => route('frontend.contact_us'),
	                        ]);

                            add_message(trans('user.payment_method.message_success_delete_payment_method'), 'success');

                            $user->updateLastActivity();
                        } else {
                            add_message(trans('user.payment_method.message_failed_delete_payment_method'), 'danger');
                        }

                        break;

                    case 'makePrimaryPaymentGateway':
                        // Set all user payment gateways as disabled
                        UserPaymentGateway::where('user_id', $user->id)->update(['is_primary' => 0]);

                        // Set the selectd payment gateway primary
                        //$userPaymentGateway = UserPaymentGateway::findOrFail($request->input('_gatewayId'));
                        $userPaymentGateway = UserPaymentGateway::where('id', $request->input('_gatewayId'))
                        										->where('user_id', $user->id)
                        										->first();

                        if ( !empty($userPaymentGateway) ) {
                             $userPaymentGateway->is_primary = 1;

                            if ( $userPaymentGateway->save() ) {
                                add_message(trans('user.payment_method.message_success_set_as_primary'), 'success');
                                $user->updateLastActivity();
                            } else {
                                add_message(trans('user.payment_method.message_failed_set_as_primary'), 'danger');
                            }
                        }                        

                        break;
                    
                    default:
                        break;
                }

                return redirect()->route('user.payment_method');

            }
        } catch ( Exception $e ) {
        	Log::error('[PaymentController::payment_method()] ' . $e->getMessage());

			add_message(trans('user.payment_method.message_failed_add_payment_method'), 'danger');        	
        }

        // Get the payment gateway list
        $paymentGateways = PaymentGateway::getActiveGateways();

        return view('pages.user.payment_method', [
            'page' => 'user.payment_method',
            'payment_gateways' => $paymentGateways,
            'countries' => $countries,
            'j_trans' => [
                'confirm_delete_payment_method' => trans('user.payment_method.confirm_delete_payment_method'),
                'ok' => trans('common.ok'),
                'save' => trans('common.save'),
                'cancel' => trans('common.cancel'),
                'iso_time' => date('c')
            ],
        ]);
    }

    /**
    * user/deposit
    *
    * @author Ro Un Nam
    * @param  Request $request
    * @return Response
    */
    public function deposit(Request $request) {
        $user = Auth::user();

        $returnUrl = route('user.deposit');

        $action = '';

        $depositAmount = '';

        $payment_gateway_id = 0;

        $wallet = $user->myBalance(false);

        $amountUnderHolding = $user->isBuyer() ? $user->getTotalAmountUnderWorkAndReview() : 0;

        $balance = $newBalance = $wallet - $amountUnderHolding;

        $cny_exchange_rate = Settings::get('CNY_EXCHANGE_RATE');
        $currency_sign = Settings::get('CURRENCY_SIGN');
        $max_wechat_amount = Settings::get('DEPOSIT_WECHAT_MAX_AMOUNT');

        try {

	        if ( $request->isMethod('post') ) {
				if ( $user->isSuspended() || $user->isFinancialSuspended() ) {
					return redirect()->to($returnUrl);
				}

	            $action = $request->input('_action');

	            switch ( $action ) {
	                case 'requestDeposit':
	                    if ( $request->input('deposit_amount') ) {
	                        $depositAmount = floatval($request->input('deposit_amount'));
	                    }

	                    if ( $request->input('payment_gateway') ) {
	                        $payment_gateway_id = floatval($request->input('payment_gateway'));
	                    }

	                    break;

	                case 'previewDeposit':
	                    $depositAmount = floatval($request->input('deposit_amount'));
	                    $newBalance = $wallet + $depositAmount;
	                    $paymentGateway = UserPaymentGateway::findOrFail($request->input('payment_gateway'));

                        if ( !$paymentGateway->enabledDeposit() ) {
                            add_message(trans('user.deposit.message_failed_no_payment_method'), 'danger');

                            return redirect()->route('user.deposit', ['_action' => 'requestDeposit']);
                        }

                        $fee = $depositAmount * $paymentGateway->depositFee() / 100;

                        // Waiting for WeChat QR code
                        $wechatQueueId = 0;

                        /*
                        if ( $paymentGateway->isWeixin() ) {
							// Check if system has already request in queue, then just ignore current request
							$existedQueue = QueueWechatDeposit::where('status', QueueWechatDeposit::STATUS_WAITING_QRCODE)->first();

							if ( $existedQueue ) {
								add_message(trans('user.deposit.message_failed_waiting_for_wechat_qrcode'), 'warning');

								return redirect()->route('user.deposit', ['_action' => 'requestDeposit']);
							} else {
	                            $convertedDepositAmount = number_format(($depositAmount + $fee) * $cny_exchange_rate, 2, '.', '');

                                if ( $convertedDepositAmount > 300000 ) {
                                    add_message(trans('user.deposit.message_failed_exceed_maximum_amount', ['currency' => trans('common.cny'), 'amount' => formatCurrency($max_wechat_amount)]), 'danger');

                                    return redirect()->route('user.deposit', ['_action' => 'requestDeposit']);    
                                }

	                            $wechatQueue = new QueueWechatDeposit;
	                            $wechatQueue->user_id = $user->id;
	                            $wechatQueue->user_payment_gateway_id = $paymentGateway->id;
	                            $wechatQueue->amount = $convertedDepositAmount;
	                            $wechatQueue->original_amount = $depositAmount;
	                            
                                if ( $wechatQueue->save() ) {
                                    $wechatQueueId = $wechatQueue->id;
                                }
	                        }
                        }
                        */

	                    return view('pages.buyer.user.deposit', [
	                        'page'         => 'buyer.user.deposit',
	                        'deposit_amount' => $depositAmount,
                            'fee' => $fee,
                            'holding_amount' => $amountUnderHolding,
                            'wallet' => $wallet,
	                        'new_balance' => $newBalance,
	                        'payment_gateway' => $paymentGateway,
	                        'action' => $action,
                            'cny_exchange_rate' => $cny_exchange_rate,
                            'wechat_queue_id' => $wechatQueueId,
	                        'j_trans' => [
				                'cse_url' => route('user.deposit.csetoken'),
                                'cny_exchange_rate' => $cny_exchange_rate,

                                'free' => trans('common.free'),
                                'tip_fee_of_deposit_amount' => trans('user.deposit.tip_fee_of_deposit_amount')
				            ],
	                    ]);

	                case 'deposit':

	                    $amount = floatval($request->input('deposit_amount'));
	                    $amount = round($amount * 100) / 100;
	                    $userPaymentGateway = UserPaymentGateway::findOrFail($request->input('payment_gateway'));

	                    if ( !$userPaymentGateway || !$userPaymentGateway->enabledDeposit() ) {
	                        add_message(trans('user.deposit.message_failed_no_payment_method'), 'danger');
	                    } else {
	                    	$user->updateLastActivity();

	                    	if ( $userPaymentGateway->paymentGateway->isPaypal() ) {
                                $fee = $amount * $userPaymentGateway->depositFee() / 100;

						        $paypalGateway = new PaymentPaypalGateway();

                                $real_amount = $amount + $fee;
                                $real_amount = floatval(sprintf('%.2f', $real_amount));
                                
						        $params = [
									'ReturnURL' => route('user.deposit', ['gatewayID' => $userPaymentGateway->id, 'amount' => $amount]),
									'CancelURL' => $returnUrl,
									'amount'    => $real_amount,
									'itemName'  => 'Deposit',
								];

						        $result = $paypalGateway->setExpressCheckout($params);

                                if ( $result ) {
                                    return $result;
                                }
                                
	                    	} else if ( $userPaymentGateway->paymentGateway->isCreditCard() ) {
	                    		// $params = json_decode($userPaymentGateway->params, true);
	                    		// $params['amount'] = $amount;

                                // $paySafeGateway = new PaymentPaySafeGateway();
                                // $chargeResult = $paySafeGateway->cardDeposit($params);

                                // $creditCardGateway = new PaymentCreditCardGateway();
                                // $chargeResult = $creditCardGateway->charge($params);

						        $params = [
						        	'amount' => $amount,
						        	'card_encrypted' => $request->input('_tokenCSE')
						        ];

						        $adyenGateway = new PaymentAdyenGateway();
						        $chargeResult = $adyenGateway->authorize($params);

						        if ( $chargeResult ) {
						        	TransactionLocal::charge($user->id, $amount, $userPaymentGateway->id, $chargeResult['id']);
								}
                            } else if ( $userPaymentGateway->paymentGateway->isSkrill() ) {
                                /*
                                $skrillGateway = new PaymentSkrillGateway();

                                $params = [
                                	'return_url' => route('user.deposit', ['gatewayID' => $userPaymentGateway->id]),
									'cancel_url' => $returnUrl,
                                    'amount' => $amount,
                                ];
                                
                                return $skrillGateway->deposit($params);
                                */

                                $chargeResult = true;
                                TransactionLocal::charge($user->id, $amount, $userPaymentGateway->id);
                                
						    } else if ( $userPaymentGateway->paymentGateway->isPayoneer() ) {

                                $chargeResult = true;
                                TransactionLocal::charge($user->id, $amount, $userPaymentGateway->id);

                            } else if ( $userPaymentGateway->paymentGateway->isWireTransfer() ) {
                                /*
                                $wireTransferGateway = new PaymentWireTransferGateway();

                                $dwollaAccessToken = $wireTransferGateway->getAccessToken();

                                $chargeResult = false;
                                if ( $dwollaAccessToken ) {
                                    $gateway_data = json_decode($userPaymentGateway->params, true);
                                    $params = [
                                        'amount' => $amount,
                                        'from' => $gateway_data['funding_source']
                                    ];

                                    $transaction_id = $wireTransferGateway->deposit($dwollaAccessToken, $params);

                                    if ( $transaction_id ) {
                                        $chargeResult = true;
                                        
                                        TransactionLocal::charge($user->id, $amount, $userPaymentGateway->id, $transaction_id);
                                    }
                                }
                                */

                                $chargeResult = true;
                                TransactionLocal::charge($user->id, $amount, $userPaymentGateway->id, $request->input('deposit_reference'), ['deposit_date' => $request->input('deposit_date')]);
                            } else if ( $userPaymentGateway->paymentGateway->isWeixin() ) {
						    	/*
                                $wePayGateway = new PaymentWePayGateway();
						    	$requestResult = $wePayGateway->requestPayment([
						    		'amount' => $amount,
						    		'product_id' => $user->id . '_' . time(),
                                    'notify_url' => $returnUrl,
						    	]);

						    	// Debug
						    	$requestResult = [
									'return_code' => 'SUCCESS',
									'return_msg' => 'OK',
									'appid' => 'wx6da39b9d4b4ee7f1',
									'mch_id' => '1266322901',
									'nonce_str' => 'lJSGFaFZIB8OFhsH',
									'sign' => '91A5649AA6040488E7E0A7B47CB76913',
									'result_code' => 'SUCCESS',
									'prepay_id' => 'wx201512091131393c0909342d0265483741',
									'trade_type' => 'NATIVE',
									'code_url' => 'weixin://wxpay/bizpayurl?pr=jGCLNfC',
						    	];

						    	if ( $requestResult['return_code'] == 'SUCCESS' ) {
						    		$chargeResult = true;
						    		//Session::set('wepay_qrcode_url', $requestResult['code_url']);
                                    $request->session()->put('wepay_qrcode_url', $requestResult['code_url']);

						    		TransactionLocal::charge($user->id, $amount, $userPaymentGateway->id);
						    	} else {
						    		$chargeResult = false;
						    	}
                                */

                                $chargeResult = true;
                                TransactionLocal::charge($user->id, $amount, $userPaymentGateway->id);
	                    	}

		                    if ( isset($chargeResult) && $chargeResult ) {
		                    	// Weixin should be done from QR code
                                if ( $userPaymentGateway->paymentGateway->isWeixin() ) {
                                    add_message(trans('user.deposit.message_success_deposit', ['currency' => trans('common.cny') . ' ', 'amount' => formatCurrency($amount * $cny_exchange_rate)]), 'success');
                                } else {
                                    add_message(trans('user.deposit.message_success_deposit', ['currency' => '$', 'amount' => formatCurrency($amount)]), 'success');
                                }
		                    } else {
			                    add_message(trans('user.deposit.message_failed_deposit'), 'danger');

			                    if ( isset($error) ) {
			                    	add_message($error, 'danger');
			                    }
			                }
	                    }

                        return redirect()->to($returnUrl);
	                
	                default:
	                    break;
	            }
	        }

	        /*************** Process PayPal Payment *****************/
	        if ( $request->input('gatewayID') && $request->input('amount') && $request->input('token') && $request->input('PayerID') ) {
	        	$paypalGateway = new PaymentPaypalGateway();
	        	$payment_result = $paypalGateway->getExpressCheckout($request->input('token'));
	        	
	        	if ( $payment_result && $payment_result['TransactionID'] && $payment_result['amount'] ) {

					$amount = floatval($payment_result['amount']);
                    
                    $existed = TransactionLocal::where('order_id', $payment_result['TransactionID'])
                                               ->where('amount', $amount)
                                               ->where('user_id', $user->id)
                                               ->count();

                    if ( $existed ) {
                        add_message(trans('user.deposit.message_failed_payment'), 'danger');
                    } else {
    					TransactionLocal::charge($user->id, $amount, $request->input('gatewayID'), $payment_result['TransactionID'], [], TransactionLocal::STATUS_DONE);

    	            	add_message(trans('user.deposit.message_success_deposit', ['currency' => '$', 'amount' => formatCurrency($amount)]), 'success');
                    }
	        	} else {
					add_message(trans('user.deposit.message_failed_payment'), 'danger');
				}

				return redirect()->to($returnUrl);
	        }
			/********************************************************/

	        /*************** Process Skrill Payment *****************/
	        /*
            if ( $request->input('gatewayID') && $request->input('transaction_id') && $request->input('msid') ) {
	        	$skrillGateway = new PaymentSkrillGateway();
	        	$transaction = $skrillGateway->getTransaction($request->input('transaction_id'));

	        	if ( $transaction && $transaction['status'] == 2 && $transaction['mb_transaction_id'] ) {
					TransactionLocal::charge($user->id, $transaction['amount'], $request->input('gatewayID'), $transaction['mb_transaction_id']);

	            	add_message(trans('user.deposit.message_success_deposit', ['amount' => formatCurrency($transaction['amount'])]), 'success');
	        	} else {
                    add_message(trans('user.deposit.message_failed_payment'), 'danger');
                }

                return redirect()->to($returnUrl);
	        }
            */
			/********************************************************/
        } catch ( Exception $e ) {
            Log::error('[UserController.php::deposit()] ' . $e->getMessage());

            add_message($e->getMessage(), 'danger');

            return redirect()->to($returnUrl);
        }

        if ( !count($user->depositPaymentGateways) ) {
        	add_message(trans('user.payment_method.message_non_setup_payment_method_for_deposit', ['url' => route('user.payment_method')]), 'danger');
        } else if ( !$user->depositPrimaryPaymentGateway ) {
        	add_message(trans('user.payment_method.message_non_setup_primary_payment_method', ['url' => route('user.payment_method')]), 'danger');
        }

        // Get the last payment
        $lastPayment = TransactionLocal::getLastCharge($user->id);

        return view('pages.buyer.user.deposit', [
            'page'         => 'buyer.user.deposit',
            'wallet' => $wallet,
            'balance'      => $balance,
            'last_payment' => $lastPayment,
            'deposit_amount' => $depositAmount,
            'holding_amount' => $amountUnderHolding,
            'payment_gateway_id' => $payment_gateway_id,
            'action' => $action,
            'cny_exchange_rate' => $cny_exchange_rate,
            'j_trans'=> [
                'cny_exchange_rate' => $cny_exchange_rate,

                'free' => trans('common.free'),
                'tip_fee_of_deposit_amount' => trans('user.deposit.tip_fee_of_deposit_amount')
            ]
        ]);
    }

    public function generateCSEToken(Request $request) {
    	$user = Auth::user();

    	if ( $request->ajax() ) {
	    	$id = $request->input('id');

	    	$userPaymentGateway = UserPaymentGateway::where('id', $id)
	    											->where('user_id', $user->id)
	    											->first();

	    	if ( $userPaymentGateway ) {
	    		$data = $userPaymentGateway->dataArray();
	    		$data['cardNumber'] = decrypt_string($userPaymentGateway->real_id);

	    		$json = [
	    			'status' => 'success',
	    			'key' => config('adyen.client_public_key'),
	    			'data' => [
	    				'number' => decrypt_string($userPaymentGateway->real_id),
	    				'cvc' => $data['cvv'],
	    				'holderName' => $data['firstName'] . ' ' . $data['lastName'],
	    				'expiryMonth' => $data['expDateMonth'],
	    				'expiryYear' => $data['expDateYear'],
	    				'generationtime' => date('c'),
	    			]
	    		];

	    		return response()->json($json);
	    	}
	    }

		return response()->json([
			'status' => 'fail'
        ]);
    }

    /**
     * Get WeChat QR code
     */
	public function getWCQRCode(Request $request) {
		$user = Auth::user();

        $id = $request->input('id');
        $user_payment_gateway_id = $request->input('user_payment_gateway_id');

        $queue = QueueWechatDeposit::where('id', $id)
        							->where('user_id', $user->id)
                                    ->where('user_payment_gateway_id', $user_payment_gateway_id)
                                    ->where('status', QueueWechatDeposit::STATUS_WAITING_PAYMENT)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        if ( $queue ) {
        	$filename = $queue->id . '_' . strtotime($queue->created_at);
            $qrcode_filename = $filename . '.png';
            $qrcode_path = get_wc_qrcode_path() . '/' . $qrcode_filename;

            if ( file_exists($qrcode_path) ) {
                return response()->json([
                    'success' => true,
                    'qrcode' => route('user.deposit.wcqrcode.view', ['code' => $filename])
                ]);
            }
        }

        return response()->json([
            'success' => false
        ]);
	}

    /**
    * View WeChat QR code
    */
    public function viewWCQRCode(Request $request, $code = '') {
    	$user = Auth::user();

        list($queue_id, $timestamp) = explode('_', $code);

        $queue = QueueWechatDeposit::where('id', $queue_id)
        							->where('user_id', $user->id)
                                    ->where('status', QueueWechatDeposit::STATUS_WAITING_PAYMENT)
                                    ->first();

        if ( $queue ) {
            if ( strtotime($queue->created_at) == $timestamp ) {
                $qrcode_filename = $queue->id . '_' . strtotime($queue->created_at) . '.png';
                $qrcode_path = get_wc_qrcode_path() . '/' . $qrcode_filename;

                if ( file_exists($qrcode_path) ) {
                    header('Cache-Control: max-age=86400');
                    header('Content-Type: image/png');
                    header('Content-Length: ' . filesize($qrcode_path));

                    readfile($qrcode_path);
                    exit;
                }
            }
        }

        abort(404);
    }

    /**
    * Check payment from WeChat
    */
    public function checkWCPayment(Request $request) {
        $user = Auth::user();

        $queue = QueueWechatDeposit::where('id', $request->input('id'))
                                    ->where('user_id', $user->id)
                                    ->where('user_payment_gateway_id', $request->input('user_payment_gateway_id'))
                                    ->where('status', QueueWechatDeposit::STATUS_APPROVED_PAYMENT)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        if ( $queue ) {
        	add_message(trans('user.deposit.message_success_deposit', ['currency' => trans('common.cny'), 'amount' => formatCurrency($queue->original_amount)]), 'success');

            return response()->json([
	            'success' => true
	        ]);
        } else {
        	return response()->json([
	            'success' => false
	        ]);
        }
    }
}
