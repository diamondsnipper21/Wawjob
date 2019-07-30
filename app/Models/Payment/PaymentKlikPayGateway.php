<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;
use Exception;

use iJobDesk\Settings;

class PaymentKlikPayGateway {

	private $gateway;
	private $config;

	private $url;

	public function __construct() {
		$this->gateway = 'Klik & Pay';
		
		$this->config = Config::get('klikpay');

		$this->url = 'https://www.klikandpay.com/paiement/server_server.pl';
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function curl($args) {
		$fields = http_build_query($args);

		try {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			$data = curl_exec($ch);

			if ( !$data ) {
				throw new Exception('[PaymentKlikPayGateway::curl()] Error: ' . curl_error($ch));
			}

			curl_close($ch);
		} catch ( Exception $e ) {
			Log::error('[PaymentKlikPayGateway::curl()] Error: ' . $e->getMessage());
			return false;
		}

		return $data;
	}

	public function directPayment($params) {
		$data = [
			'ID'         	=> $this->config['merchant_id'],
			'IP'         	=> $_SERVER['REMOTE_ADDR'],
			'NOM'        	=> $params['lastName'],
			'PRENOM'     	=> $params['firstName'],
			'ADRESSE'    	=> $params['address'],
			'CODEPOSTAL'	=> $params['zipCode'],
			'VILLE'			=> $params['city'],
			'STATE'			=> $params['state'],
			'PAYS'       	=> $params['country'],
			'TEL'        	=> $params['phoneNumber'],
			'EMAIL'      	=> $params['email'],
			'MONTANT'    	=> $params['amount'],
			'NUMCARTE'   	=> $params['cardNumber'],
			'EXPMOIS'    	=> $params['expDateMonth'],
			'EXPANNEE'   	=> $params['expDateYear'],
			'CVV'        	=> $params['cvv'],
			'KEY'        	=> $this->config['private_key']
		];

		return $this->curl($data);
	}

}