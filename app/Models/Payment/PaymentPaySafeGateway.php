<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;
use Exception;

use iJobDesk\Settings;

use Paysafe\PaysafeApiClient;
use Paysafe\Environment;
use Paysafe\JSONObject;
use Paysafe\Request;
use Paysafe\CustomerVault\Profile;
use Paysafe\CustomerVault\Address;
use Paysafe\CustomerVault\Card;
use Paysafe\CardPayments\Authorization;

class PaymentPaySafeGateway {

	private $gateway;
	private $config;
	private $mode;
	private $api_client;
	private $unit = 100;

	public function __construct() {
		$this->gateway = 'PaySafe';
		
		$this->config = Config::get('paysafe');

		$this->mode = $this->config['mode'] == 'sandbox' ? Environment::TEST : Environment::LIVE;
		
		$this->api_client = new PaysafeApiClient($this->config['api_username'], $this->config['api_password'], $this->mode, $this->config['account_id']);
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function createCard($params = []) {
		try {
			// Create profile
			$profile = $this->api_client->customerVaultService()->createProfile(new Profile([
				'merchantCustomerId' => $this->uniqid(),
				'locale' => 'en_US',
				'firstName' => $params['firstName'],
				'lastName' => $params['lastName'],
				'email' => $params['email'],
				'phone' => $params['phoneNumber']
			]));
			
			if ( $profile->id ) {
				// Create address
				$address = $this->api_client->customerVaultService()->createAddress(new Address([
                    'nickName' => 'Home',
                    'street' => $params['address'],
                    'street2' => $params['address2'],
                    'city' => $params['city'],
                    'state' => $params['state'],
                    'country' => $params['country'],
                    'zip' => $params['zipCode'],
                    'phone' => $params['phoneNumber'],
                    'profileID' => $profile->id
                ]));

                if ( $address->id ) {
					$card = $this->api_client->customerVaultService()->createCard(new Card([
						'holderName' => $params['firstName'] . ' ' . $params['lastName'],
						'cardNum' => $params['cardNumber'],
						'cvv' => $params['cvv'],
						'cardExpiry' => [
							'month' => intval($params['expDateMonth']),
							'year' => $params['expDateYear']
						],
						'billingAddressId' => $address->id,
						'profileID' => $profile->id
					]));

					if ( $card->id ) {
						return $card;
					}
                }
			}
		} catch ( Exception $e ) {
			Log::error('[PaymentPaySafeGateway::createCard()] Error: ' . $e->getMessage());
		}

		return false;
	}

	public function cardDeposit($params = '') {
		try {
			$auth = $this->api_client->cardPaymentService()->authorize(new Authorization([
				'merchantRefNum' => $this->uniqid(),
				'amount' => $params['amount'] * $this->unit,
				'settleWithAuth' => true,
				'card' => [
					'paymentToken' => $params['payment_token'],
				]
			]));

			if ( $auth->id ) {
				return $auth;
			}
		} catch ( Exception $e ) {
			Log::error('[PaymentPaySafeGateway::cardDeposit()] Error: ' . $e->getMessage());
		}

		return false;
	}

	public function cardPayment($account_id) {
		$jsonObject = new JSONObject([
			'amount' => '2500',
			'merchantRefNum' => $this->uniqid(),
			'detail' => 'Payment for ' . $account_id,
			'linkedAccount' => $account_id,
		]);

		$request = new Request([
			'method' => Request::POST,
			'uri' => '/accountmanagement/v1/accounts/' . $this->config['account_id'] . '/credits',
			'body' => $jsonObject
		]);

		$response = $this->api_client->processRequest($request);

		return $response;
	}

}