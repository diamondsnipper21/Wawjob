<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => 'Api'], function () {
	/**
	* v1 APIs
	*/
	Route::group(['prefix' => 'v1', 'namespace' => 'v1'], function () {
		Route::match(['get', 'post'], 'login', 'AuthController@login');
		Route::match(['get', 'post'], 'logout', 'AuthController@logout');
		Route::group(['middleware' => 'auth.api_v1'], function () {
			Route::post('valid', 'AuthController@valid');
			Route::post('sync', 'AuthController@sync');
			Route::post('timelog', 'ContractController@timelog');
			Route::post('limit', 'ContractController@limit');
		});

		Route::match(['get', 'post'], 'requestWeChatQrCode', 'PaymentController@requestWeChatQrCode');
		Route::match(['get', 'post'], 'uploadWeChatQrCode', 'PaymentController@uploadWeChatQrCode');
		Route::match(['get', 'post'], 'payWeChat', 'PaymentController@payWeChat');
	});
});
