<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
Route::get('how-it-works', ['as' => 'frontend.how_it_works', 'uses' => 'HomeController@how_it_works']);

Route::get('help', ['as' => 'frontend.help', 'uses' => 'HomeController@help']);
Route::get('help/article/{slug}', ['as' => 'frontend.help.detail', 'uses' => 'HomeController@help']);
Route::get('help/search', ['as' => 'frontend.help.search', 'uses' => 'HomeController@help_search']);

Route::match(['get', 'post'], 'contact', ['as' => 'frontend.contact_us', 'uses' => 'HomeController@contact_us']);
Route::match(['get', 'post'], 'downloads', ['as' => 'frontend.download_tools', 'uses' => 'HomeController@download_tools']);

Route::match(['get', 'post'], 'coming-soon', ['as' => 'frontend.coming_soon', 'uses' => 'HomeController@coming_soon']);

Route::match(['get', 'post'], 'unsubscribe/{token}', ['as' => 'email.unsubscribe', 'uses' => 'EmailMarketingController@unsubscribe']);

// Login
Route::match(['get', 'post'], 'login', ['as' => 'user.login', 'uses' => 'AuthController@login']);

// Logout
Route::get('logout', ['as' => 'user.logout', 'uses' => 'AuthController@logout']);

// Signup
Route::group(['prefix' => 'signup'], function () {
	Route::match(['get', 'post'], '/', ['as' => 'user.signup', 'uses' => 'AuthController@signup']);
	
	Route::match(['get', 'post'], '/{role}', ['as' => 'user.signup.user', 'uses' => 'AuthController@signup_user'])->where(['role' => '(buyer)|(freelancer)']);
	
	Route::match(['get', 'post'], 'checkusername', ['as' => 'user.signup.checkusername', 'uses' => 'AuthController@signup_checkusername']);
	Route::match(['get', 'post'], 'checkemail', ['as' => 'user.signup.checkemail', 'uses' => 'AuthController@signup_checkemail']);
	
  	// Apr 19, 2016 - paulz
	Route::get('checkfield', ['as' => 'user.signup.checkfield', 'uses' => 'AuthController@signup_checkfield']);

	Route::match(['get', 'post'], 'success', ['as' => 'user.signup.success', 'uses' => 'AuthController@signup_success']);

	Route::match(['get', 'post'], 'verify/{token}', ['as' => 'user.signup.verify', 'uses' => 'AuthController@verify']);
});

// Password
Route::group(['prefix' => 'forgot'], function () {
	Route::match(['get', 'post'], '/', ['as' => 'forgot', 'uses' => 'PasswordController@forgot']);

	Route::get('reset', ['as' => 'forgot.reset', 'uses' => 'PasswordController@reset']);

	Route::match(['get', 'post'], 'reset/{token}', ['as' => 'forgot.reset', 'uses' => 'PasswordController@reset']);
});

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'search'], function () {
	Route::group(['prefix' => 'freelancers'], function () {
		Route::match(['get', 'post'], '/', ['as' => 'search.user', 'uses' => 'SearchController@user']);
		Route::post('save', ['as' => 'search.user.save', 'uses' => 'SearchController@save_user']);
	});
	Route::match(['get', 'post'], 'freelancers', ['as' => 'search.user', 'uses' => 'SearchController@user']);
	Route::match(['get', 'post'], 'jobs', ['as' => 'search.job', 'uses' => 'SearchController@job']);
	Route::match(['get', 'post'], 'rssjob', ['as' => 'search.rssjob', 'uses' => 'SearchController@rssjob']);

});

// After logged in
Route::group(['middleware' => 'auth.customer'], function () {
	Route::get('system/service', ['as' => 'system.service', 'uses' => 'SystemController@index']);
	
	Route::match(['get', 'post'], 'dashboard', ['as' => 'user.dashboard', 'uses' => 'HomeController@dashboard']);

	Route::match(['delete'], 'my-profile/remove-avatar', ['as' => 'user.my_profile.remove_avatar', 'uses' => 'MyProfileController@remove_avatar']);
  	// Job
	// Route::match(['get', 'post'], 'my-jobs', ['as' => 'job.my_jobs', 'uses' => 'JobController@my_jobs']);
  	// Contract
	Route::group(['prefix' => 'contracts'], function () {
		Route::match(['get', 'post'], '/{tab?}', ['as' => 'contract.all_contracts', 'uses' => 'ContractController@all_contracts'])->where(['tab' => '(active)|(archived)']);
	});

	Route::group(['prefix' => 'contract'], function () {
		Route::match(['get', 'post'], '{id}', ['as' => 'contract.contract_view', 'uses' => 'ContractController@contract_view'])->where(['id' => '[0-9a-zA-Z]+']);
		
		Route::group(['middleware' => 'auth.active'], function () {
			Route::match(['get', 'post'], '{id}/feedback', ['as' => 'contract.feedback', 'uses' => 'ContractController@feedback'])->where(['id' => '[0-9]+']);			
		});

		// Dispute
		Route::group(['namespace' => 'Frontend\\Contract'], function () {
			Route::match(['post'], '{id}/dispute/create', ['as' => 'contract.dispute.create', 'uses' => 'DisputeController@create'])->where(['id' => '[0-9]+']);
			Route::match(['post'], '{id}/dispute/{tid}/cancel', ['as' => 'contract.dispute.cancel', 'uses' => 'DisputeController@cancel'])->where(['id' => '[0-9]+'])->where(['tid' => '[0-9]+']);
			Route::match(['post'], '{id}/dispute/{tid}/send-message', ['as' => 'contract.dispute.send_message', 'uses' => 'DisputeController@send_message'])->where(['id' => '[0-9]+'])->where(['tid' => '[0-9]+']);
		});
	});

  	// Work Diary
	Route::group(['prefix' => 'workdiary'], function () {
		Route::get('/', ['as' => 'workdiary.view_first', 'uses' => 'WorkdiaryController@view_first']);
		Route::get('/{cid}', ['as' => 'workdiary.view', 'uses' => 'WorkdiaryController@view'])->where(['cid' => '[0-9a-zA-Z]+']);
	});

  	// Report
	Route::group(['prefix' => 'reports'], function () {
		Route::get('/', ['as' => 'report.index', 'uses' => 'ReportController@index']);
		Route::match(['get', 'post'], 'transactions', ['as' => 'report.transactions', 'uses' => 'ReportController@transactions']);
		Route::match(['get', 'post'], 'timesheet', ['as' => 'report.timesheet', 'uses' => 'ReportController@timesheet']);
	});

	/*
	|--------------------------------------------------------------------------
	| Message
	|--------------------------------------------------------------------------
	*/
	Route::group(['prefix' => 'messages'], function () {
		Route::match(['get', 'post'], '/{id?}', ['as' => 'message.list', 'uses' => 'MessageController@index'])->where(['id' => '[0-9a-zA-Z]+']);
	});

	/**
	 * User Ignore Warning
	 */
	Route::post('user/ignore-warning', ['as' => 'user.ignore_warning', 'uses' => 'UserController@ignore_warning']);

});

/**
 * Common Actions
 */
Route::post('/message/send/{id}', ['as' => 'message.send', 'uses' => 'Controller@create_message'])->where(['id' => '[0-9]+']);
Route::post('/message/{id}/unread/{type}', ['as' => 'message.unread', 'uses' => 'Controller@unread_message'])->where(['id' => '[0-9]+'])->where(['type' => '[0-9]+']);


Route::group(['prefix' => 'job'], function () {
	Route::match(['get', 'post'], '{id}', ['as' => 'job.view', 'uses' => 'JobController@view_job'])->where(['id' => '[0-9a-zA-Z]+']);		

	Route::match(['get', 'post'], '/{user_id}/feedbacks/{page}', ['as' => 'job.detail.feedbacks', 'uses' => 'JobController@load_ended_contracts'])->where(['user_id' => '[0-9]+'])->where(['page' => '[0-9]+']);		

	Route::match(['get', 'post'], 'search-skills', ['as' => 'job.search_skills.ajax', 'uses' => 'JobController@search_job_skills']);

	Route::match(['get', 'post'], 'search-locations', ['as' => 'job.search_locations.ajax', 'uses' => 'JobController@search_locations']);
	
	Route::match(['get', 'post'], 'search-languages', ['as' => 'job.search_languages.ajax', 'uses' => 'JobController@search_languages']);
});

// Public profile
Route::match(['get', 'post'], 'freelancer/{id}', ['as' => 'user.profile', 'uses' => 'UserController@profile'])->where(['id' => '[0-9a-zA-Z_\-]+']);

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'account', 'middleware' => 'auth.customer'], function () {
	Route::get('/', function () {
		return redirect()->route('user.contact_info');
	});

	Route::group(['middleware' => 'auth.security'], function () {
		Route::get('contact/{section?}', ['as' => 'user.contact_info', 'uses' => 'UserController@contact_info']);

		Route::get('pwd', ['as' => 'user.change_password', 'uses' => 'UserController@change_password']);

		Route::get('notification-settings', ['as' => 'user.notification_settings', 'uses' => 'UserController@notification_settings']);
		
		Route::group(['namespace' => 'Frontend\\User'], function () {
			Route::get('affiliate', ['as' => 'user.affiliate', 'uses' => 'AffiliateController@index']);
			Route::get('payment-methods', ['as' => 'user.payment_method', 'uses' => 'PaymentController@payment_method']);
			Route::get('withdraw', ['as' => 'user.withdraw', 'uses' => 'PaymentController@withdraw']);
		});

		Route::group(['middleware' => 'auth.active'], function () {
			Route::post('contact/{section?}', ['as' => 'user.contact_info', 'uses' => 'UserController@contact_info']);

			Route::post('pwd', ['as' => 'user.change_password', 'uses' => 'UserController@change_password']);

			Route::post('notification-settings', ['as' => 'user.notification_settings', 'uses' => 'UserController@notification_settings']);

			Route::group(['namespace' => 'Frontend\\User'], function () {
				Route::post('affiliate', ['as' => 'user.affiliate', 'uses' => 'AffiliateController@index']);

				Route::post('payment-methods', ['as' => 'user.payment_method', 'uses' => 'PaymentController@payment_method']);
				Route::post('withdraw', ['as' => 'user.withdraw', 'uses' => 'PaymentController@withdraw']);
			});

			Route::match(['get', 'post'], 'close', ['as' => 'user.close_my_account', 'uses' => 'UserController@close_my_account']);
		});
	});

	Route::group(['middleware' => 'auth.active'], function () {
		Route::match(['get', 'post'], 'update-locale/{lang}', ['as' => 'user.update_locale', 'uses' => 'UserController@update_locale']);

		// We will enable this later
		/*
		Route::match(['get', 'post'], 'switch', ['as' => 'user.switch', 'uses' => 'UserController@switch_user']);
		*/
	});

	// Set or change security question
	Route::match(['get', 'post'], 'secure-question', ['as' => 'user.change_security_question', 'uses' => 'UserController@change_security_question']);

	// Security Question
	Route::match(['get', 'post'], 'question', ['as' => 'user.security_question', 'uses' => 'UserController@security_question']);
});

/*
|--------------------------------------------------------------------------
| Notification
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'notifications', 'middleware' => 'auth.customer'], function () {
	Route::match(['get', 'post'], '/', ['as' => 'notification.list', 'uses' => 'NotificationController@all']);
	Route::post('read/{id}', ['as' => 'notification.read', 'uses' => 'NotificationController@read'])->where(['id' => '[0-9]+']);
	Route::post('delete/{id}', ['as' => 'notification.delete', 'uses' => 'NotificationController@delete'])->where(['id' => '[0-9]+']);
});

/*
|--------------------------------------------------------------------------
| Files
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'file'], function () {
	Route::get('{hash}', ['as' => 'file.get', 'uses' => 'FileController@get']);
	Route::get('{hash}/download', ['as' => 'file.download', 'uses' => 'FileController@download']);
	Route::post('upload', ['as' => 'file.upload', 'uses' => 'FileController@upload']);
	Route::match(['delete'], '{hash}/delete', ['as' => 'files.delete', 'uses' => 'FileController@delete']);
});

/*
|--------------------------------------------------------------------------
| Avatar
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'avatar'], function () {
	Route::get('{hash}', ['as' => 'avatar.get', 'uses' => 'FileController@get']);
	Route::get('{hash}/download', ['as' => 'avatar.download', 'uses' => 'FileController@download']);
});

/*
|--------------------------------------------------------------------------
| Portfolio
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'portfolio'], function () {
	Route::get('{hash}', ['as' => 'portfolio.get', 'uses' => 'FileController@get']);
	Route::get('thumb/{hash}', ['as' => 'portfolio.thumb.get', 'uses' => 'FileController@get_thumb']);
	Route::get('{hash}/download', ['as' => 'portfolio.download', 'uses' => 'FileController@download']);
});

/*
|--------------------------------------------------------------------------
| Screenshots
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'screenshot'], function () {
	Route::get('{hash}', ['as' => 'screenshot.get', 'uses' => 'ScreenshotController@get']);
	Route::post('upload', ['as' => 'screenshot.upload', 'uses' => 'ScreenshotController@upload']);
	Route::match(['delete'], '{hash}/delete', ['as' => 'screenshot.delete', 'uses' => 'ScreenshotController@delete']);
});

/*
|--------------------------------------------------------------------------
| FAQ
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'faq', 'middleware' => 'auth.customer'], function () {
	Route::match(['get', 'post'], 'load', ['as' => 'faq.load', 'uses' => 'FaqController@load']);
});

/*
|--------------------------------------------------------------------------
| ticket
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'ticket'], function () {
	Route::match(['get', 'post'], 'list/{tab?}', ['as' => 'ticket.list', 'uses' => 'TicketController@all'])->where(['tab' => '(opening)|(closed)']);
	Route::match(['get', 'post'], 'create', ['as' => 'ticket.create', 'uses' => 'TicketController@create']);
	Route::match(['get', 'post'], '{id}', ['as' => 'ticket.detail', 'uses' => 'TicketController@detail'])->where(['id' => '[0-9a-zA-Z]+']);
});

/*
|--------------------------------------------------------------------------
| Test
|--------------------------------------------------------------------------
*/
// Route::group(['prefix' => 'test'], function () {
// 	Route::get('/', ['as' => 'test.index', 'uses' => 'TestController@index']);
// });

/**
 * Static Page in Frontend
 */
Route::get('{slug}', ['as' => 'frontend.static_page', 'uses' => 'HomeController@static_page'])->where(['slug' => '[0-9A-Za-z\-]+']);