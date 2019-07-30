<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;
use Exception;

use iJobDesk\Models\Settings;

class PaymentSkrillGateway {

	private static $version = '2.8';

	private $gateway;
	private $config;

	private $url;
	private $query_url;
	private $quickpay_url;
	private $quickpay_url2;
	private $verification_url;

	public function __construct() {
		$this->gateway = 'Skrill';
		
		$this->config = [
			'merchant_email' => Settings::get('SKRILL_MERCHANT_EMAIL'),
			'password' => Settings::get('SKRILL_MERCHANT_PASSWORD'),
			'secret_word' => Settings::get('SKRILL_MERCHANT_SECRET_WORD'),
			'merchant_id' => Settings::get('SKRILL_MERCHANT_ID'),
		];

		$this->url = 'https://www.skrill.com/app/pay.pl';
		$this->query_url = 'https://www.skrill.com/app/query.pl';
		$this->quickpay_url = 'https://pay.skrill.com';
		$this->quickpay_url2 = 'https://pay.skrill.com/app';
		$this->verification_url = 'https://api.skrill.com/mqi/customer-verifications/';

		$this->currency = Settings::get('CURRENCY');
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function curl($url, $args, $output = 'xml') {
		$fields = http_build_query($args);

		try {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			$data = curl_exec($ch);

			if ( !$data ) {
				throw new Exception('[PaymentSkrillGateway::curl()] Error: ' . curl_error($ch));
			}

			if ( $output == 'xml' ) {
				$result = $this->parseXml($data);
			} else {
				$result = $data;
			}

			curl_close($ch);
		} catch ( Exception $e ) {
			Log::error('[PaymentSkrillGateway::curl()] Error: ' . $e->getMessage());
			return false;
		}

		return $result;
	}

	public function generateSessionId($params = []) {
		$params['pay_to_email'] = $this->config['merchant_email'];
		$params['language'] = 'EN';
		$params['transaction_id'] = $this->uniqid();
		$params['prepare_only'] = 1;
		$params['recipient_description'] = 'Deposit to ' . config('app.name');
		$params['logo_url'] = config('app.url') . '/assets/images/common/logo.png';

		return $this->curl($this->quickpay_url, $params, 'text');
	}

	public function getTransaction($trn_id = '') {
		$params = [
			'action' => 'status_trn',
			'email' => $this->config['merchant_email'],
			'password' => md5($this->config['password']),
			'trn_id' => $trn_id
		];

		$transaction_result = $this->curl($this->query_url, $params, 'text');
		if ( $transaction_result ) {
			$transaction_result = preg_replace('/\t/', ' ', $transaction_result);
			$transaction_result = preg_replace('/\s+/', ' ', $transaction_result);
			$transaction_result = preg_replace("/\n/", ' ', $transaction_result);
			$transaction_result = preg_split("/[\s,]+/", $transaction_result);

			if ( count($transaction_result) >2 ) {
				if ( $transaction_result[0] == '200' && strtoupper($transaction_result[1]) == 'OK' ) {
					parse_str($transaction_result[2], $transaction);

					return $transaction;
				}
			}
		}

		return false;
	}

	public function authenticate($email) {
		$params = [];
		$params['email'] = $email;
		$params['merchantId'] = $this->config['merchant_id'];
		$params['password'] = md5($this->config['password']);

		$result = $this->curl($this->verification_url, $params, 'text');

		if ( $result ) {
			$result = json_decode($result, true);
		}

		if ( is_array($result) ) {
			if ( isset($result['verificationLevel']) && $result['verificationLevel'] >= 10 ) {
				return true;
			}
		}

		return false;
	}

	public function deposit($params = []) {
		$session_id = $this->generateSessionId($params);

		$url = $this->quickpay_url2 . '?sid=' . $session_id;

		return redirect()->away($url);
	}

	public function withdraw($email, $amount = 0, $params = []) {
		$params['action'] = 'prepare';
		$params['email'] = $this->config['merchant_email'];
		$params['password'] = md5($this->config['password']);
		$params['bnf_email'] = $email;
		$params['amount'] = $amount;
		$params['subject'] = 'Withdraw';
		$params['note'] = 'You will get paid shortly';
		$params['frn_trn_id'] = $this->uniqid();

		$prepareResult = $this->curl($this->url, $params);

		if ( $prepareResult && isset($prepareResult['sid']) ) {
			$transferResult = $this->curl($this->url, [
				'action' => 'transfer',
				'sid' => $prepareResult['sid']
			]);

			if ( $transferResult && isset($transferResult['transaction']) ) {
				return $transferResult['transaction'];
			}
		}

		return false;
	}

	public function parseXml($xml) {
		if ( !$xml ) {
			throw new Exception('[PaymentSkrillGateway::parseXml()] Error: Invalid XML ' . $xml);
		}

		libxml_disable_entity_loader(true);

		$parsed = simplexml_load_string($xml, 'SimpleXMLElement');

		$result = json_decode(json_encode($parsed), true);

		if ( isset($result['error']) ) {
			Log::error('[PaymentSkrillGateway::parseXml()] Error: ' . $result['error']['error_msg']);

			return false;
		}

		return $result;
	}

}