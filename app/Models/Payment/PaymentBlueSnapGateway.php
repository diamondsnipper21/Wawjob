<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;
use Exception;

use iJobDesk\Models\Settings;

class PaymentBlueSnapGateway {

	private $gateway;
	private $config;

	public function __construct() {
		$this->gateway = 'BlueSnap';
		$this->config = Config::get('bluesnap');

		$this->currency = Settings::get('CURRENCY');

    	\tdanielcox\Bluesnap\Bluesnap::init($this->config['mode'], $this->config['api_key'], $this->config['api_password']);
    }

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function createTransaction($params) {
		try {
			$response = \tdanielcox\Bluesnap\CardTransaction::create([
				'cardHolderInfo' => [
					'firstName' => $params['firstName'],
					'lastName' => $params['lastName'],
					'zip' => $params['zipCode']
				],				
				'creditCard' => [
					'cardNumber' => $params['cardNumber'],
					'expirationMonth' => $params['expDateMonth'],
					'expirationYear' => $params['expDateYear'],
					'securityCode' => $params['cvv']
				],
				'amount' => $params['amount'],
				'currency' => $this->currency,
				'recurringTransaction' => 'ECOMMERCE',
				'cardTransactionType' => 'AUTH_CAPTURE',
			]);

			if ( $response->failed() ) {
				$error = $response->data;
			}

			$transaction = $response->data;
		} catch ( Exception $e ) {
			Log::error('[PaymentBluesnapGateway::createTransaction()] ' . $e->getMessage());

			return false;
		}

		return $transaction;
	}
}