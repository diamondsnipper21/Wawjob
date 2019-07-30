<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Exception;

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\CreditCardDetailsType;
use PayPal\EBLBaseComponents\DoDirectPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\PayerInfoType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\PersonNameType;
use PayPal\PayPalAPI\DoDirectPaymentReq;
use PayPal\PayPalAPI\DoDirectPaymentRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;

use iJobDesk\Models\Settings;

class PaymentCreditCardGateway {

	private $gateway;
	private $config;
	private $config_braintree;

	public function __construct() {
		$this->gateway = 'CreditCard';

		$this->config = [
			'mode' => Settings::get('PAYPAL_MODE') ? 'live' : 'sandbox',

			'client_id' => 'AW5tHYK54MNCMMTF8p5eInOdnU65cxs-hsJ_2vo-hxRn3q_6Xoc-2g4vPfJZqAUAxMwG4qzvKZal82Jf',
			'secret' => 'EPjbBPZFLnr7S_LyNz0bGnnUxLX9EDQgW-Cwb6J6fuUc6iwaN0DyBaySKD06Arp5jO0nsHI6jnW3rRxW',

			'acct1.UserName' => Settings::get('PAYPAL_API_USERNAME'),
			'acct1.Password' => Settings::get('PAYPAL_API_PASSWORD'),
			'acct1.Signature' => Settings::get('PAYPAL_API_SIGNATURE'),
			'acct1.AppId' => Settings::get('PAYPAL_APP_ID'),

			'http.ConnectionTimeOut' => 300,
			'log.LogEnabled' => true,
			'log.FileName' => storage_path() . '/logs/paypal.log',
			'log.LogLevel' => 'ERROR'
		];

		$this->config_braintree = Config::get('braintree');

		\Braintree_Configuration::environment($this->config_braintree['mode']);
        \Braintree_Configuration::merchantId($this->config_braintree['merchantid']);
        \Braintree_Configuration::publicKey($this->config_braintree['publickey']);
        \Braintree_Configuration::privateKey($this->config_braintree['privatekey']);

        $this->currency = Settings::get('CURRENCY');
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function charge($params = []) {
		return $this->btCreateTransaction($params);
	}

	public function btCreateCustomer($params = []) {
        $customer = \Braintree_Customer::create([
			'firstName' => $params['firstName'],
			'lastName'  => $params['lastName'],
			'phone'     => $params['phoneNumber'],
			'email'     => $params['email'],
			'creditCard' => [
				'number'          => $params['cardNumber'],
				'cardholderName'  => $params['firstName'] . ' ' . $params['lastName'],
				'expirationMonth' => $params['expDateMonth'],
				'expirationYear'  => $params['expDateYear'],
				'cvv'             => $params['cvv'],
				'billingAddress' => [
					'postalCode'        => $params['zipCode'],
					'streetAddress'     => $params['address'],
					'extendedAddress'   => $params['address2'],
					'locality'          => $params['city'],
					'region'            => $params['state'],
					'countryCodeAlpha2' => $params['country']
				]
			]
		]);

		if ( $customer->success ) {
			return $customer->customer->id;
		}

		return false;
	}

	public function btCreateTransaction($params = []) {
		$sale = \Braintree_Transaction::sale([
			'customerId' => $params['customer_id'],
			'amount'   => $params['amount'],
			'orderId'  => $this->uniqid(),
			'options' => ['submitForSettlement' => true]
		]);

		if ( $sale->success ) {
			return $sale->transaction->id;
		}

		return false;
	}

	/**
	* Send the money using credit card via PayPal
	* @param $params
	*/
	public function doDirectPayment($params = []) {
		$result = [
			'success' => false
		];

		if ( !isset($params['cardType']) && !isset($params['cardNumber']) ) {
			return $result;
		}

		$address = new AddressType();
		$address->Name = $params['firstName'] . ' ' . $params['lastName'];
		$address->Street1 = $params['address'];
		$address->Street2 = '';
		$address->CityName = $params['city'];
		$address->StateOrProvince = $params['state'];
		$address->PostalCode = $params['zipCode'];
		$address->Country = $params['country'];
		$address->Phone = $params['phoneNumber'];

		$paymentDetails = new PaymentDetailsType();
		// $paymentDetails->ShipToAddress = $address;

		$paymentDetails->OrderTotal = new BasicAmountType($this->currency, $params['amount']);

		if ( isset($params['notifyUrl']) ) {
			$paymentDetails->NotifyURL = $params['notifyUrl'];
		}

		$personName = new PersonNameType();
		$personName->FirstName = $params['firstName'];
		$personName->LastName = $params['lastName'];
		
		$payer = new PayerInfoType();
		$payer->PayerName = $personName;
		$payer->Address = $address;
		$payer->PayerCountry = $params['country'];
		
		$cardDetails = new CreditCardDetailsType();
		$cardDetails->CreditCardNumber = $params['cardNumber'];
		$cardDetails->CreditCardType = $params['cardType'];
		$cardDetails->ExpMonth = $params['expDateMonth'];
		$cardDetails->ExpYear = $params['expDateYear'];
		$cardDetails->CVV2 = $params['cvv'];
		$cardDetails->CardOwner = $payer;
		
		$ddReqDetails = new DoDirectPaymentRequestDetailsType();
		$ddReqDetails->CreditCard = $cardDetails;
		$ddReqDetails->PaymentDetails = $paymentDetails;
		$ddReqDetails->PaymentAction = 'Sale';
		
		$doDirectPaymentReq = new DoDirectPaymentReq();
		$doDirectPaymentReq->DoDirectPaymentRequest = new DoDirectPaymentRequestType($ddReqDetails);

		$paypalService = new PayPalAPIInterfaceServiceService($this->config);

		try {
			$doDirectPaymentResponse = $paypalService->DoDirectPayment($doDirectPaymentReq);

			if ( $doDirectPaymentResponse ) {
				if ( strtoupper($doDirectPaymentResponse->Ack) == 'SUCCESS' ) {
					$result['success'] = true;
					$result['TransactionID'] = $doDirectPaymentResponse->TransactionID;
				} else {
					if ( $doDirectPaymentResponse->Errors ) {
						$result['error'] = $doDirectPaymentResponse->Errors[0]->LongMessage;
						$result['error_code'] = $doDirectPaymentResponse->Errors[0]->ErrorCode;
					}					
				}
			}
		} catch (Exception $e) {
			error_log('[PaymentCreditCardGateway::doDirectPayment()] ' . $e->getMessage());
		}

		return $result;
	}

}