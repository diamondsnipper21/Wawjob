<?php namespace iJobDesk\Http\Controllers\Api\v1;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Api\v1\ApiController;

use Illuminate\Http\Request;

use Auth;
use Log;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Contract;
use iJobDesk\Models\UserToken;
use iJobDesk\Models\UserAnalytic;
use iJobDesk\Models\Project;
use iJobDesk\Models\Settings;

class AuthController extends ApiController {

	/**
	* Constructor
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Sync the time with server.
	*
	* @param  Request $request
	* @return JSON
	*/
	public function sync(Request $request)
	{
		return response()->json([
			'time' => time(),
		]);
	}

	/**
	* Validate user info by token and get relation info.
	*
	* @param  Request $request
	* @return JSON
	*/
	public function valid(Request $request) {
		$return = [
			'error' => trans('message.api.error.5'),
			'error_code' => 5
		];

		$payload = $this->parseJWT($request->header('JWT'));

		$token_row = UserToken::where('token', $payload['token'])
							  ->where('type', UserToken::TYPE_API_V1)
							  ->first();

		if ( $token_row ) {
			$user = User::find($token_row->user_id);

			if ( $user ) {
				if ( !$user->isFreelancer() ) {
					Auth::logout();

					$return = [
						'error' => trans('message.api.error.1'),
						'error_code' => 1
					];
				} else {
					if ( $user->isLoginBlocked() ) {
						$return = [
							'error' => trans('message.api.error.4'),
							'error_code' => 4
						];
					} else {
						switch ($user->status) {
							case User::STATUS_AVAILABLE:
							case User::STATUS_FINANCIAL_SUSPENDED:

								$contracts = Contract::leftJoin('users', 'contracts.buyer_id', '=', 'users.id')
													 ->where('users.status', User::STATUS_AVAILABLE)
													 ->where('contracts.contractor_id', $user->id)
													 ->where('contracts.type', Contract::TYPE_HOURLY)
													 ->whereIn('contracts.status', [
													 	Contract::STATUS_OPEN,
													 	Contract::STATUS_PAUSED,
													 ])
													 ->select('contracts.*')
													 ->get();

								$return = [
									'time' => time(),
									'name' => $user->fullname(),
									'contracts' => []
								];
								
								foreach ($contracts as $contract) {
									$return['contracts'][] = [
										'id' => $contract->id,
										'title' => $contract->title,
										'buyer' => $contract->buyer->fullname(),
										'status' => $contract->status,
										'rate' => $contract->price,
										'workdiary_url' => _route('workdiary.view', ['cid' => $contract->id]),
									];
								}

								$return = array_merge($return, $this->get_app_version());

								break;
							case User::STATUS_NOT_AVAILABLE:

								$return = [
									'error' => trans('message.api.error.2'),
									'error_code' => 2
								];

								break;
							case User::STATUS_SUSPENDED:

								$return = [
									'error' => trans('message.api.error.3'),
									'error_code' => 3
								];

								break;
							default:
								
								$return = [
									'error' => trans('message.api.error.1'),
									'error_code' => 1
								];

								break;
						}
					}
				}
			}
		}

		return response()->json($return);
	}

	/**
	* Authenticate user info.
	*
	* @param  Request $request
	* @return JSON
	*/
	public function login(Request $request) {
		$return = [
			'error' => trans('message.api.error.1'),
			'error_code' => 1
		];

		if ( $request->isMethod('get') ) {
			abort(404);
		}

		$payload = $this->parseJWT($request->header('JWT'));

		if ( $payload !== false && isset($payload['username']) && isset($payload['password']) ) {

			$checklist = ['username', 'email'];

	  		// Gather user information from request.
			$username = $payload['username'];
			$password = $payload['password'];

			if ( filter_var($username, FILTER_VALIDATE_EMAIL) ) {
				$credential = [
					'email' => $username,
					'password' => $password
				];
			} else {
				$credential = [
					'username' => $username,
					'password' => $password
				];
			}

			if ( Auth::attempt($credential, true) ) {
				$user = Auth::user();

				if ( !$user->isFreelancer() ) {
					Auth::logout();

					$return = [
						'error' => trans('message.api.error.1'),
						'error_code' => 1
					];
				} else {

					if ( $user->isLoginBlocked() ) {
						Auth::logout();

						$return = [
							'error' => trans('message.api.error.4'),
							'error_code' => 4
						];
					} else {
						switch ($user->status) {
							case User::STATUS_AVAILABLE:
							case User::STATUS_FINANCIAL_SUSPENDED:

								$return = [
									'time' => time()
								];

				      			// Log user login
								UserAnalytic::insert([
									'user_id' => $user->id,
									'login_ipv4' => $_SERVER['REMOTE_ADDR'],
									'logged_at' => date("Y-m-d H:i:s")
								]);

								UserToken::where('user_id', $user->id)
										->where('type', UserToken::TYPE_API_V1)
										->delete();

								$return['name'] = $user->fullname();
								$return['token'] = str_random(118) . time();

								$user_token = new UserToken;
								$user_token->user_id = $user->id;
								$user_token->type = UserToken::TYPE_API_V1;
								$user_token->token = $return['token'];
								$user_token->save();

								$contracts = Contract::leftJoin('users', 'contracts.buyer_id', '=', 'users.id')
													 ->where('users.status', User::STATUS_AVAILABLE)
													 ->where('contracts.contractor_id', $user->id)
													 ->where('contracts.type', Contract::TYPE_HOURLY)
													 ->where('contracts.status', Contract::STATUS_OPEN)
													 ->select('contracts.*')
													 ->get();

								$return['contracts'] = [];

								foreach ($contracts as $contract) {
									$return['contracts'][] = [
										'id' => $contract->id,
										'title' => $contract->title,
										'buyer' => $contract->buyer->fullname(),
										'status' => $contract->status,
										'rate' => $contract->price,
										'workdiary_url' => _route('workdiary.view', ['cid' => $contract->id]),
									];
								}

								$return = array_merge($return, $this->get_app_version());

								break;
							case User::STATUS_NOT_AVAILABLE:
								Auth::logout();

								$return = [
									'error' => trans('message.api.error.2'),
									'error_code' => 2
								];

								break;
							case User::STATUS_SUSPENDED:
								Auth::logout();

								$return = [
									'error' => trans('message.api.error.3'),
									'error_code' => 3
								];

								break;
							default:
								Auth::logout();
								
								$return = [
									'error' => trans('message.api.error.1'),
									'error_code' => 1
								];

								break;
						}
					}
				}
			}
		}

		return response()->json($return);
	}

	/**
	* Log the user out.
	*
	* @return JSON
	*/
	public function logout(Request $request) {
		if ( $request->isMethod('get') ) {
			abort(404);
		}
		
		$jwt = $request->header('JWT');

		$payload = $this->parseJWT($jwt);
		$token = $payload['token'];

		$return = [
			'error' => false,
		];

		$userToken = UserToken::where('token', $token)
								->where('type', UserToken::TYPE_API_V1)
								->first();

		if ( $userToken ) {
			// Log user logout
			UserAnalytic::insert([
				'user_id' => $userToken->user_id,
				'type' => 1,
				'login_ipv4' => $_SERVER['REMOTE_ADDR'],
				'logged_at' => date("Y-m-d H:i:s")
			]);

			if ( !$userToken->delete() ) {
				$return = [
					'error' => trans('message.api.error.5'),
					'error_code' => 5,
				];
			}
		} else {
			$return = [
				'error' => trans('message.api.error.5'),
				'error_code' => 5,
			];
		}

		return response()->json($return);
	}

	public function get_app_version() {
		return [
			'version' => Settings::get('APP_VERSION'),
			'link' => _route('frontend.download_tools'),
		];
	}
}