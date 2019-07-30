<?php

/*
|--------------------------------------------------------------------------
| Web Freelancer Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Freelancer
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth.freelancer'], function () {
	
	Route::match(['get', 'post'], 'profile-start', ['as' => 'profile.start', 'uses' => 'Frontend\\User\\ProfileController@start']);

	Route::match(['get', 'post'], 'profile-step-{step}', ['as' => 'profile.step', 'uses' => 'Frontend\\User\\ProfileController@add_item'])->where(['step' => '[0-7]+']);

	Route::match(['get', 'post'], 'profile/add', ['as' => 'profile.add', 'uses' => 'Frontend\\User\\ProfileController@add']);
	Route::match(['get', 'post'], 'profile/remove', ['as' => 'profile.remove', 'uses' => 'Frontend\\User\\ProfileController@add']);
	Route::match(['delete'], 'profile/delete', ['as' => 'profile.delete', 'uses' => 'Frontend\\User\\ProfileController@delete']);

	Route::match(['get', 'post'], 'my-profile', ['as' => 'user.my_profile', 'uses' => 'MyProfileController@index']);
	Route::post('my-profile/add', ['as' => 'user.my_profile.add', 'uses' => 'MyProfileController@add']);
	Route::match(['delete'], 'my-profile/delete', ['as' => 'user.my_profile.delete', 'uses' => 'MyProfileController@delete']);

	Route::match(['get', 'post'], 'profile-settings', ['as' => 'user.profile_settings', 'uses' => 'MyProfileController@profile_settings']);

	// Job
	Route::group(['prefix' => 'job'], function () {
		Route::match(['get', 'post'], 'application/{id}', ['as' => 'job.application_detail', 'uses' => 'JobController@application_detail'])->where(['id' => '[0-9a-zA-Z]+']);

		Route::match(['get', 'post'], 'my-proposals/{tab?}', ['as' => 'job.my_proposals', 'uses' => 'JobController@my_proposals'])->where(['tab' => '(active)|(archived)']);

		Route::match(['get', 'post'], 'my-contracts', ['as' => 'job.my_contracts', 'uses' => 'JobController@my_contracts']);
		
		Route::get('apply-offer/{id}', ['as' => 'job.apply_offer', 'uses' => 'JobController@apply_offer'])->where(['id' => '[0-9]+']);

		Route::get('accept-invite/{id}', ['as' => 'job.accept_invite', 'uses' => 'JobController@accept_invite'])->where(['id' => '[0-9]+']);

		Route::get('{id}/apply', ['as' => 'job.apply', 'uses' => 'JobController@job_apply'])->where(['id' => '[0-9a-zA-Z]+']);

		Route::group(['middleware' => 'auth.active'], function () {
			Route::match(['get', 'post'], 'application/{id}', ['as' => 'job.application_detail', 'uses' => 'JobController@application_detail'])->where(['id' => '[0-9a-zA-Z]+']);

			Route::post('apply-offer/{id}', ['as' => 'job.apply_offer', 'uses' => 'JobController@apply_offer'])->where(['id' => '[0-9]+']);

			Route::post('accept-invite/{id}', ['as' => 'job.accept_invite', 'uses' => 'JobController@accept_invite'])->where(['id' => '[0-9]+']);

			Route::post('{id}/apply', ['as' => 'job.apply', 'uses' => 'JobController@job_apply'])->where(['id' => '[0-9a-zA-Z]+']);
		});
	});
  	
	Route::group(['prefix' => 'saved-jobs'], function () {
		Route::match(['get', 'post'], '/', ['as' => 'saved_jobs.index', 'uses' => 'UserSavedProjectController@index']);
		Route::match(['get', 'post'], 'create/{id?}', ['as' => 'saved_jobs.create', 'uses' => 'UserSavedProjectController@create'])->where(['id' => '[0-9]+']);
		Route::match(['get', 'post'], 'destroy/{id?}', ['as' => 'saved_jobs.destroy', 'uses' => 'UserSavedProjectController@destroy'])->where(['id' => '[0-9]+']);
	});

  	// Work Diary
	Route::group(['prefix' => 'workdiary'], function () {
		Route::group(['middleware' => 'auth.active'], function () {
			Route::post('ajaxjob', ['as' => 'workdiary.ajaxjob', 'uses' => 'WorkdiaryController@ajaxjobAction']);
		});
	});

  	// Report
	Route::group(['prefix' => 'reports'], function () {
		Route::match(['get', 'post'], 'overview', ['as' => 'report.overview', 'uses' => 'ReportController@overview']);
		Route::match(['get', 'post'], 'timelogs', ['as' => 'report.timelogs', 'uses' => 'ReportController@timelogs']);
		// Route::match(['get', 'post'], 'connections', ['as' => 'report.connections', 'uses' => 'ReportController@connections']);
	});
});