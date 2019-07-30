<?php namespace iJobDesk\Http\Middleware;

use Closure;
use Config;

use iJobDesk\Models\UserToken;

class IsAuthenticated4ApiV1 {

	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle($request, Closure $next)
	{
		$payload = parse_jwt(Config::get('api.key.v1'), $request->header('JWT'));

		if ($payload !== false) {
			$token = $payload['token'];

			if ( UserToken::where('token', $token)->where('type', UserToken::TYPE_API_V1)->count() ) {
				return $next($request);
			}
		}

		return response()->json([
			'error' => trans('message.api.error.5'),
			'error_code' => 5
		]);
	}
}