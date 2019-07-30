<?php namespace iJobDesk\Http\Controllers\Api\v1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Config;
use Auth;

abstract class ApiController extends BaseController {

	use ValidatesRequests;

	/**
	* Secret key
	*/
	public $secret;

	/**
	* Secret key for WeChat app
	*/
	public $secret_wc;

	public function __construct() {
		$this->secret = Config::get('api.key.v1');
		$this->secret_wc = Config::get('api.key.wc_v1');
		$this->secret_wc_encrypt = Config::get('api.key.wc_encrypt');
	}

	/**
	* Generate JWT token.
	*
	* @return string
	*/
	public function generateJWT($payload, $header = false)
	{
		return generate_jwt($this->secret, $payload, $header);
	}

	/**
	* Parse the JWT and return payload part.
	*
	* @return string
	*/
	public function parseJWT($token)
	{
		return parse_jwt($this->secret, $token);
	}

	public function generateWCJWT($payload, $header = false)
	{
		return generate_jwt($this->secret_wc, $payload, $header);
	}

	public function parseWCJWT($token)
	{
		return parse_jwt($this->secret_wc, $token);
	}
}