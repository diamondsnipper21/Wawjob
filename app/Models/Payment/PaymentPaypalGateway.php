<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;

// Via Mass Pay
use PayPal\PayPalAPI\GetAuthDetailsReq;
use PayPal\PayPalAPI\GetAuthDetailsRequestType;
use PayPal\EBLBaseComponents\SetAuthFlowParamRequestDetailsType;
use PayPal\PayPalAPI\SetAuthFlowParamReq;
use PayPal\PayPalAPI\SetAuthFlowParamRequestType;

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\PayPalAPI\MassPayReq;
use PayPal\PayPalAPI\MassPayRequestItemType;
use PayPal\PayPalAPI\MassPayRequestType;
use PayPal\Auth\PPSignatureCredential;
use PayPal\Auth\PPTokenAuthorization;

// Via Express Checkout
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;

use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;

use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;

use PayPal\Service\PayPalAPIInterfaceServiceService;

// Via Rest Api
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

use PayPal\Api\Amount;
use PayPal\Api\Currency;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Payout;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\PayoutItem;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

// Via Adaptive Payments
use PayPal\Service\AdaptivePaymentsService;
use PayPal\Types\AP\FundingConstraint;
use PayPal\Types\AP\FundingTypeInfo;
use PayPal\Types\AP\FundingTypeList;
use PayPal\Types\AP\PayRequest;
use PayPal\Types\AP\Receiver;
use PayPal\Types\AP\ReceiverList;
use PayPal\Types\AP\SenderIdentifier;
use PayPal\Types\Common\PhoneNumberType;
use PayPal\Types\Common\RequestEnvelope;

use iJobDesk\Models\Settings;

class PaymentPaypalGateway {

	private $gateway;
	private $config;
	private $apiContext;

	public function __construct() {
		$this->gateway = 'Paypal';

		$this->config = [
			'mode' => Settings::get('PAYPAL_MODE') ? 'live' : 'sandbox',

			// 'client_id' => 'AW5tHYK54MNCMMTF8p5eInOdnU65cxs-hsJ_2vo-hxRn3q_6Xoc-2g4vPfJZqAUAxMwG4qzvKZal82Jf',
			// 'secret' => 'EPjbBPZFLnr7S_LyNz0bGnnUxLX9EDQgW-Cwb6J6fuUc6iwaN0DyBaySKD06Arp5jO0nsHI6jnW3rRxW',

			'client_id' => 'AS1gtBSm69WybMlWwt948mbAEZV2yWOo4BspUbmelmqnCMbqmr9pbL4uqYdyUnkNmXJNV0siDLIFilEx',
			'secret' => 'EJ732UFIvhbyR0SgMgDqRsaM6qYRMGhueqyu4l56fNwVn52ECtCWf9sSS9Cv-PKaQ_ABOPVNdW62mFh4',

			'acct1.UserName' => Settings::get('PAYPAL_API_USERNAME'),
			'acct1.Password' => Settings::get('PAYPAL_API_PASSWORD'),
			'acct1.Signature' => Settings::get('PAYPAL_API_SIGNATURE'),
			'acct1.AppId' => Settings::get('PAYPAL_APP_ID'),

			'http.ConnectionTimeOut' => 300,
			'log.LogEnabled' => true,
			'log.FileName' => storage_path() . '/logs/paypal.log',
			'log.LogLevel' => 'ERROR'
		];

		$this->apiContext = $this->getApiContext($this->config['client_id'], $this->config['secret']);

		$this->currency = Settings::get('CURRENCY');
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	/**
	* Send the payment
	*/
	public function withdraw($to, $total, $params = []) {
		$data = [
			'email' => $to,
			'amount' => $total,
		];

		// return $this->payout($data);

		// return $this->adaptivePay($data);
		
		// Using MassPay
		return $this->pay($data);
	}

	public function getAuthenticateUrl($token = '') {
		$mode = $this->config['mode'] == 'sandbox' ? 'sandbox.' : '';
		
		$url = 'https://www.' . $mode . 'paypal.com/webscr&cmd=_account-authenticate-login&token=' . $token;

		return $url;
	}

	/**
	* Get the token to authenticate the account added.
	* @param $params [ReturnURL, CancelURL, LogoutURL]
	*/
	public function getAuthenticate($params = []) {
        $reqDetails = new SetAuthFlowParamRequestDetailsType();
		$reqDetails->CancelURL = $params['CancelURL'];
		$reqDetails->ReturnURL = $params['ReturnURL'];
		$reqDetails->LogoutURL = $params['LogoutURL'];

		$reqType = new SetAuthFlowParamRequestType();
		$reqType->SetAuthFlowParamRequestDetails = $reqDetails;
		$req = new SetAuthFlowParamReq();
		$req->SetAuthFlowParamRequest = $reqType;

		$paypalService = new PayPalAPIInterfaceServiceService($this->config);
		try {
			$setAuthFlowParamResponse = $paypalService->SetAuthFlowParam($req);
			dd($setAuthFlowParamResponse);

			if ( $setAuthFlowParamResponse ) {
				if ( strtoupper($setAuthFlowParamResponse->Ack) == 'SUCCESS' ) {
					$payPalURL = $this->getAuthenticateUrl($setAuthFlowParamResponse->Token);
					return redirect()->away($payPalURL);
				} else {
					if ( $setAuthFlowParamResponse->Errors ) {
						Log::error('[PaymentPaypalGateway::getAuthenticate()] ' . $setAuthFlowParamResponse->Errors[0]->LongMessage);
					}
				}
			}
		} catch (Exception $e) {
			Log::error('[PaymentPaypalGateway::getAuthenticate()] ' . $e->getMessage());
		}

		return redirect()->to($params['ReturnURL']);
	}

	/**
	* Check the account added
	* @param $token
	*/
	public function checkAuthenticate($token = '') {
		if ( $token ) {
			$reqType = new GetAuthDetailsRequestType($token);
			$req = new GetAuthDetailsReq();
			$req->GetAuthDetailsRequest = $reqType;
			$paypalService = new PayPalAPIInterfaceServiceService($this->config);

			try {
				$getAuthDetailsResponse = $paypalService->GetAuthDetails($req);

				if ( $getAuthDetailsResponse ) {
					if ( strtoupper($getAuthDetailsResponse->Ack) == 'SUCCESS' ) {
						$payerID = $getAuthDetailsResponse->GetAuthDetailsResponseDetails->PayerID;
						
						return $payerID;
					}
				}
			} catch (Exception $e) {
				Log::error('[PaymentPaypalGateway::checkAuthenticate()] ' . $e->getMessage());
			}
		}

		return false;
	}

	/**
	* Send the payment using MassPay
	* @param $params [email or PayerID, amount]
	*/
	public function pay($params = []) {
		$result = [
			'success' => false
		];

		if ( !isset($params['email']) && !isset($params['PayerID']) ) {
			return $result;
		}

		$massPayRequest = new MassPayRequestType();
		$massPayRequest->MassPayItem = [];

		$masspayItem = new MassPayRequestItemType();
		$masspayItem->Amount = new BasicAmountType($this->currency, $params['amount']);
		
		if ( isset($params['email']) ) {
			$masspayItem->ReceiverEmail = $params['email'];
		} else if ( isset($params['PayerID']) ) {
			$masspayItem->ReceiverID = $params['PayerID'];
		}

		$massPayRequest->MassPayItem[] = $masspayItem;

		$massPayReq = new MassPayReq();
		$massPayReq->MassPayRequest = $massPayRequest;

		$paypalService = new PayPalAPIInterfaceServiceService($this->config);
		
		if ( isset($params['accessToken']) && isset($params['tokenSecret']) ) {
			$cred = new PPSignatureCredential($this->config['acct1.UserName'], $this->config['settings']['acct1.Password'], $this->config['acct1.Signature']);
			$cred->setThirdPartyAuthorization(new PPTokenAuthorization($params['accessToken'], $params['tokenSecret']));
		}

		try {
			if ( isset($params['accessToken']) && isset($params['tokenSecret']) ) {
				$massPayResponse = $paypalService->MassPay($massPayReq, $cred);
			} else {
				$massPayResponse = $paypalService->MassPay($massPayReq);
			}
		} catch (Exception $e) {
			Log::error('[PaymentPaypalGateway::massPay()] ' . $e->getMessage());
		}

		if ( isset($massPayResponse) ) {
			if ( strtoupper($massPayResponse->Ack) == 'SUCCESS' ) {
				$result['success'] = true;
				$result['transaction_id'] = $massPayResponse->CorrelationID;
			} else {
				if ( $massPayResponse->Errors ) {
					$result['error'] = $massPayResponse->Errors[0]->LongMessage;
					$result['error_code'] = $massPayResponse->Errors[0]->ErrorCode;
				}
			}
		}

		return $result;
	}

	public function getExpressCheckoutUrl($token = '') {
		$mode = $this->config['mode'] == 'sandbox' ? 'sandbox.' : '';
		
		$url = 'https://www.' . $mode . 'paypal.com/webscr&cmd=_express-checkout&token=' . $token;

		return $url;
	}

	public function setExpressCheckout($params = []) {
		
		// details about payment
		$paymentDetails = new PaymentDetailsType();

		$itemAmount = new BasicAmountType($this->currency, $params['amount']);
		
		$itemDetail = new PaymentDetailsItemType();
		$itemDetail->Name = $params['itemName'];
		$itemDetail->Amount = $itemAmount;
		$itemDetail->Quantity = 1;
		$itemDetail->ItemCategory = 'Physical';
		
		$paymentDetails->PaymentDetailsItem[0] = $itemDetail;	
		$paymentDetails->ItemTotal = new BasicAmountType($this->currency, $params['amount']);
		$paymentDetails->OrderTotal = new BasicAmountType($this->currency, $params['amount']);
		$paymentDetails->PaymentAction = 'Sale';

		$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
		
		$setECReqDetails->PaymentDetails[0] = $paymentDetails;
		$setECReqDetails->CancelURL = $params['CancelURL'];
		$setECReqDetails->ReturnURL = $params['ReturnURL'];
		$setECReqDetails->NoShipping = 1;
		$setECReqDetails->AddressOverride = 0;
		$setECReqDetails->ReqConfirmShipping = 0;
		
		// Display options
		/*
		$setECReqDetails->cppheaderimage = $_REQUEST['cppheaderimage'];
		$setECReqDetails->cppheaderbordercolor = $_REQUEST['cppheaderbordercolor'];
		$setECReqDetails->cppheaderbackcolor = $_REQUEST['cppheaderbackcolor'];
		$setECReqDetails->cpppayflowcolor = $_REQUEST['cpppayflowcolor'];
		$setECReqDetails->cppcartbordercolor = $_REQUEST['cppcartbordercolor'];
		$setECReqDetails->cpplogoimage = $_REQUEST['cpplogoimage'];
		$setECReqDetails->PageStyle = $_REQUEST['pageStyle'];
		*/
		$setECReqDetails->BrandName = config('app.name');

		// Advanced options
		$setECReqDetails->AllowNote = 1;
		$setECReqType = new SetExpressCheckoutRequestType();
		$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;

		$setECReq = new SetExpressCheckoutReq();
		$setECReq->SetExpressCheckoutRequest = $setECReqType;

		$paypalService = new PayPalAPIInterfaceServiceService($this->config);
		
		try {
			$setECResponse = $paypalService->SetExpressCheckout($setECReq);

			if ( $setECResponse ) {
				if ( strtoupper($setECResponse->Ack) == 'SUCCESS' ) {
					$payPalURL = $this->getExpressCheckoutUrl($setECResponse->Token);
					return redirect()->away($payPalURL);
				} else {
					if ( $setECResponse->Errors ) {
						Log::error('[PaymentPaypalGateway::setExpressCheckout()] ' . $setECResponse->Errors[0]->LongMessage);
					}
				}
			}
		} catch (Exception $e) {
			Log::error('[PaymentPaypalGateway::setExpressCheckout()] ' . $e->getMessage());
		}

		return false;
	}

	public function getExpressCheckout($token = '') {
		if ( !$token ) {
			return false;
		}

		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);
		
		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

		$paypalService = new PayPalAPIInterfaceServiceService($this->config);
		
		try {
			$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);

			if ( $getECResponse ) {
				if ( strtoupper($getECResponse->Ack) == 'SUCCESS' ) {
					$token = $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token;
					$Payer = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Payer;
					$PayerID = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID;
					$PayerStatus = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerStatus;
					$amount = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value;
					$currencyID = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->currencyID;
					
					if ( $token && $PayerID ) {
						return $this->doExpressCheckout([
							'token' => $token,
							'Payer' => $Payer,
							'PayerID' => $PayerID,
							'PayerStatus' => $PayerStatus,
							'currency' => $currencyID,
							'amount' => $amount,
						]);
					}
				}
			}
		} catch (Exception $e) {
			Log::error('[PaymentPaypalGateway::getExpressCheckout()] ' . $e->getMessage());
		}

		return false;
	}

	public function doExpressCheckout($params = []) {
		if ( !$params['token'] || !$params['PayerID'] ) {
			return false;
		}

		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($params['token']);
		
		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

		$paypalService = new PayPalAPIInterfaceServiceService($this->config);
		
		try {
			$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);

			$orderTotal = new BasicAmountType();
			$orderTotal->currencyID = $this->currency;
			$orderTotal->value = $params['amount'];
			
			$paymentDetails = new PaymentDetailsType();
			$paymentDetails->OrderTotal = $orderTotal;

			if ( isset($params['notifyUrl']) ) {
				$paymentDetails->NotifyURL = $params['notifyUrl'];
			}

			$DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
			$DoECRequestDetails->PayerID = $params['PayerID'];
			$DoECRequestDetails->Token = $params['token'];
			$DoECRequestDetails->PaymentAction = 'Sale';
			$DoECRequestDetails->PaymentDetails[0] = $paymentDetails;

			$DoECRequest = new DoExpressCheckoutPaymentRequestType();
			$DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;

			$DoECReq = new DoExpressCheckoutPaymentReq();
			$DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

			try {
				$DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);

				if ( $DoECResponse ) {
					if ( strtoupper($getECResponse->Ack) == 'SUCCESS' ) {
						if ( isset($DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo) ) {
							$TransactionID = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID;
							$amount = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->GrossAmount->value;

							return [
								'TransactionID' => $TransactionID,
								'amount' => $amount,
							];
						}
					} else {
						if ( $getECResponse->Errors ) {
							Log::error('[PaymentPaypalGateway::doExpressCheckout()] ' . $getECResponse->Errors[0]->LongMessage);
						}
					}
				}
			} catch (Exception $e) {
				Log::error('[PaymentPaypalGateway::doExpressCheckout()] ' . $e->getMessage());
			}

		} catch (Exception $e) {
			Log::error('[PaymentPaypalGateway::doExpressCheckout()] ' . $e->getMessage());
		}

		return false;
	}

	/******************** Via Rest Api *********************/
	private function getApiContext($clientId, $clientSecret) {
	    $apiContext = new ApiContext(
	        new OAuthTokenCredential(
	            $clientId,
	            $clientSecret
	        )
	    );

	    $apiContext->setConfig([
            'mode' => $this->config['mode'],
            'log.LogEnabled' => true,
            'log.FileName' => storage_path() . '/logs/paypal.log',
            'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            'cache.enabled' => true,
        ]);

	    
	    return $apiContext;
	}

	public function getAccessToken() {
		$credential = new OAuthTokenCredential($this->config['client_id'], $this->config['secret']);

		$access_token = $credential->getAccessToken([
			'mode' => $this->config['mode']
		]);

		return $access_token;
	}

	/**
	* Transfer money from personal to business using PayPal
	*/
	public function deposit($params = []) {
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');

		$item = new Item();
		$item->setName($params['itemName'])
		    ->setCurrency($this->currency)
		    ->setQuantity(1)
		    ->setPrice($params['amount']);

		$itemList = new ItemList();
		$itemList->setItems([$item]);

		$amount = new Amount();
		$amount->setCurrency($this->currency)
			    ->setTotal($params['amount']);

		$transaction = new Transaction();
		$transaction->setAmount($amount)
				    ->setItemList($itemList)
				    ->setInvoiceNumber($this->uniqid());

		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($params['ReturnURL'])
		    		->setCancelUrl($params['CancelURL']);

		$payment = new Payment();
		$payment->setIntent('sale')
		    	->setPayer($payer)
		    	->setRedirectUrls($redirectUrls)
		    	->setTransactions([$transaction]);

		try {
		    $payment->create($this->apiContext);

		    $approvalUrl = $payment->getApprovalLink();

			return redirect()->away($approvalUrl);
		} catch (Exception $e) {
		    Log::error('[PaymentPaypalGateway::deposit()] ' . $e->getMessage());
		}

		return false;		
	}

	/**
	* Execute deposit payment after logged in and click confirm button in PayPal
	*/
	public function execute(Request $request) {
	    $paymentId = $request->input('paymentId');
	    $payerId = $request->input('PayerID');

	    $payment = Payment::get($paymentId, $this->apiContext);

		try {

		    $total = $payment->transactions[0]->amount->total;
		    $currency = $payment->transactions[0]->amount->currency;

		    $execution = new PaymentExecution();
		    $execution->setPayerId($payerId);

		    $transaction = new Transaction();
		    
		    $amount = new Amount();
		    $amount->setCurrency($currency)
		    		->setTotal($total);

		    $transaction->setAmount($amount);

		    $execution->addTransaction($transaction);
	    
	        $result = $payment->execute($execution, $this->apiContext);

	        if ( $result->state == 'approved' ) {
	        	return [
					'TransactionID' => $result->transactions[0]->related_resources[0]->sale->id,
					'amount' => $result->transactions[0]->amount->total,
				];
			}
	    } catch (Exception $e) {
	        Log::error('[PaymentPaypalGateway::execute()] ' . $e->getMessage());
	    }

	    return false;
	}

	/**
	* Create payout from business to personal
	*/
	public function payout($params = []) {
		$this->getAccessToken();

		$payouts = new Payout();

		$senderBatchHeader = new PayoutSenderBatchHeader();
		$senderBatchHeader->setSenderBatchId($this->uniqid())
		    				->setEmailSubject('Withdrawal');

		$senderItem = new PayoutItem();
		$senderItem->setRecipientType('Email')
				    ->setNote('Thanks for your patronage!')
				    ->setReceiver($params['email'])
				    ->setSenderItemId('User 1')
				    ->setAmount(new Currency('{
				                        "value":"' . $params['amount'] . '",
				                        "currency":"' . $this->currency . '"
				                    }'));

		$payouts->setSenderBatchHeader($senderBatchHeader)
		    	->addItem($senderItem);
		
		try {
		    $result = $payouts->createSynchronous($this->apiContext);
		} catch (Exception $e) {
		    Log::error('[PaymentPaypalGateway::payout()] ' . $e->getMessage());
		}

		return false;
	}
	/***********************************************/

	public function adaptivePay($params = []) {
		$result = [
			'success' => false
		];

		if ( !isset($params['email']) && !isset($params['PayerID']) ) {
			return $result;
		}

		$requestEnvelope = new RequestEnvelope('en_US');
		$actionType = 'PAY';
		$cancelUrl = url('/');
		$returnUrl = url('/');

		$receiver = new Receiver();
		$receiver->email = $params['email'];
		$receiver->amount = $params['amount'];

		$receiverList = new ReceiverList([$receiver]);

		$payRequest = new PayRequest($requestEnvelope, $actionType, $cancelUrl, $this->currency, $receiverList, $returnUrl);

		try {
			$service = new AdaptivePaymentsService($this->config);
			$response = $service->Pay($payRequest);	
			
			if ( strtoupper($response->responseEnvelope->ack) == 'SUCCESS' ) {
				$result['success'] = true;
				$result['transaction_id'] = $response->responseEnvelope->correlationId;
			} else {
				if ( $response->error ) {
					$result['error'] = $response->error[0]->message;
					$result['error_code'] = $response->error[0]->errorId;
				}
			}
		} catch ( Exception $e ) {
			Log::error('[PaymentPaypalGateway::adaptivePay()] ' . $e->getMessage());
		}

		return $result;
	}

}