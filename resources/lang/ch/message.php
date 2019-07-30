<?php
/**
* Show messages.
*
* @author Jin
* @since Jan 14, 2016
* @version 1.0 show common data
* @return Response
*/
return [

	/*
	|--------------------------------------------------------------------------
	| Page Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used in common.
	|
	*/

	'buyer' => [
		'job' => [
			'post' => [
				'success_create' => '工作已发布。', 
				'success_update' => '工作已更新。',
				'success_repost' => '工作已重新发布。',
				'success_draft' => '草稿已保存。',
				'success_close' => '工作已关闭。', 
				'success_cancel' => '工作已取消。', 
				'success_delete_draft' => '草稿已删除。', 
				'failed_payment_for_featured' => '处理付款时出错。',
			],
			'offer' => [
				'success_withdraw' => '您已经撤回了一个工作机会。',
			],			
			'contract' => [
				'already_opened' => '您已经和:sb开始了合同。',
				'already_offered' => '您已经向:sb发送了一个工作机会。',
				'already_hired' => '您已经聘用了一个威客。',
			], 
			'invite' => [
				'success' => ':contractor_name被邀请“:job_title”。', 
				'failure' => '您已经向:contractor_name发送了一个工作机会或邀请。',
			],
		],

		'payment' => [
			'failed' => '发送付款请求时出错。',
			'failed_user_suspended' => '您无法执行操作，因为您的威客账户已被暂停。',
			'contract' => [
				"success_paid" => "您已经支付了$:amount。", 
			]
		],

		'contract' => [
			'changed_term' => '您的合同条款已更改。',
			'paused' => '您的合同已暂停。',
			'restarted' => '您的合同已重新开始。',
			'close' => [
				'success_close' => '您已经关闭了工作“:contract_title”。',
			],
			'send_offer' => [
				'success_offer' => '您已经发送了一个工作机会。',
				'failure_validation' => '请检查所有必填的字段。',
				'failure' => '发送工作机会时出错。请稍后再试。',
			],
			'milestones' => [
				'fund_milestone_success' => '您已经出入了里程碑“:milestone”的代管资金。',
				'fund_milestone_error' => '存入里程碑“:milestone”的资金时出错。',
				'release_milestone_success' => '您已经释放了里程碑“:milestone”的代管资金。',
				'release_milestone_error' => '释放里程碑“:milestone”的资金时出错。',
				'success_added_milestone' => "里程碑已创建。",
				'success_updated_milestone' => "里程碑已升级。",
				'success_deleted_milestone' => "里程碑已被删除。",
				'failed_added_milestone' => "里程碑创建过程中出错。",
				'failed_updated_milestone' => "里程碑升级过程中出错。",
				'failed_deleted_milestone' => "里程碑删除过程中出错。",
				'error_milestone_date' => "请输入正确的里程碑日期。",
			]
		],
	],

	'freelancer' => [
		'job' => [
			'apply' => [
				'success_apply' => '您已经向“:job_title”发送了提案。',
			],
			'invite' => [
				'accept' => '您已经接受了“:job_title”的邀请。',
				'decline' => '您已经拒绝了“:job_title”的邀请。',
				'error_max_fixed_price' => '账单金额应少于$:amount。',
				'error_max_hourly_price' => '时薪计费金额应少于$:amount。',
			],
			'offer' => [
				'success_accept' => '您已经接受了工作机会。',
				'success_reject' => '您已经拒绝了工作机会。',
				'error_accept' => '接受工作机会时出错。',
			],
			'proposal' => [
				'success_withdraw' => '您已经撤回了提案。',
				'success_revise_term' => '您已经修改了条款。',
			],
		],

		'contract' => [
			'close' => [
				'success_close' => '您已经关闭了工作“:contract_title”。',
			],
		],

		'payment' => [
			'failed' => '发送付款请求时出错。',	
			'failed_user_suspended' => '您无法执行操作，因为您的客户账户已被暂停。',
			'contract' => [
				'success_refund' => '您已退还$:amount。', 
				'success_refund_fund' => '您已退还代管资金。', 
				'success_request_payment' => '您已经发送了通知，要求客户释放代管资金。', 
				'failed_refund_amount_over_paid_amount' => '您的退款金额不会超过您收到的金额。', 
				'failed_refund_amount_over_balance' => '您的账户资金不足。',
				'failed_refund_fund' => '您无法退还代管资金。',
			],
			'withdraw' => [
				'success_withdraw' => '您已经取款了$:amount。', 
				'failed_request_payment' => '您无法要求付款。', 
			],
		], 
	], 

	'admin' => [
		'user' => [
			'found' => 'user(s) found',
			'notfound' => 'No users found',
		],
		'contract' => [
			'found' => 'contract(s) found',
			'notfound' => 'No contracts found',
		],
	],

	'no_such_job_posting' => '没有这样的工作发布。',
	'not_enough_balance' => '您的账户资金不足。',
	'load_more_messages' => '加载更多消息',
	'no_messages' => '没有消息',
	'error_empty_price' => '请输入有效价格。', 

	'api' => [
		'error' => [
			'1' => '用户名或密码不匹配。',
			'2' => '您的账户未通过审核。请检查您的电子邮件并按照说明操作。',
			'3' => '您的账户已被暂停。请联系客服。',
			'4' => '您的账户已被阻止，因为您输错了10此密码。请联系客服。',
			'5' => '无效的登录令牌。',
			'6' => '没有这样的合同',
			'7' => '您已经到达了本周的每周限制。',
			'8' => '合同已被临时暂停。',
			'9' => '合同已被暂停。',
			'10' => '合同已被关闭。',
			'11' => '您的客户账户已被暂停。',
			'12' => '您的截图无效。',
			'13' => '您的客户账户资金不足。',
			'14' => '无效的日记。',
			'15' => '您的客户账户资金不足。',
			'99' => '检查数据库时出错。',
		]
	]

];