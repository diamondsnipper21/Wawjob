<?php namespace iJobDesk\Models\Payment;

// All references are from https://pay.weixin.qq.com/wiki/doc/api

use Config;
use Session;
use Exception;

use iJobDesk\Models\Settings;

class PaymentWePayGateway {

	private $gateway;
	private $config;
	private $data;

	public function __construct() {
		$this->gateway = 'WePay';
		
		$this->config = Config::get('wepay');

		$this->data = [
			'appid' => $this->config['app_id'],
			'mch_id' => $this->config['merchant_id']
		];
	}

	private function uniqid() {
		return md5(time() . uniqid());
	}

	public function setData($key, $value) {
		$this->data[$key] = $value;
	}

	public function setBulkData($data) {
		foreach ($data as $k => $v) {
			$this->data[$k] = $v;
		}
	}

	public function curlXml($xml, $url) {		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
		if ( $this->config['use_proxy'] ){
			curl_setopt($ch, CURLOPT_PROXY, $this->config['proxy_host']);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->config['proxy_port']);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if ( $this->config['use_cert'] ) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, $this->config['cert_path']);
			curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, $this->config['cert_key_path']);
		}

		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$data = curl_exec($ch);

		if ( !$data ) {
			throw new Exception('[PaymentWePayGateway::curlXml()] Error: ' . curl_error($ch));
		}

		curl_close($ch);

		$result = $this->parseXml($data);

		return $result;
	}

	/**
	* Generate random nonce string
	* @return String(32)
	*/
	public function generateNonce($length = 32) {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		
		$str = '';
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);  
		}

		return $str;
	}

	public function getUrlParams() {
		$params = '';

		foreach ($this->data as $k => $v) {
			if ( !$v || $k != 'sign' || is_array($v) ) {
				continue;
			}

			$params .= $k . '=' . $v . '&';
		}
		
		return trim($params, '&');
	}

	/**
	* https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_3
	* @return String(32)
	*/
	public function generateSign() {
		ksort($this->data);

		$string = $this->getUrlParams();

		$string = $string . '&key=' . $this->config['app_secret'];
		
		return strtoupper(md5($string));
	}

	public function generateXml() {
    	$xml = '<xml>';

    	foreach ($this->data as $k => $v) {
    		if ( is_numeric($v) ) {
    			$xml .= '<' . $k . '>' . $v . '</' . $k . '>';
    		} else {
    			$xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
    		}
        }

        $xml .= '</xml>';

        return $xml;
	}

	public function parseXml($xml) {	
		if ( !$xml ) {
			throw new Exception('[PaymentWePayGateway::parseXml()] Error: Invalid XML ' . $xml);
		}

        libxml_disable_entity_loader(true);
        
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

		return $result;
	}

	/**
	* https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
	* @param $params []
	* @return array
		(
			[return_code] => SUCCESS
			[return_msg] => OK
			[appid] => wx6da39b9d4b4ee7f1
			[mch_id] => 1266322901
			[nonce_str] => lJSGFaFZIB8OFhsH
			[sign] => 91A5649AA6040488E7E0A7B47CB76913
			[result_code] => SUCCESS
			[prepay_id] => wx201512091131393c0909342d0265483741
			[trade_type] => NATIVE
			[code_url] => weixin://wxpay/bizpayurl?pr=jGCLNfC
		)
	*/
	public function unifiedOrder($data = []) {
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

		if ( !isset($data['out_trade_no']) ) {
			throw new Exception('[PaymentWePayGateway::unifiedOrder()] Error: Not set up out_trade_no');
		} else if ( !isset($data['body']) ){
			throw new Exception('[PaymentWePayGateway::unifiedOrder()] Error: Not set up body');
		} else if ( !isset($data['total_fee']) ) {
			throw new Exception('[PaymentWePayGateway::unifiedOrder()] Error: Not set up total_fee');
		} else if ( !isset($data['trade_type']) ) {
			throw new Exception('[PaymentWePayGateway::unifiedOrder()] Error: Not set up trade_type');
		}
		
		if ( $data['trade_type'] == 'JSAPI' && !isset($data['openid']) ) {
			throw new Exception('[PaymentWePayGateway::unifiedOrder()] Error: Not set up openid if trade_type is JSAPI');
		}

		if ( $data['trade_type'] == 'NATIVE' && !isset($data['product_id']) ) {
			throw new Exception('[PaymentWePayGateway::unifiedOrder()] Error: Not set up product_id if trade_type is NATIVE');
		}
		
		if ( !isset($data['notify_url']) ) {
			$data['notify_url'] = 'http://www.weixin.qq.com/wxpay/pay.php';
		}
		
		$this->setBulkData($data);
		$this->setData('nonce_str', $this->generateNonce());
		$this->setData('sign', $this->generateSign());

		$xml = $this->generateXml();
		$response = $this->curlXml($xml, $url);
		
		return $response;
	}

	/**
	* https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_2
	* @param $params []
	*/
    public function orderQuery($data) {
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';

		if ( !isset($data['out_trade_no']) ) {
			throw new Exception('[PaymentWePayGateway::orderQuery()] Error: Not set up out_trade_no');
		} else if ( !isset($data['transaction_id']) ){
			throw new Exception('[PaymentWePayGateway::orderQuery()] Error: Not set up transaction_id');
		}

		$this->setBulkData($data);
		$this->setData('nonce_str', $this->generateNonce());
		$this->setData('sign', $this->generateSign());

		$xml = $this->generateXml();
		$response = $this->curlXml($xml, $url);
		
		return $response;
    }

	/**
	* https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_8
	* @param $params []
	*/
	public function report($data) {
		$url = 'https://api.mch.weixin.qq.com/payitil/report';

		if ( !isset($data['out_trade_no']) ) {
			throw new Exception('[PaymentWePayGateway::report()] Error: Not set up interface_url');
		} else if ( !isset($data['return_code']) ) {
			throw new Exception('[PaymentWePayGateway::report()] Error: Not set up return_code');
		} else if ( !isset($data['result_code']) ) {
			throw new Exception('[PaymentWePayGateway::report()] Error: Not set up result_code');
		} else if ( !isset($data['execute_time'])) {
			throw new Exception('[PaymentWePayGateway::report()] Error: Not set up execute_time');
		}

		$this->setBulkData($data);
		$this->setData('nonce_str', $this->generateNonce());
		$this->setData('user_ip', $_SERVER['REMOTE_ADDR']);
		$this->setData('time', date('YmdHis'));

		$xml = $this->generateXml();
		$response = $this->curlXml($xml, $url);
		
		return $response;
	}

	public function requestPayment($params = []) {
		$cny_exchange_rate = Settings::get('CNY_EXCHANGE_RATE');
		if ( !$cny_exchange_rate ) {
			$cny_exchange_rate = 1;
		}

		$data = [];

		$total_fee = $params['amount'] * $cny_exchange_rate;

		$data['body'] = config('app.name') . ' Deposit ' . $total_fee . ' CNY';
		$data['trade_type'] = 'NATIVE';
		$data['attach'] = $params['product_id'];
		$data['product_id'] = $params['product_id'];
		$data['detail'] = '';
		$data['out_trade_no'] = md5(date('YmdHis') . $params['product_id']);
		$data['total_fee'] = $total_fee;
		$data['notify_url'] = $params['notify_url'];
		$data['time_start'] = convertTz(date('Y-m-d H:i:s'), 'Asia/Shanghai', 'UTC', 'YmdHis');
		$data['time_expire'] = convertTz(date('Y-m-d H:i:s', strtotime('+10 minutes')), 'Asia/Shanghai', 'UTC', 'YmdHis');

		$result = $this->unifiedOrder($data);
		return $result;
	}

}