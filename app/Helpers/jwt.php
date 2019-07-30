<?php
/**
* JWT services
*
* Mar 11, 2016 - sunlignt
*/

if ( !function_exists('generate_jwt') ) {
	/**
	* Generate JWT token.
	*
	* ie,
	* Header
	* {
	*   "typ": "JWT",
	*   "alg": "sha256"
	* }
	* Payload
	* {
	*   "iss": "scotch.io",
	*   "exp": 1300819380,
	*   "name": "Chris Sevilleja",
	*   "admin": true
	* }
	* iss : The issuer of the token
	* sub : The subject of the token
	* aud : The audience of the token
	* exp : This will probably be the registered claim most often used.
	*       This will define the expiration in NumericDate value.
	*       The expiration MUST be before the current date/time.
	* nbf : Defines the time before which the JWT MUST NOT be accepted for processing
	* iat : The time the JWT was issued. Can be used to determine the age of the JWT
	* jti : Unique identifier for the JWT. Can be used to prevent the JWT from being replayed. This is helpful for a one time use token.
	*
	* @return string
	*/
	function generate_jwt($secret, $payload, $header = false)
	{
		if (!$secret || !is_array($payload)) {
			return false;
		}

		$header = $header ? $header : ['typ' => 'JWT', 'alg' => 'sha256'];
		$algorithm = isset($header['alg']) ? $header['alg'] : 'sha256';
		$header = json_encode($header);
		$payload = json_encode($payload);
		$encoded = base64_encode($header) . '.' . base64_encode($payload);
		$signature = hash_hmac($algorithm, $encoded, $secret, false);

		return $encoded . '.' . $signature;
	}
}

if ( !function_exists('parse_jwt') ) {
	/**
	* Parse the JWT and return payload part.
	*
	* @return string
	*/
	function parse_jwt($secret, $token)
	{
		if (!$secret || !$token) {
			Log::debug('[parse_jwt - 901] Invalid secret or token.');
			return false;
		}

		$pieces = explode('.', $token, 3);
		// It's not constructed by 3 parts
		if (count($pieces) < 3) {
			Log::debug('[parse_jwt - 902] pieces should have 3 parts.');
			return false;
		}

		list($header, $payload, $signature) = $pieces;
		$parsed_header = json_decode(base64_decode($header), true);

		$algorithm = isset($parsed_header['alg']) ? $parsed_header['alg'] : 'sha256';

		$calc_sig = hash_hmac($algorithm, "$header.$payload", $secret, false);

		if ($signature != $calc_sig) {
			Log::debug("[parse_jwt - 903] signature mismatch.
				[sent signature = ] $signature
				[calculated signature = ] $calcSig");

			return false;
		}

		return json_decode(base64_decode($payload), true);
	}
}