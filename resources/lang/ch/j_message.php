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
	'btn_cancel' => '取消', 
	'btn_yes' => '是', 
	'btn_no' => '否', 
	'btn_delete' => '删除', 

	'buyer' => [  
		'job' => [
			'status' => [
				'close_job' => '您确定要关闭此工作吗？',
				'cancel_job' => '您确定要取消此工作吗？',
				'delete_job' => '您确定要删除此工作吗？',
				'delete_draft' => '您确定要删除此草稿吗？',
				'change_public' => '您确定要把此工作发布:status吗？', 
				'app_declined' => '您确定要拒绝此申请吗？', 
			]
		]
	], 

	'freelancer' => [
		'job' => [
			'reject_offer' => '您确定要拒绝此工作机会吗？', 
			'accept_offer' => '您确定要接受此工作机会吗？', 
		], 
		'workdiary' => [
			'delete_screenshot' => '您确定要删除所有选定的截图吗？', 
			'select_screenshot' => '请选择截图。', 
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