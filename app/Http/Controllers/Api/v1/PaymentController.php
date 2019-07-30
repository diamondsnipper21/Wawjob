<?php namespace iJobDesk\Http\Controllers\Api\v1;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Api\v1\ApiController;

use Illuminate\Http\Request;

use Auth;
use Exception;
use Endroid\QrCode\QrCode;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\QueueWechatDeposit;

class PaymentController extends ApiController {

	/**
	* Constructor
	*/
	public $cipher;
	
	// Constant token
	public $token = 'jFcWMVqCsXBhmXVsqCKyKXYVchNxB6VcxQndQKrfSGVGyqzpYJPMEdRbQc3CVZ6Ajq24wykweJHMnpx57FKLBDaWvGQJCrL3QMEdMvuGhxj7h7rnrY62FR4ULxxSFTfs';

	// Constant JWT token
	// eyJ0eXAiOiJKV1QiLCJhbGciOiJzaGEyNTYifQ==.eyJ0b2tlbiI6ImpGY1dNVnFDc1hCaG1YVnNxQ0t5S1hZVmNoTnhCNlZjeFFuZFFLcmZTR1ZHeXF6cFlKUE1FZFJiUWMzQ1ZaNkFqcTI0d3lrd2VKSE1ucHg1N0ZLTEJEYVd2R1FKQ3JMM1FNRWRNdnVHaHhqN2g3cm5yWTYyRlI0VUx4eFNGVGZzIn0=.90b51972587928f60af62b905f2644ef71ef11f4df2aceae0d182969161dca2b

	public function __construct()
	{
		parent::__construct();

		$this->cipher = 'aes-256-cbc';
	}

	/**
	* Encrypt the data
	*/
	public function encrypt($string) {
    	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));

    	$encrypted = openssl_encrypt($string, $this->cipher, $this->secret_wc_encrypt, OPENSSL_RAW_DATA, $iv);

    	return base64_encode($encrypted . '::' . $iv);
	}

	/**
	* Decrypt the data
	*/
	public function decrypt($string) {
		list($encrypted, $iv) = explode('::', base64_decode($string), 2);

		return openssl_decrypt($encrypted, $this->cipher, $this->secret_wc_encrypt, OPENSSL_RAW_DATA, $iv);
	}

	/**
	* Check the access token from app
	*/
	public function checkToken($payload) {
		if ( isset($payload['token']) && $payload['token'] == $this->token ) {
			return true;
		}

		return false;
	}

	/**
	* Send the request to create QR code for WeChat pay
	*/
	public function requestWeChatQrcode(Request $request) {
		if ( $request->isMethod('get') ) {
			abort(404);
		}

		try {
			$payload = $this->parseWCJWT($request->header('JWT'));

			if ( !$this->checkToken($payload) ) {
				return response()->json([
					'error_code' => 10, 
					'error' => 'Invalid token'
				]);
			}

			$queue = QueueWechatDeposit::where('status', QueueWechatDeposit::STATUS_WAITING_QRCODE)->first();

			if ( $queue ) {
				$data = [
					'id' => $queue->id,
					'user_id' => $queue->user_id,
					'amount' => $queue->amount,
				];

				return response()->json([
					'success' => true,
					'data' => $this->encrypt(json_encode($data))
				]);
			}

			return response()->json([
				'error_code' => 9, 
				'error' => 'Invalid queue request'
			]);
		} catch ( Exception $e ) {
			return response()->json([
				'error_code' => 100, 
				'error' => 'Exception: ' . $e->getMessage()
			]);
		}
	}

	/**
	* Upload a WeChat QR code created from app
	*/
	public function uploadWeChatQrCode(Request $request) {
		if ( $request->isMethod('get') ) {
			abort(404);
		}
		
		try {
			$payload = $this->parseWCJWT($request->header('JWT'));

			if ( !$this->checkToken($payload) ) {
				return response()->json([
					'error_code' => 10, 
					'error' => 'Invalid token'
				]);
			}

			$data = json_decode($this->decrypt($payload['data']), true);

			if ( isset($data['id']) && isset($data['qrcode']) ) {
				$queue = QueueWechatDeposit::find($data['id']);
				if ( $queue ) {
					$upload_path = get_wc_qrcode_path();
					$upload_file = $queue->id . '_' . strtotime($queue->created_at) . '.png';

					$qrCode = new QrCode($data['qrcode']);
					$qrCode->setSize(250);
					$qrCode->writeFile($upload_path . '/' . $upload_file);

					$queue->status = QueueWechatDeposit::STATUS_WAITING_PAYMENT;
					$queue->save();

					return response()->json([
						'success' => true
					]);
				}
			}

			return response()->json([
				'error_code' => 9, 
				'error' => 'Invalid queue request'
			]);
		} catch ( Exception $e ) {
			return response()->json([
				'error_code' => 100, 
				'error' => 'Exception: ' . $e->getMessage()
			]);
		}
	}

	/**
	* Deposit fund through WeChat after approved from app
	*/
	public function payWeChat(Request $request) {
		if ( $request->isMethod('get') ) {
			abort(404);
		}

		try {
			$payload = $this->parseWCJWT($request->header('JWT'));

			if ( !$this->checkToken($payload) ) {
				return response()->json([
					'error_code' => 10, 
					'error' => 'Invalid token'
				]);
			}

			$data = json_decode($this->decrypt($payload['data']), true);

			if ( isset($data['transactions']) ) {
				$transactions = $data['transactions'];

				foreach ( $transactions as $transaction ) {

					if ( is_array($transaction) && isset($transaction['id']) ) {
						$queue = QueueWechatDeposit::find($transaction['id']);

						if ( isset($transaction['transaction_timestamp']) && isset($transaction['transaction_url']) ) {

							// Check if it is duplicated payment
							$meta = [
								'timestamp' => $transaction['transaction_timestamp'], 
								'url' => $transaction['transaction_url']
							];

							$duplicated = TransactionLocal::where('meta', json_encode($meta))->exists();

							if ( !$duplicated ) {
								$result = TransactionLocal::charge(
									$queue->user_id, 
									$queue->original_amount, 
									$queue->user_payment_gateway_id, 
									$queue->id, 
									$meta, 
									TransactionLocal::STATUS_DONE
								);

								if ( $result ) {
									$queue->status = QueueWechatDeposit::STATUS_APPROVED_PAYMENT;
									$queue->save();
								} else {
									throw new Exception('Create deposit transaction error.');
								}
							} else {
								throw new Exception('Duplicated transaction');
							}
						} else {
							throw new Exception('Invalid QR code');
						}
					} else {
						throw new Exception('Invalid data structure');
					}
				}

				return response()->json([
					'success' => true
				]);
			}

			return response()->json([
				'error_code' => 9, 
				'error' => 'Invalid queue request'
			]);			
		} catch ( Exception $e ) {
			return response()->json([
				'error_code' => 100, 
				'error' => 'Exception: ' . $e->getMessage()
			]);
		}
	}
}