<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;
use Exception;

use iJobDesk\Models\Settings;

class PaymentStripeGateway {

	private static $version = '2018-02-06';

	private $gateway;
	private $config;
	private $api_url;
	private $currency;

	public function __construct() {
		$this->gateway = 'Stripe';
		$this->api_url = 'https://api.stripe.com';
		$this->currency = Settings::get('CURRENCY');;		
		$this->config = Config::get('stripe');

		\Stripe\Stripe::setApiKey($this->config['api_key']);
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function getBalance() {
		return \Stripe\Balance::retrieve();
	}

	/**
	* @param $type ['card', 'bank_account', or 'account']
	*/
	public function createToken($params, $type = 'card') {
		if ( $type == 'bank_account' ) {
			$args = [
				'country' => $params['bankCountry'],
				'currency' => $this->currency,
				'account_holder_name' => $params['accountHolderName'],
				'account_holder_type' => 'individual', // or 'company'
				'routing_number' => $params['routingNumber'],
				'account_number' => $params['accountNumber'],
			];
		} else {
			$args = [
				'exp_month' => $params['expDateMonth'],
				'exp_year' => $params['expDateYear'],
				'number' => $params['cardNumber'],
				'address_city' => $params['city'],
				'address_country' => $params['country'],
				'address_line1' => $params['address'],
				'address_line2' => $params['address2'],
				'address_state' => $params['state'],
				'address_zip' => $params['zipCode'],
				'currency' => $this->currency,
				'cvc' => $params['cvv'],
				'default_for_currency' => true,
				'name' => $params['firstName'] . ' ' . $params['lastName'],
			];
		}

		try {
			return \Stripe\Token::create([
				$type => $args
			]);
		} catch ( Exception $e ) {
			Log::error('[PaymentStripeGateway::createToken()] ' . $e->getMessage());

			return false;
		}
	}

	public function createCustomer($params) {
		$type = 'card';
		if ( isset($params['bankName']) ) {
			$type = 'bank_account';
		}

		$token = $this->createToken($params, $type);

		if ( $token ) {
			$args = [
				'source' => $token->id,
			];

			if ( isset($params['email']) ) {
				$args['email'] = $params['email'];
			}

			try {
				$customer = \Stripe\Customer::create($args);

				return $customer;
			} catch ( Exception $e ) {
				Log::error('[PaymentStripeGateway::createToken()] ' . $e->getMessage());
			}
		}

		return false;
	}
}