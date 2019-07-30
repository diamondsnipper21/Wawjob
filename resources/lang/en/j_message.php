<?php
/**
   * Show messages.
   *
   * @author nada
   * @since June 14, 2016
   * @version 1.0 show common data
   * @return Response
   */
return [

	/*
	|--------------------------------------------------------------------------
	| Javascript - Message
	|--------------------------------------------------------------------------
	|
	| The following language lines are used in common.  
	|
	*/
	'btn_ok' => 'OK', 
	'btn_cancel' => 'Cancel', 
	'btn_yes' => 'Yes', 
	'btn_no' => 'No', 
	'btn_delete' => 'Delete', 

	'buyer' => [  
		'job' => [
			'status' => [
				'close_job' => 'Are you sure to close this job?',
				'cancel_job' => 'Are you sure to cancel this job?',
				'delete_job' => 'Are you sure to delete this job?',
				'delete_draft' => 'Are you sure to delete this draft?',
				'change_public' => 'Are you sure to make this job posting :status?', 
				'app_declined' => 'Are you sure to decline this application?', 
			]
		]
	], 

	'freelancer' => [
		'job' => [
			'reject_offer' => 'Are you sure to reject this offer?', 
			'accept_offer' => 'Are you sure to accept this offer?', 
		], 
		'workdiary' => [
			'delete_screenshot' => 'Are you sure to delete all selected screenshots?', 
			'select_screenshot' => 'Please select screenshots.', 
		], 
	],

	'admin' => [
		'category' => [
			'remove_category' => 'Are you sure to remove this category?', 
		],
		'affiliate' => [
			'update' => 'Are you sure to update the Affiliate settings?',
			'saved'  => 'The affiliate settings have been updated.',
		],
		'notification' => [
			'remove_notification' => 'Are you sure to remove this notification?', 
			'title_send_notification' => 'Send Notification', 
			'title_add_cronjob' => 'Add CronJob',
			'send_notification' => 'Are you sure to send the notification', 
			'add_cronjob' => 'Are you sure to add the notification in CronJob?', 
		], 
		'skill' => [
			'remove_skill' => 'Are you sure to remove this skill?', 
			'deactivate_skill' => 'Are you sure to disable this skill?', 
		], 
		'ticket' => [
			'remove_comment' => 'Are you sure to remove this comment?', 
		], 
	]
];