<?php namespace iJobDesk\Models\Payment;

use Config;
use Session;
use Log;
use Exception;

use DwollaSwagger\Configuration;
use DwollaSwagger\ApiClient;
use DwollaSwagger\RootApi;
use DwollaSwagger\AccountsApi;
use DwollaSwagger\CustomersApi;
use DwollaSwagger\FundingsourcesApi;
use DwollaSwagger\MasspaymentsApi;
use DwollaSwagger\TransfersApi;

use iJobDesk\Models\Settings;

class PaymentWireTransferGateway {

	private $gateway;
	private $config;

	public function __construct() {
		$this->gateway = 'WireTransfer';
		
		$this->config = Config::get('dwolla');

		$this->url = ($this->config['mode'] == 'sandbox') ? 'https://sandbox.dwolla.com' : 'https://www.dwolla.com';

		$this->api_url = ($this->config['mode'] == 'sandbox') ? 'https://api-sandbox.dwolla.com' : 'https://api.dwolla.com';

		$this->currency = Settings::get('CURRENCY');
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function curl($url, $args = [], $header = []) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if ( $header ) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}

		if ( $args ) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
		}

		$data = curl_exec($ch);

		if ( !$data ) {
			throw new Exception('[PaymentWireTransferGateway::curl()] Error: ' . curl_error($ch));
		}

		curl_close($ch);

		$result = json_decode($data, true);

		return $result;
	}

	public function getAccessToken() {
		$args = [
			'client_id' => $this->config['key'],
			'client_secret' => $this->config['secret'],
			'grant_type' => 'client_credentials',
		];

		$url = $this->url . '/oauth/v2/token';
		
		$result = $this->curl($url, $args);

		if ( $result ) {
			return $result['access_token'];
		}

		return false;
	}

	public function getAccount($access_token) {
		try {
			/*
			$header = [
				'Content-Type: application/vnd.dwolla.v1.hal+json',
				'Accept: application/vnd.dwolla.v1.hal+json',
				'Authorization: Bearer ' . $access_token
			];

			$root = $this->curl($this->api_url, [], $header);
			*/

			Configuration::$access_token = $access_token;
			$apiClient = new ApiClient($this->api_url);
			$rootApi = new RootApi($apiClient);
			$root = $rootApi->root();

			if ( isset($root->_links) ) {
				$accountsApi = new AccountsApi($apiClient);

				$accountUrl = $root->_links['account']->href;
				$account = $accountsApi->id($accountUrl);

				if ( $account ) {
					// Get account funding sources
					$fsApi = new FundingsourcesApi($apiClient);

					$fundingSources = $fsApi->getAccountFundingSources($accountUrl);

					return [
						'id' => $account->id,
						'name' => $account->name,
						'url' => $accountUrl,
						'funding_sources' => $fundingSources->_embedded->{'funding-sources'},
					];
				}
			}
		} catch ( Exception $e ) {
			Log::error('[PaymentWireTransferGateway::getAccount()] Error: ' . $e->getMessage());
		}

		return false;
	}

	public function getFundingSourceId($funding_source) {
		return str_replace($this->api_url . '/funding-sources/', '', $funding_source);
	}

	public function createFundingSource($access_token, $params = []) {
		Configuration::$access_token = $access_token;
		$apiClient = new ApiClient($this->api_url);

		$fundingApi = new FundingsourcesApi($apiClient);

		try {
			$fundingSource = $fundingApi->createFundingSource([
				'routingNumber' => $params['routingNumber'],
				'accountNumber' => $params['accountNumber'],
				'bankAccountType' => 'checking',
				'name' => $params['accountHolderName']
			]);
		} catch ( Exception $e ) {
			Log::error('[PaymentWireTransferGateway::createFundingSource()] Error: ' . $e->getMessage());

			return false;
		}

		return $fundingSource;
	}

	public function deposit($access_token, $params = []) {
		Configuration::$access_token = $access_token;
		// Configuration::$debug = 1;

		$apiClient = new ApiClient($this->api_url);

		$transfersApi = new TransfersApi($apiClient);

		try {
			$transfer = $transfersApi->create([
				'_links' => [
					'source' => [
						'href' => $params['from'],
					],
					'destination' => [
						'href' => $this->api_url . '/funding-sources/' . $this->config['funding_source']
					]
				],
				'amount' => [
					'currency' => $this->currency,
					'value' => $params['amount']
				],
			]);

			if ( $transfer ) {
				// $transfer = 'https://api-sandbox.dwolla.com/transfers/19e5716c-7c18-e811-8105-0a595ef38714';

				// $transfer = $transfersApi->byId($transfer);
				$transfer = str_replace($this->api_url . '/transfers/', '', $transfer);
			}			
		} catch ( Exception $e ) {
			Log::error('[PaymentWireTransferGateway::deposit()] Error: ' . $e->getMessage());

			return false;
		}

		return $transfer;
	}

	public function withdraw($access_token, $params = []) {
		Configuration::$access_token = $access_token;

		$apiClient = new ApiClient($this->api_url);

		$transfersApi = new TransfersApi($apiClient);

		try {
			$transfer = $transfersApi->create([
				'_links' => [
					'source' => [
						'href' => $this->api_url . '/funding-sources/' . $this->config['funding_source'],
					],
					'destination' => [
						'href' => $params['to']
					]
				],
				'amount' => [
					'currency' => $this->currency,
					'value' => $params['amount']
				],
			]);

			if ( $transfer ) {
				$transfer = str_replace($this->api_url . '/transfers/', '', $transfer);
			}			
		} catch ( Exception $e ) {
			Log::error('[PaymentWireTransferGateway::withdraw()] Error: ' . $e->getMessage());

			return false;
		}

		return $transfer;
	}

}