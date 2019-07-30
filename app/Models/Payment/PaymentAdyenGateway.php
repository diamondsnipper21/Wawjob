<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;
use Exception;

use iJobDesk\Models\Settings;

class PaymentAdyenGateway {

	private $gateway;
	private $config;

	public function __construct() {
		$this->gateway = 'Adyen';
		
		$this->config = Config::get('adyen');

		$this->currency = Settings::get('CURRENCY');
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function authorize($params = []) {
		try {
			$client = new \Adyen\Client();
			$client->setApplicationName(config('app.name'));
			$client->setUsername($this->config['username']);
			$client->setPassword($this->config['password']);
			$client->setEnvironment($this->config['env'] == 'live' ? \Adyen\Environment::LIVE : \Adyen\Environment::TEST);

			$service = new \Adyen\Service\Payment($client);

			$json = '{
				"amount": {
					"value": ' . ($params['amount'] * 100) . ',
					"currency": "' . $this->currency . '"
				},
				"reference": "Deposit",
				"merchantAccount": "' . $this->config['merchant'] . '",
				"additionalData": {
					"card.encrypted.json": "' . $params['card_encrypted'] . '"
				}
			}';

			$params = json_decode($json, true);

			$result = $service->authorise($params);

			if ( isset($result['authCode']) && isset($result['resultCode']) && isset($result['pspReference']) ) {

				if ( $result['resultCode'] == 'Authorised' && strlen($result['pspReference']) == 16 ) {
					return [
						'result' => true,
						'id' => $result['pspReference']
					];
				}
			}

			if ( isset($result['refusalReason']) ) {
				Log::error('[PaymenetAdyenGateway.php::authorize()] ' . $result['refusalReason']);
			}

		} catch ( Exception $e ) {
			Log::error('[PaymenetAdyenGateway.php::authorize()] ' . $e->getMessage());
		}

		return false;
	}

	public function payout($params = []) {
		try {
			$client = new \Adyen\Client();
			$client->setApplicationName(config('app.name'));
			$client->setUsername($this->config['username']);
			$client->setPassword($this->config['password']);
			$client->setEnvironment($this->config['env'] == 'live' ? \Adyen\Environment::LIVE : \Adyen\Environment::TEST);

			$service = new \Adyen\Service\Payout($client);

			$json = '{
				"amount": {
					"value": ' . ($params['amount'] * 100) . ',
					"currency": "' . $this->currency . '"
				},
				"card":{
					"number":"' . $params['cardNumber'] . '",
					"expiryMonth":"' . $params['expDateMonth'] . '",
					"expiryYear":"' . $params['expDateYear'] . '",
					"holderName":"' . $params['firstName'] . ' ' . $params['lastName'] . '"
				},
				"reference": "Withdraw ' . $this->uniqid() . '",
				"recurring": {
                	"contract": "PAYOUT"
              	},
              	"shopperEmail": "daniellu1987@outlook.com",
              	"shopperReference": "' . $params['firstName'] . ' ' . $params['lastName'] . ' ' . $this->uniqid() . '",
              	"selectedRecurringDetailReference": "LATEST",
				"merchantAccount": "' . $this->config['merchant'] . '"
			}';

			$params = json_decode($json, true);

			$result = $service->submitThirdParty($params);

			if ( isset($result['authCode']) && isset($result['resultCode']) && isset($result['pspReference']) ) {

				if ( $result['resultCode'] == 'Authorised' && strlen($result['pspReference']) == 16 ) {
					return [
						'result' => true,
						'id' => $result['pspReference']
					];
				}
			}

			if ( isset($result['refusalReason']) ) {
				Log::error('[PaymenetAdyenGateway.php::payout()] ' . $result['refusalReason']);
			}

		} catch ( Exception $e ) {
			Log::error('[PaymenetAdyenGateway.php::payout()] ' . $e->getMessage());
		}

		return false;
	}

}