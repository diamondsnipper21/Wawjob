<?php

/*
|--------------------------------------------------------------------------
| Web Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'aqd1454JK', 'namespace' => 'Admin'], function () {
	Route::match(['get', 'post'], 'login', ['as' => 'admin.user.login', 'uses' => 'AuthController@login']);
	Route::match(['get', 'post'], 'logout', ['as' => 'admin.user.logout', 'uses' => 'AuthController@logout']);
	
	Route::get('/', function () {
		return redirect()->route('admin.user.login');
	});
});

Route::group(['prefix' => 'pts1208DL', 'namespace' => 'Admin'], function () {
	// Financial Manager
	Route::group(['middleware' => 'auth.admin.financial', 'namespace' => 'Super'], function () {
		Route::get('/', function () {
			return redirect()->route('admin.financial.dashboard');
		});

		Route::match(['get', 'post'], '/dashboard', ['as' => 'admin.financial.dashboard', 'uses' => 'PaymentController@index']);

		Route::group(['prefix' => 'notifications'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.financial.notifications', 'uses' => 'NotificationController@index']);
			Route::match(['get', 'post'], '/{id}/read', ['as' => 'admin.financial.notification.read', 'uses' => 'NotificationController@read'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/delete', ['as' => 'admin.financial.notification.delete', 'uses' => 'NotificationController@delete'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'messages'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.financial.messages', 'uses' => 'MessageController@index']);
			Route::match(['get', 'post'], '/{id}/read', ['as' => 'admin.financial.message.read', 'uses' => 'MessageController@read'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/delete', ['as' => 'admin.financial.message.delete', 'uses' => 'MessageController@delete'])->where(['id' => '[0-9]+']);
		});

		Route::match(['get', 'post'], '/ajax-search-users/ajax', ['as' => 'admin.super.user.ajax.search_users', 'uses' => 'UserController@ajax_search_users']);

		Route::group(['namespace' => 'Payment'], function () {
			Route::match(['get', 'post'], '/transactions/', ['as' => 'admin.financial.transactions', 'uses' => 'TransactionController@index']);
			Route::match(['get', 'post'], '/transactions/{view}', ['as' => 'admin.super.payment.transactions.view', 'uses' => 'TransactionController@index']);
			Route::match(['get', 'post'], '/escrows', ['as' => 'admin.financial.escrows', 'uses' => 'EscrowController@index']);
			Route::match(['get', 'post'], '/deposit', ['as' => 'admin.financial.deposit', 'uses' => 'DepositController@index']);
			Route::match(['get', 'post'], '/site-withdraws', ['as' => 'admin.financial.site_withdraws', 'uses' => 'SiteWithdrawController@index']);
			Route::match(['get', 'post'], '/withdraw', ['as' => 'admin.financial.withdraw', 'uses' => 'WithdrawController@index']);
		});

		Route::group(['prefix' => 'account'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.financial.account', 'uses' => 'AccountController@index']);
		});
	});
});

Route::group(['prefix' => 'mjr2369LE', 'namespace' => 'Admin'], function () {
	// Ticket Manager
	Route::group(['middleware' => 'auth.admin.ticket', 'namespace' => 'Ticket'], function () {
		Route::get('/', function () {
			return redirect()->route('admin.ticket.dashboard');
		});

		Route::group(['prefix' => 'dashboard'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.ticket.dashboard', 'uses' => 'DashboardController@index']);
			Route::match(['get', 'post'], '/notification/delete/{id}', ['as' => 'admin.ticket.dashboard.delete_notification', 'uses' => 'DashboardController@delete_notification'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'notifications'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.ticket.notifications', 'uses' => 'NotificationController@index']);
			Route::match(['get', 'post'], '/{id}/read', ['as' => 'admin.ticket.notification.read', 'uses' => 'NotificationController@read'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/delete', ['as' => 'admin.ticket.notification.delete', 'uses' => 'NotificationController@delete'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'messages'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.ticket.messages', 'uses' => 'MessageController@index']);
			Route::match(['get', 'post'], '/{id}/read', ['as' => 'admin.ticket.message.read', 'uses' => 'MessageController@read'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/delete', ['as' => 'admin.ticket.message.delete', 'uses' => 'MessageController@delete'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'todos'], function () {
			Route::match(['get', 'post'], '/{tab?}', ['as' => 'admin.ticket.todo.list', 'uses' => 'TodoController@index'])->where(['tab' => '(opening)|(archived)']);
		});

		Route::group(['prefix' => 'todo'], function () {
			Route::match(['post'], '/edit/{id?}', ['as' => 'admin.ticket.todo.edit', 'uses' => 'TodoController@edit'])->where(['id' => '[0-9]+']);
			Route::match(['delete'], '/edit/{id}/delete-file/{file_id}', ['as' => 'admin.ticket.todo.edit.delete_file', 'uses' => 'TodoController@delete_file'])->where(['id' => '[0-9]+'])->where(['file_id' => '[0-9]+']);
			Route::match(['post'], '/tickets', ['as' => 'admin.ticket.todo.tickets', 'uses' => 'TodoController@tickets']);
			Route::match(['get', 'post'], '/{id}', ['as' => 'admin.ticket.todo.detail', 'uses' => 'TodoController@detail'])->where(['id' => '[0-9]+']);

			Route::match(['post'], '/message/send', ['as' => 'admin.ticket.todo-message.send', 'uses' => 'TodoMessageController@send'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'ticket'], function () {
			Route::match(['get', 'post'], '/{tab?}', ['as' => 'admin.ticket.ticket.list', 'uses' => 'TicketController@index'])->where(['tab' => '(opening)|(mine)|(archived)']);
			Route::match(['get', 'post'], '/{id}', ['as' => 'admin.ticket.ticket.detail', 'uses' => 'TicketController@detail'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/send/{id}', ['as' => 'admin.ticket.ticket.send', 'uses' => 'TicketController@send'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/comment/read/{id}', ['as' => 'admin.ticket.ticket.comment.read', 'uses' => 'TicketController@read_comment'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/solve', ['as' => 'admin.ticket.ticket.solve', 'uses' => 'TicketController@solve']);
			Route::match(['get', 'post'], '/send-admin/{id}', ['as' => 'admin.ticket.ticket.msg_admin', 'uses' => 'TicketController@msg_admin'])->where(['id' => '[0-9]+']);
		});

		Route::match(['get', 'post'], '/contract/{id}', ['as' => 'admin.ticket.contract', 'uses' => 'ContractController@detail'])->where(['id' => '[0-9]+']);

		Route::group(['prefix' => 'account'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.ticket.account', 'uses' => 'AccountController@index']);
		});
	});
});

Route::group(['prefix' => 'bpe5982JPG', 'namespace' => 'Admin'], function () {
	// Super Admin
	Route::group(['middleware' => 'auth.admin.super', 'namespace' => 'Super'], function () {
		Route::get('/', function () {
			return redirect()->route('admin.super.dashboard');
		});

		Route::group(['prefix' => 'notifications'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.super.notifications', 'uses' => 'NotificationController@index']);
			Route::match(['get', 'post'], '/{id}/read', ['as' => 'admin.super.notification.read', 'uses' => 'NotificationController@read'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/delete', ['as' => 'admin.super.notification.delete', 'uses' => 'NotificationController@delete'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'messages'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.super.messages', 'uses' => 'MessageController@index']);
			Route::match(['get', 'post'], '/{id}/read', ['as' => 'admin.super.message.read', 'uses' => 'MessageController@read'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/delete', ['as' => 'admin.super.message.delete', 'uses' => 'MessageController@delete'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'dashboard'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.super.dashboard', 'uses' => 'DashboardController@index']);
			Route::match(['get', 'post'], '/notification/delete/{id}', ['as' => 'admin.super.dashboard.delete_notification', 'uses' => 'DashboardController@delete_notification'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'account'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.super.account', 'uses' => 'AccountController@index']);
		});

		// Todo
		Route::group(['prefix' => 'todos'], function () {
			Route::match(['get', 'post'], '/{tab?}', ['as' => 'admin.super.todo.list', 'uses' => 'TodoController@index'])->where(['tab' => '(opening)|(archived)']);
		});

		Route::group(['prefix' => 'todo'], function () {
			Route::match(['post'], '/edit/{id?}', ['as' => 'admin.super.todo.edit', 'uses' => 'TodoController@edit'])->where(['id' => '[0-9]+']);
			Route::match(['delete'], '/edit/{id}/delete-file/{file_id}', ['as' => 'admin.super.todo.edit.delete_file', 'uses' => 'TodoController@delete_file'])->where(['id' => '[0-9]+'])->where(['file_id' => '[0-9]+']);
			Route::match(['post'], '/tickets', ['as' => 'admin.super.todo.tickets', 'uses' => 'TodoController@tickets']);
			Route::match(['get', 'post'], '/{id}', ['as' => 'admin.super.todo.detail', 'uses' => 'TodoController@detail'])->where(['id' => '[0-9]+']);

			Route::match(['post'], '/message/send', ['as' => 'admin.super.todo-message.send', 'uses' => 'TodoMessageController@send'])->where(['id' => '[0-9]+']);
		});
		
		// Tickets
		Route::group(['prefix' => 'tickets'], function () {
			Route::match(['get', 'post'], '/{tab?}', ['as' => 'admin.super.ticket.list', 'uses' => 'TicketController@index'])->where(['tab' => '(opening)|(mine)|(archived)']);
		});
		Route::group(['prefix' => 'ticket'], function () {
			Route::match(['get', 'post'], '/{id}', ['as' => 'admin.super.ticket.detail', 'uses' => 'TicketController@detail'])->where(['id' => '[0-9]+']);
			Route::match(['post'], '/create/{user_id}', ['as' => 'admin.super.ticket.create', 'uses' => 'TicketController@create'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/send/{id}', ['as' => 'admin.super.ticket.send', 'uses' => 'TicketController@send'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/comment/read/{id}', ['as' => 'admin.super.ticket.comment.read', 'uses' => 'TicketController@read_comment'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/solve', ['as' => 'admin.super.ticket.solve', 'uses' => 'TicketController@solve']);
			Route::match(['get', 'post'], '/send-admin/{id}', ['as' => 'admin.super.ticket.msg_admin', 'uses' => 'TicketController@msg_admin'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'users'], function () {
			Route::match(['get', 'post'], '/dashboard', ['as' => 'admin.super.users.dashboard', 'uses' => 'UserController@dashboard']);
			Route::match(['get', 'post'], '/', ['as' => 'admin.super.users.list', 'uses' => 'UserController@listing']);
			Route::match(['get', 'post'], '/freelancers', ['as' => 'admin.super.users.list.freelancers', 'uses' => 'UserController@listing_freelancers']);
			Route::match(['get', 'post'], '/buyers', ['as' => 'admin.super.users.list.buyers', 'uses' => 'UserController@listing_buyers']);
		});

		// User
		Route::group(['prefix' => 'user', 'namespace' => 'User'], function () {
			
			Route::match(['get', 'post'], '/{user_id}/overview', ['as' => 'admin.super.user.overview', 'uses' => 'OverviewController@index'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/messages', ['as' => 'admin.super.user.messages', 'uses' => 'MessageController@index'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/messages/{thread_id}', ['as' => 'admin.super.user.messages.thread', 'uses' => 'MessageController@thread'])->where(['user_id' => '[0-9]+'])->where(['thread_id' => '[0-9]+']);

			// Action History
			Route::match(['get', 'post'], '/{user_id}/action-history', ['as' => 'admin.super.user.action_history', 'uses' => 'ActionHistoryController@index'])->where(['user_id' => '[0-9]+']);

			// Access History
			Route::match(['get', 'post'], '/{user_id}/access-history', ['as' => 'admin.super.user.access_history', 'uses' => 'AccessHistoryController@index'])->where(['user_id' => '[0-9]+']);

			Route::match(['get', 'post'], '/{user_id}/change-status', ['as' => 'admin.super.user.change_status', 'uses' => 'OverviewController@change_status'])->where(['user_id' => '[0-9]+']);
			
			// Contracts
			Route::match(['get', 'post'], '/{user_id}/contracts', ['as' => 'admin.super.user.contracts', 'uses' => 'ContractController@index'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/contract/{contract_id}', ['as' => 'admin.super.user.contract', 'uses' => 'ContractController@detail'])->where(['user_id' => '[0-9]+', 'contract_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/contract/{contract_id}/action-history', ['as' => 'admin.super.user.contract.action_history', 'uses' => 'ContractController@action_history'])->where(['user_id' => '[0-9]+', 'contract_id' => '[0-9]+']);

			// Transactions
			Route::match(['get', 'post'], '/{user_id}/transactions', ['as' => 'admin.super.user.transactions', 'uses' => 'ReportController@transactions'])->where(['user_id' => '[0-9]+']);

			// Timesheet
			Route::match(['get', 'post'], '/{user_id}/timesheet', ['as' => 'admin.super.user.timesheet', 'uses' => 'ReportController@timesheet'])->where(['user_id' => '[0-9]+']);

		  	// Work Diary
			Route::match(['get', 'post'], '/{user_id}/workdiary', ['as' => 'admin.super.user.workdiary', 'uses' => 'WorkDiaryController@view_first'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/workdiary/view', ['as' => 'admin.super.user.workdiary.view_first', 'uses' => 'WorkDiaryController@view_first'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/workdiary/view/{cid}', ['as' => 'admin.super.user.workdiary.view', 'uses' => 'WorkDiaryController@view'])->where(['user_id' => '[0-9]+'])->where(['cid' => '[0-9]+']);
			Route::post('/{user_id}/workdiary/ajax', ['as' => 'workdiary.ajax', 'uses' => 'WorkdiaryController@ajaxAction']);

			// Ticket
			Route::match(['get', 'post'], '/{user_id}/tickets/{tab?}', ['as' => 'admin.super.user.ticket.list', 'uses' => 'TicketController@index_view'])->where(['user_id' => '[0-9]+'])->where(['tab' => '(opening)|(archived)']);

			Route::match(['get', 'post'], '/{user_id}/ticket/{id}', ['as' => 'admin.super.user.ticket.detail', 'uses' => 'TicketController@detail'])->where(['user_id' => '[0-9]+'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/ticket/send/{id}', ['as' => 'admin.super.user.ticket.send', 'uses' => 'TicketController@send'])->where(['user_id' => '[0-9]+'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/ticket/solve/{id?}', ['as' => 'admin.super.user.ticket.solve', 'uses' => 'TicketController@solve'])->where(['user_id' => '[0-9]+'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/ticket/send-admin/{id}', ['as' => 'admin.super.user.ticket.msg_admin', 'uses' => 'TicketController@msg_admin'])->where(['user_id' => '[0-9]+'])->where(['id' => '[0-9]+']);
			
			// Notification Settings
			Route::match(['get', 'post'], '/{user_id}/notification-settings', ['as' => 'admin.super.user.notification_settings', 'uses' => 'NotificationSettingController@index'])->where(['user_id' => '[0-9]+']);

			// Freelancer
			Route::group(['namespace' => 'Freelancer'], function () {
				// Proposals
				Route::match(['get', 'post'], '/{user_id}/proposals', ['as' => 'admin.super.user.freelancer.proposals', 'uses' => 'ProposalController@index'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/proposal/{proposal_id}', ['as' => 'admin.super.user.freelancer.proposal', 'uses' => 'ProposalController@detail'])->where(['user_id' => '[0-9]+'])->where(['proposal_id' => '[0-9]+']);
				
				// Profile
				Route::match(['get', 'post'], '/{user_id}/profile', ['as' => 'admin.super.user.freelancer.profile', 'uses' => 'ProfileController@index'])->where(['user_id' => '[0-9]+']);
			});	

			//Buyer
			Route::group(['namespace' => 'Buyer'], function () {
				Route::match(['get', 'post'], '/{user_id}/jobs', ['as' => 'admin.super.user.buyer.jobs', 'uses' => 'JobController@index'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/overview', ['as' => 'admin.super.user.buyer.job.overview', 'uses' => 'JobController@overview'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/invitation', ['as' => 'admin.super.user.buyer.job.invitation', 'uses' => 'JobController@invitation'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/proposal', ['as' => 'admin.super.user.buyer.job.proposal', 'uses' => 'JobController@proposal'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/proposal/{page}', ['as' => 'admin.super.user.buyer.job.proposal_page', 'uses' => 'JobController@proposal'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/interview', ['as' => 'admin.super.user.buyer.job.interview', 'uses' => 'JobController@interview'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/interview/{page}', ['as' => 'admin.super.user.buyer.job.interview_page', 'uses' => 'JobController@interview'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/hire-offers', ['as' => 'admin.super.user.buyer.job.hire_offers', 'uses' => 'JobController@hire_offers'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
				Route::match(['get', 'post'], '/{user_id}/job/{id}/action-history', ['as' => 'admin.super.user.buyer.job.action_history', 'uses' => 'JobController@action_history'])->where(['id' => '[0-9]+'])->where(['user_id' => '[0-9]+']);
			});

			// Affiliates
			Route::match(['get', 'post'], '/{user_id}/affiliate/{tab?}', ['as' => 'admin.super.user.affiliate', 'uses' => 'AffiliateController@index'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/affiliate-detail/{paid_date}', ['as' => 'admin.super.user.affiliate_detail', 'uses' => 'AffiliateController@detail'])->where(['user_id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{user_id}/affiliate-pay', ['as' => 'admin.super.user.affiliate_pay', 'uses' => 'AffiliateController@pay'])->where(['user_id' => '[0-9]+']);
		});

		// Proposals
		Route::match(['get', 'post'], '/proposals', ['as' => 'admin.super.proposals', 'uses' => 'ProposalController@index']);
		Route::match(['get', 'post'], '/proposal/{proposal_id}', ['as' => 'admin.super.proposal', 'uses' => 'ProposalController@detail'])->where(['proposal_id' => '[0-9]+']);

		// Contracts
		Route::match(['get', 'post'], '/contracts', ['as' => 'admin.super.contracts', 'uses' => 'ContractController@index']);
		Route::match(['get', 'post'], '/contract/{id}', ['as' => 'admin.super.contract', 'uses' => 'ContractController@detail'])->where(['id' => '[0-9]+']);
		Route::match(['get', 'post'], '/contract/{id}/action-history', ['as' => 'admin.super.contract.action_history', 'uses' => 'ContractController@action_history'])->where(['id' => '[0-9]+']);

		Route::group(['prefix' => 'affiliates'], function () {	
			Route::match(['get', 'post'], '/overview', ['as' => 'admin.super.affiliates.overview', 'uses' => 'AffiliateController@overview']);
			Route::match(['get', 'post'], '/users', ['as' => 'admin.super.affiliates.users', 'uses' => 'AffiliateController@index']);
		});

		// Jobs
		Route::match(['get', 'post'], '/jobs', ['as' => 'admin.super.job.jobs', 'uses' => 'JobController@index']);
		Route::group(['prefix' => 'job'], function () {
			Route::match(['get', 'post'], '/{id}/overview', ['as' => 'admin.super.job.overview', 'uses' => 'JobController@overview'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/invitation', ['as' => 'admin.super.job.invitation', 'uses' => 'JobController@invitation'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/proposal', ['as' => 'admin.super.job.proposal', 'uses' => 'JobController@proposal'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/proposal/{page}', ['as' => 'admin.super.job.proposal_page', 'uses' => 'JobController@proposal'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/interview', ['as' => 'admin.super.job.interview', 'uses' => 'JobController@interview'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/interview/{page}', ['as' => 'admin.super.job.interview_page', 'uses' => 'JobController@interview'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/hire-offers', ['as' => 'admin.super.job.hire_offers', 'uses' => 'JobController@hire_offers'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/{id}/action-history', ['as' => 'admin.super.job.action_history', 'uses' => 'JobController@action_history'])->where(['id' => '[0-9]+']);
		});

		// Payments
		Route::group(['prefix' => 'payment'], function () {
			Route::match(['get', 'post'], 'overview', ['as' => 'admin.super.payment.overview', 'uses' => 'PaymentController@index']);

			Route::group(['namespace' => 'Payment'], function () {

				Route::match(['get', 'post'], '/transactions/', ['as' => 'admin.super.payment.transactions', 'uses' => 'TransactionController@index']);

				Route::match(['get', 'post'], '/transactions/{view}', ['as' => 'admin.super.payment.transactions.view', 'uses' => 'TransactionController@index']);

				Route::match(['get', 'post'], '/escrows', ['as' => 'admin.super.payment.escrows', 'uses' => 'EscrowController@index']);
				Route::match(['get', 'post'], '/deposit', ['as' => 'admin.super.payment.deposit', 'uses' => 'DepositController@index']);
				Route::match(['get', 'post'], '/site-withdraws', ['as' => 'admin.super.payment.site_withdraws', 'uses' => 'SiteWithdrawController@index']);
				Route::match(['get', 'post'], '/withdraw', ['as' => 'admin.super.payment.withdraw', 'uses' => 'WithdrawController@index']);
			});
		});

		// Disputes
		Route::match(['get', 'post'], '/disputes', ['as' => 'admin.super.disputes', 'uses' => 'DisputeController@index']);
		Route::match(['get', 'post'], '/dispute/determine/{id}', ['as' => 'admin.super.dispute.determine', 'uses' => 'DisputeController@determine'])->where(['id' => '[0-9]+']);
		
		// Stats
		Route::match(['get', 'post'], '/stats', ['as' => 'admin.super.stats', 'uses' => 'StatsController@index']);

		// Settings
		Route::group(['prefix' => 'settings', 'namespace' => 'Setting'], function () {
			// Payment Method
			Route::match(['get', 'post'], '/payment-method', ['as' => 'admin.super.settings.payment_method', 'uses' => 'PaymentMethodController@index']);
			
			// Email Template
			Route::match(['get', 'post'], '/email-templates', ['as' => 'admin.super.settings.email_templates', 'uses' => 'EmailTemplateController@index']);
			Route::match(['post'], '/email-template/edit/{id?}', ['as' => 'admin.super.settings.email_template.edit', 'uses' => 'EmailTemplateController@edit'])->where(['id' => '[0-9]+']);

			// Notifications
			Route::match(['get', 'post'], '/notifications', ['as' => 'admin.super.settings.notifications', 'uses' => 'NotificationController@index']);
			Route::match(['post'], '/notifications/edit/{id?}', ['as' => 'admin.super.settings.notifications.edit', 'uses' => 'NotificationController@edit'])->where(['id' => '[0-9]+']);

			// Job Categories
			Route::match(['get', 'post'], '/job-categories', ['as' => 'admin.super.settings.job_categories', 'uses' => 'JobCategoryController@index']);
			Route::match(['get', 'post'], '/job-category/edit/{id?}', ['as' => 'admin.super.settings.job_category.edit', 'uses' => 'JobCategoryController@edit'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/job-categories/re-order', ['as' => 'admin.super.settings.job_category.re_order', 'uses' => 'JobCategoryController@re_order']);

			// Skills
			Route::match(['get', 'post'], '/skills', ['as' => 'admin.super.settings.skills', 'uses' => 'SkillController@index']);
			Route::match(['get', 'post'], '/skill/validate-name/{id?}', ['as' => 'admin.super.settings.skill.validate.name', 'uses' => 'SkillController@validate_name'])->where(['id' => '[0-9]+']);
			Route::match(['get', 'post'], '/skill/edit/{id?}', ['as' => 'admin.super.settings.skill.edit', 'uses' => 'SkillController@edit'])->where(['id' => '[0-9]+']);

			// Countries
			Route::match(['get', 'post'], '/countries', ['as' => 'admin.super.settings.countries', 'uses' => 'CountryController@index']);

			// Fees
			Route::match(['get', 'post'], '/fees', ['as' => 'admin.super.settings.fees', 'uses' => 'FeeController@index']);
			Route::match(['post'], '/fees/refresh-chinese-money-rate', ['as' => 'admin.super.settings.fees.refresh_chinese_money_rate', 'uses' => 'FeeController@refresh_chinese_money_rate']);

			// User Points
			Route::match(['get', 'post'], '/user-points', ['as' => 'admin.super.settings.user_points', 'uses' => 'UserPointController@index']);

			//FAQs
			Route::match(['get', 'post'], '/faqs', ['as' => 'admin.super.settings.faqs', 'uses' => 'FaqController@listing']);
			Route::match(['get', 'post'], '/faqs/edit/{id?}', ['as' => 'admin.super.settings.faq.edit', 'uses' => 'FaqController@edit'])->where(['id' => '[0-9]+']);

			// static pages
			Route::match(['get', 'post'], '/static-pages', ['as' => 'admin.super.settings.static_pages', 'uses' => 'StaticPageController@index']);
			Route::match(['get', 'post'], '/static-page/edit/{id?}', ['as' => 'admin.super.settings.static_page.edit', 'uses' => 'StaticPageController@edit'])->where(['id' => '[0-9]+']);

			// help pages
			Route::match(['get', 'post'], '/help-pages', ['as' => 'admin.super.settings.help_pages', 'uses' => 'HelpPageController@index']);
			Route::match(['get', 'post'], '/help-page/edit/{id?}', ['as' => 'admin.super.settings.help_page.edit', 'uses' => 'HelpPageController@edit'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'admin-users'], function () {
			Route::match(['get', 'post'], '/', ['as' => 'admin.super.admin_users.list', 'uses' => 'AdminUsersController@index']);
			Route::match(['get', 'post'], '/edit/{id?}', ['as' => 'admin.super.admin_users.edit', 'uses' => 'AdminUsersController@edit'])->where(['id' => '[0-9]+']);
			Route::match(['get'], 'check-duplicated/{id?}', ['as' => 'admin.super.admin_users.check_duplicated', 'uses' => 'AdminUsersController@check_duplicated'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'message-room'], function () {
			Route::match(['post'], '/create', ['as' => 'admin.super.thread.create', 'uses' => 'ThreadController@create']);
			Route::match(['post', 'get'], '/{id}', ['as' => 'admin.super.thread.detail', 'uses' => 'ThreadController@detail'])->where(['id' => '[0-9]+']);
			Route::match(['post'], '/{id}/send', ['as' => 'admin.super.thread.send', 'uses' => 'ThreadController@send'])->where(['id' => '[0-9]+']);
		});

		Route::group(['prefix' => 'contact-us'], function () {
			Route::match(['get', 'post'], '/contacts', ['as' => 'admin.super.contact_us.list', 'uses' => 'ContactUsController@index']);
			Route::match(['get', 'post'], '/{id}', ['as' => 'admin.super.contact_us.detail', 'uses' => 'ContactUsController@detail'])->where(['id' => '[0-9]+']);
			Route::match(['post'], '/{id}/send', ['as' => 'admin.super.contact_us.send', 'uses' => 'ContactUsController@send'])->where(['id' => '[0-9]+']);
		});

		Route::match(['get', 'post'], '/ajax-search-users/ajax', ['as' => 'admin.super.user.ajax.search_users', 'uses' => 'UserController@ajax_search_users']);

		// Cronjobs
		Route::match(['get', 'post'], '/cronjobs', ['as' => 'admin.super.cronjobs', 'uses' => 'CronjobsController@index']);

		// Send Email
		Route::match(['get', 'post'], '/send-email', ['as' => 'admin.super.send_email', 'uses' => 'EmailController@index']);

		// Error Log Viewer
		Route::match(['get', 'post'], 'logs-viewer', ['as' => 'admin.super.tools.log_viewer', 'uses' => 'ToolsController@log_viewer']);

		// Backup
		Route::match(['get', 'post'], 'backup', ['as' => 'admin.super.tools.backup', 'uses' => 'ToolsController@backup']);
	});
});