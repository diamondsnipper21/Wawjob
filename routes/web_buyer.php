<?php

/*
|--------------------------------------------------------------------------
| Web Buyer Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Buyer
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth.buyer'], function () {
	Route::match(['get', 'post'], 'jobs/{type?}', ['as' => 'job.all_jobs', 'uses' => 'JobController@all_jobs'])->where(['type' => '(open)|(draft)|(archived)']);
	
  	// Job
	Route::group(['prefix' => 'job'], function () {

		Route::group(['middleware' => 'auth.active'], function () {
			Route::match(['get', 'post'], 'post', ['as' => 'job.create', 'uses' => 'JobController@create']);

			Route::match(['get', 'post'], '{id}/edit', ['as' => 'job.edit', 'uses' => 'JobController@edit_job'])->where(['id' => '[0-9a-zA-Z]+']);

			Route::match(['get', 'post'], '{id}/edit/{action}', ['as' => 'job.edit.repost', 'uses' => 'JobController@edit_job'])->where(['id' => '[0-9a-zA-Z]+']);

			Route::match(['get', 'post'], 'hire/{uid}', ['as' => 'job.hire_user', 'uses' => 'JobController@hire_user'])->where(['uid' => '[0-9a-zA-Z-]+']);

			Route::match(['get', 'post'], '{id}/hire/{uid}', ['as' => 'job.hire', 'uses' => 'JobController@hire'])->where(['id' => '[0-9a-zA-Z]+', 'uid' => '[0-9a-zA-Z-]+']);

			Route::match(['get', 'post'], '{id}/hire/{uid}/{pid?}', ['as' => 'job.hire', 'uses' => 'JobController@hire'])->where(['id' => '[0-9a-zA-Z]+', 'uid' => '[0-9a-zA-Z-]+', 'pid' => '[0-9a-zA-Z]+']);

			// Ajax controller to change the job status
			Route::match(['get', 'post'], '{id}/change-status/{status}', ['as' => 'job.change_status.ajax', 'uses' => 'JobController@change_status'])->where(['id' => '[0-9a-zA-Z]+']);

			Route::match(['get', 'post'], '{id}/change-public/{public}', ['as' => 'job.change_public.ajax', 'uses' => 'JobController@change_public'])->where(['id' => '[0-9a-zA-Z]+']);

			Route::post('send_invitation', ['as' => 'job.send_invitation.ajax', 'uses' => 'JobController@send_invitation']);

			Route::post('withdraw_offer', ['as' => 'job.withdraw_offer.ajax', 'uses' => 'JobController@withdraw_offer']);

			Route::post('{id}/invite-freelancers', ['as' => 'job.invite_freelancers', 'uses' => 'JobController@invite_freelancers'])->where(['id' => '[0-9a-zA-Z]+']);
			Route::post('{id}/invite-freelancers/{page}', ['as' => 'job.invite_freelancers_page', 'uses' => 'JobController@invite_freelancers'])->where(['id' => '[0-9a-zA-Z]+']);

			Route::post('{id}/proposals', ['as' => 'job.interviews', 'uses' => 'JobController@interviews'])->where(['id' => '[0-9a-zA-Z]+']);
			Route::post('{id}/proposals/{page}', ['as' => 'job.interviews_page', 'uses' => 'JobController@interviews'])->where(['id' => '[0-9a-zA-Z]+']);

			Route::post('{id}/hire-offers', ['as' => 'job.hire_offers', 'uses' => 'JobController@hire_offers'])->where(['id' => '[0-9a-zA-Z]+']);			
		});
		
		// Job Overview Page
		Route::match(['get', 'post'], '{id}/overview', ['as' => 'job.overview', 'uses' => 'JobController@overview'])->where(['id' => '[0-9a-zA-Z]+']);

		// Job Invite Freelancers Page
		Route::get('{id}/invite-freelancers', ['as' => 'job.invite_freelancers', 'uses' => 'JobController@invite_freelancers'])->where(['id' => '[0-9a-zA-Z]+']);
		Route::get('{id}/invite-freelancers/{page}', ['as' => 'job.invite_freelancers_page', 'uses' => 'JobController@invite_freelancers'])->where(['id' => '[0-9a-zA-Z]+']);

		// Job Interviews Page
		Route::get('{id}/proposals', ['as' => 'job.interviews', 'uses' => 'JobController@interviews'])->where(['id' => '[0-9a-zA-Z]+']);
		Route::get('{id}/proposals/{page}', ['as' => 'job.interviews_page', 'uses' => 'JobController@interviews'])->where(['id' => '[0-9a-zA-Z]+']);

		// Job Hire & Offers Page
		Route::get('{id}/hire-offers', ['as' => 'job.hire_offers', 'uses' => 'JobController@hire_offers'])->where(['id' => '[0-9a-zA-Z]+']);
	});

	Route::group(['prefix' => 'contract'], function () {
		Route::group(['middleware' => 'auth.active'], function () {
			// Dispute
			Route::group(['namespace' => 'Frontend\\Contract'], function () {
				Route::match(['post'], '{id}/dispute/refund', ['as' => 'contract.dispute.refund', 'uses' => 'DisputeController@refund'])->where(['id' => '[0-9]+']);
			});
		});
	});

	// Deposit
	Route::group(['middleware' => 'auth.security', 'namespace' => 'Frontend\\User'], function () {	
		Route::group(['prefix' => 'deposit'], function () {
			Route::get('/', ['as' => 'user.deposit', 'uses' => 'PaymentController@deposit']);
			Route::group(['middleware' => 'auth.active'], function () {
				Route::post('/', ['as' => 'user.deposit', 'uses' => 'PaymentController@deposit']);
			});

			Route::post('csetoken', ['as' => 'user.deposit.csetoken', 'uses' => 'PaymentController@generateCSEToken']);

			Route::post('wcqrcode', ['as' => 'user.deposit.wcqrcode.get', 'uses' => 'PaymentController@getWCQRCode']);

			Route::get('wcqrcode/{code}', ['as' => 'user.deposit.wcqrcode.view', 'uses' => 'PaymentController@viewWCQRCode'])->where(['code' => '[0-9_]+']);

			Route::post('wcpayment', ['as' => 'user.deposit.wcpayment', 'uses' => 'PaymentController@checkWCPayment']);
		});
	});

  	// My Freelancers   
	Route::match(['get', 'post'], 'my-freelancers/{tab?}', ['as' => 'contract.my_freelancers', 'uses' => 'ContractController@my_freelancers'])->where(['tab' => '(hired)|(saved)']);

  	// Work Diary
	Route::group(['prefix' => 'workdiary'], function () {
		Route::group(['middleware' => 'auth.active'], function () {
			Route::post('ajax', ['as' => 'workdiary.ajax', 'uses' => 'WorkdiaryController@ajaxAction']);
		});
	});

  	// Report
	Route::group(['prefix' => 'reports'], function () {
		Route::match(['get', 'post'], 'weekly-summary', ['as' => 'report.weekly_summary', 'uses' => 'ReportController@weekly_summary']);
	});

	// User-profile view save by buyer
	// created by sogwang 2017.4.18
	Route::group(['prefix' => 'freelancer'], function () {
		Route::match(['get', 'post'], 'create-view-history/{uid}', ['as' => 'create.view.history', 'uses' => 'UserController@create_view_history']);
	});

});