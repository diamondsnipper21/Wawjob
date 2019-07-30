<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Page Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used by the pages.
	|
	*/

	// Logo
	'title' => 'iJobDesk',
	'logo' => 'iJobDesk',

	// Footer
	'footer' => [
		'copyright' => 'Copyright &copy; 2018 - :year iJobDesk Technology',
	],

	// Home Page
	'home' => [
		'title' => '首页',
		'title1' => '首页',
		'logo'  => 'iJobDesk',
	],

	// About Page
	'frontend' => [
		'contact_us' => [
			'title' 	=> '联系我们',
			'success' 	=> '谢谢您联络我们。我们很快就会回复您。',
			'error_captcha' => '验证码不正确，请重新填写。',
			'error_user' 	=> '电子邮件地址无效。',
			'error' 	=> '出了些问题。很抱歉给您带来不便。',
			'desc' 	=> '需要我们为您提供帮助吗？<br />联系我们，我们的客服团队将会尽快回复您。',
			'got_a_question' => '有问题？ 我们洗耳恭听您的意见!',
		],
		'how_it_works' => [
			'title' => '如何运作',
		],
		'help' => [
			'title' => '我们该怎么帮助您？',
		],
		'download_tools' => [
			'title' => '下载工具',
		],
		'dashboard' => [
			'title' => '仪表板',
		],
		'coming_soon' => [
			'title' => '敬请期待',
		],

		// Unsubscribe Page
		'unsubscribe' => [
			'title' => '退订',
			'guest' => [
				'title' => '您已经退订了',
				'desc'  => '根据您的要求，您已取消订阅iJobDesk市场。'
			],
			'user' => [
				'title' => '通知设置已更新',
				'desc'  => '根据您的要求，您的账户已更新电子邮件通知设置。'
			],
			'ignore' => [
				'title' => '通知设置',
				'desc'  => '您无法选择退出iJobDesk上的重要电子邮件通知。'
			],
		]
	],

	// About Page
	'about' => [
	],

	// Error Pages
	'errors' => [
		'404' => [
			'title' => '找不到网页',
			'desc'  	=> '哎呀！对不起，我们找不到该页面。',
			'sub_desc'  => '出现问题或该页面不再存在。',
		],
	],

	// Authenticate Pages
	'auth' => [
		// Login page
		'login' => [
			'title'             => '登陆',
			'title_with_space'  => '登陆',
			'learn_more'        => '学到更多',
			'login_and_work'    => '登陆并开始工作',
			'username_or_email' => '用户名或电子邮件地址',
			'password'          => '密码',
			'remember'          => '记住我',
			'forgot'            => '忘记密码',
			'signup'            => '没有账户？＆nbsp;＆nbsp;<a href=":link">注册</a>',
		],

		'logout' => [
			'title' => '登出',
		],

		'forgot' => [
			'title' => '忘记密码',
			'description' => '请输入您的用户名或电子邮件地址。您将收到一封电子邮件，其中包含设置新密码的链接。',
			'back_to_login' => '回到登入',
			'new_password' => '新密码',
			'confirm_password' => '确认密码',
			'get_new_password' => '获取新密码',
			'reset_password' => '重置密码',
			'username_or_email' => '用户名或电子邮件地址',
			'invalid_email' => '您的电子邮件地址无效。',
			'suspended_account' => '您的账户已被暂停。',
			'failed_get_new_password' => '出了些问题。请稍后重试。',
			'success_reset_password' => '您的密码已重置。',
			'submit_get_new_password' => '已发送带有链接的电子邮件。请查看您的邮箱。',
			'failed_get_new_password' => '出了些问题。请稍后重试。',
			'invalid_token' => '令牌无效或已过期。',
		],
	
		// Singup pages
		'signup_user' => [
			'title'                     => '注册',
		],

		'reset' => [
			'title' => '重置您的密码',
			'reset_password' => '重置密码',
		],
	
		// Singup pages
		'signup' => [
			'title'                     => '注册',
			'title_with_space'          => '注册',
			'have_an_account'           => '已经拥有iJobDesk账户？',
			'get_started'               => '让我们开始吧！',
			'what_you_are_looking_for'  => '首先，告诉我们您在寻找什么。',
			'hire_a_freelancer'         => '我想聘请一名威客。',
			'find_collaborate'          => '寻找专家，合作<br>并支付费用。',
			'hire'                      => '聘请',
			'looking_for_online_work'   => '我正在寻找在线工作。',
			'find_freelance_projects'   => '寻找外包项目<br>并拓展业务。',
			'work'                      => '工作',
			  
			 // Buyer page
			'buyer' => [
				'title'   => '注册成为买方',
				'create'  => '创建买方账户',
				'looking_work'  => '期待工作',
				'signup_as_freelancer' => '注册成为威客'
			],

		  	// Freelancer page
			'freelancer' => [
				'title'         => '注册成为威客',
				'create'        => '创建一个威客账户',
				'signup_as_buyer' => '注册成为买方',
				'looking_hire'  => '想聘用',
			],

			'success' => [
				'title' => '成功注册账户',
				'verify_your_email_address' => '确认您的电子邮件地址',
				'sent_email_description' => '我们刚发送了一封电子邮件到您的电子邮件地址:email',
				'check_email_description' => '请查看您的电子邮件，然后单击提供的链接以验证您的电子邮件地址。',
				'change_email' => '更改电子邮件地址',
				'new_email' => '新电子邮件地址',
				'resend_verification_email' => '请重新发送验证电子邮件',
				'message_success_changed_email' => '您的电子邮件地址已更改。',
				'message_failed_changed_email' => '更改电子邮件地址时出错了。',
				'message_failed_changed_duplicated_email' => '该电子邮件地址已被使用。',
				'success_sent_email' => '我们刚发送了一封电子邮件到您的电子邮件地址:email',
				'success_resent_email' => '我们重新发送了一封电子邮件到您的电子邮件地址:email',
				'failed_sent_email' => '无法发送电子邮件到您的电子邮件地址:email',
			],

			'verify' => [
				'verify_your_email_address' => '确认您的电子邮件地址',
				'invalid_token' => '令牌无效或已过期。',
				'success_verified' => '您的电子邮件地址已过验证。',
			],
		],

		'signup_success' => [
			'title' => '成功'
		],
	],

	'contract' => [
		'my_contracts' => [
			'title' => '合同'
		],

		'contract_detail' => [
			'title' => '合同细节'
		],

		'feedback' => [
			'title' => '留下反馈'
		],

		'end' => [
			'title' => '结束合同'
		],
	],

	'report' => [
		'timesheet' => [
			'title' => '时间表'
		]
	],

	'job' => [
		'job_detail' => [
			'title' => ':job'
		],
	],

	'user' => [
		'contact_info' => [
			'title' => '我的账户'
		],
		'account' => [
			'title' => '账户',
		],
		'location' => [
			'title' => '位置',        
		],
		'invoice_address' => [
			'title' => '发票地址（可选）',
		],
		'detail' => [
			'title' => '公司详情'
		],
		'contact' => [
			'title' => '公司联系方式'
		],
		'change_password' => [
			'title' => '更改密码'
		],
		'close_my_account' => [
			'title' => '关闭我的账户',
		],
		'change_security_question' => [
			'title' => '更改安全提问'
		],
		'security_question' => [
			'title' => '安全提问'
		],		
		'payment_method' => [
			'title' => '付款方式'
		],
		'notification_settings' => [
			'title' => '通知设置'
		],
		'affiliate' => [
			'title' => '联盟计划'
		],
		'withdraw' => [
			'title' => '提取资金'
		],
		'withdraw_preview' => [
			'title' => '取款预览'
		],
	],

	/* Freelancer Pages */
	'freelancer' => [
		'step' => [
			'start' => [
				'title' => '开始您的个人资料'
			],
			'security_question' => [
				'title' => '安全提问'
			],
			'about_me' => [
				'title' => '关于我'
			],
			'add_portfolio' => [
				'title' => '添加案例'
			],
			'add_certification' => [
				'title' => '添加认证'
			],
			'add_employment' => [
				'title' => '添加就业历史'
			],
			'add_education' => [
				'title' => '添加教育'
			],
			'add_experience' => [
				'title' => '添加其他经验'
			]
		],

		'user' => [
			'my_profile' => [
				'title' => '我的资料'
			],

			'profile' => [
				'title' => ':user'
			],

			'profile_settings' => [
				'title' => '资料设置',
			],
		],

		'job' => [
			'job_detail' => [
				'title' => ':job'
			],

			'job_apply' => [
				'title' => '申请工作'
			],

			'my_applicant' => [
				'title' => '我的申请'
			],

			'my_proposals' => [
				'title' => '我的申请'
			],

			'my_archived' => [
				'title' => '存档的提案'
			], 

			'search' => [
				'title' => '工作搜索'
			],

			'apply_offer' => [
				'title' => '工作机会'
			],

			'accept_invite' => [
				'title' => '工作邀请'
			],

			'saved_jobs' => [
				'title' => '保存的工作'
			],
		],

		'contract' => [
			'my_contracts' => [
				'title' => '我的合同'
			],

			'my_all_jobs' => [
				'title' => '我的工作'
			],

			'contract_detail' => [
				'title' => '合同细节'
			]
		],

		'workdiary' => [
			'viewjob' => [
				'title' => "工作日记"
			]
		],

		'report' => [
			'overview' => [
				'title' => "概观"
			],

			'timelogs' => [
				'title' => "时间日志"
			],

			'transactions' => [
				'title' => "交易"
			],

			'timesheet' => [
				'title' => "时间表"
			],
		],

	],

	/* Buyer Pages */
	'buyer' => [
		'job' => [
			'create' => [
				'title' => '发一份工作',
				'sub_title' => '发一份工作'
			],
			'all_jobs' => [
				'title' => '招聘消息'
			],
			'my_jobs' => [
				'title' => '我的工作'
			],
			'view' => [
				'title' => ':job'
			],
			'edit' => [
				'title' => '编辑发布',
				'sub_title' => '编辑发布 - :job'
			],
			'invite_freelancers' => [
				'title' => '邀请威客 - :job',      	
			],
			'interviews' => [
				'title' => '审核投标 - :job',
			],
			'hire_offers' => [
				'title' => '聘用/提供 - :job',
			],
			'overview' => [
				'title' => '概观 - :job',
			],
			'hire_user' => [
				'title' => '聘用'
			],
			'hire' => [
				'title' => '聘用'
			],
		],

		'contract' => [
			'all_contracts' => [
				'title' => "所有合同"
			],

			'contract_view' => [
				'title' => "合同细节"
			],

			'my_freelancers' => [
				'title' => '我的威客'
			],

			'my_saved_freelancers' => [
				'title' => '已保存的威客'
			],
		],
		'workdiary' => [
			'view' => [
				'title' => "工作日记"
			]
		],
		'report' => [
			'weekly_summary' => [
				'title' => "每周摘要"
			],
			'transactions' => [
				'title' => "交易"
			],
			'timesheet' => [
				'title' => "时间表"
			], 
		],

		'user' => [
			'deposit' => [
				'title' => '存款'
			],
			'deposit_preview' => [
				'title' => '存款预览'
			],
		],
	],

	/* Admin Pages */
	'admin' => [
		// Dashboard Page
		'dashboard' => [
			'title' => 'Dashboard',
			'exp'   => 'Shows analytics information for system',
		],
		// User Pages
		'user' => [
			'list' => [
				'title' => 'Users',
				'exp' => 'Shows all users through system',
			],
			  // Add User Page
			'add' => [
				'title' => 'Add User',
				'exp' => 'Add new user',
				'user_info' => 'User Info',
				'credential' => 'User Credential',
				'contact_info' => 'Contact Info',
				'username' => 'Username',
				'email' => 'Email',
				'password' => 'Password',
				'password_cfm' => 'Confirm Password',
			],
			  // Edit User Page
			'edit' => [
				'title' => 'Edit User',
				'exp' => 'Edit user data',
			],
		],
		// Contract Pages
		'contract' => [
			'list' => [
				'title' => 'All Contracts',
				'exp' => 'Shows all contracts',
			],
			'details' => [
				'title' => 'Contract Details',
				'weekly_info' => 'Weekly',
				'overal_info' => 'Overall',
				'general_info' => 'General',
			],
		],
		// Job Pages
		'job' => [
			'list' => [
				'title' => 'Jobs',
				'exp' => 'Shows all jobs',
			],
		],

		// Work Diary
		'workdiary' => [
			'view' => [
				'title' => 'Work Diary'
			]
		],

		// Reports
		'report' => [
			'transaction' => [
				'title' => 'Transaction'
			],
			'usertransaction' => [
				'title' => 'User Transaction'
			],
		],

		// Ticket
		'ticket' => [
			'list' => [
				'title' => 'Tickets',
			],
		],

		// Notification
		'notification' => [
			'list' => [
				'title' => 'Templates',
			],
			'send' => [
				'title' => 'Send Notification',
			],
		],

		// Skill
		'skill' => [
			'list' => [
				'title' => 'Skills',
				'in_use' => "Skill item ':skill' is still using on and seems not allowed to deactivate it, sorry!",
				'updated' => "Skill item ':skill' has been updated.",
				'removed' => "Skill item ':skill' has been removed.",
				'added' => "Skill item ':skill' has been added.",
				'activated' => "Skill item ':skill' has been activated.",
				'deactivated' => "Skill item ':skill' has been deactivated.",
			],
		],
		// Category
		'category' => [
			'list' => [
				'title' => 'Categories',
				'no_id_given' => 'No id given, please try again.',
				'projects_exist' => 'Wow, there are :number projects associated with this category, please remove projects first or just give up to remove category, sorry.',
				'no_records' => 'No Category found, please try again or reload page.',
				'success_update' => 'The categories have been updated.',
				'type' => [
					'0' => 'Project',
					'1' => 'QA',
					'2' => 'Maintenance',
					'3' => 'FAQ',
				],
			],
		],
		// FAQ
		'faq' => [
			'list' => [
				'title' => 'FAQ',
			],
		],
		// Affiliate
		'affiliate' => [
			'edit' => [
				'title' => 'Affiliate',
			],
		],
		'fee' => [
			'settings' => [
				'title' => 'Fee Settings',
			],
		],    
		'api' => [
			'v1' => [
				'title' => 'Api v1 test',
			],
		],
	],

	/* Search Pages */
	'search' => [
		'job' => [
			'title' => '搜索工作',
		],
		'user' => [
			'title' => '搜索威客',
		],
	],

	/* Message Pages*/
	'message' => [
		'list' => [
			'title' => '消息',
		],
		'threads' => [
			'title' => '留言室',
		],
		'detail' => [
			'title' => '消息',
		]
	],

	/* Notification Pages*/
	'notification' => [
		'list' => [
			'title' => '通知',
		],
	],

	/* Ticket Pages*/
	'ticket' => [
		'list' => [
			'title' => '咨询单',
		],
		'detail' => [
			'title' => '咨询单详情',
		],
	],

	'download_tools' => [
		'title' => '下载工具',
		'desc' => '请下载iJobDesk桌面应用程序以记录您的时薪合同的时间。它将捕获您工作过程的界面截图。',
		'download' => '下载',
		'linux_modal' => [
			'title' => '下载iJobDesk桌面应用程序',
			'desc' => '下载Linux系统的桌面应用程序',
			'version' => '版本',
			'select_placeholder' => '请选择版本...',
			'debian_64' => '基于Debian的64位标准',
			'debian_32' => '基于Debian的32位标准',
			'rpm_64' => '基于RPM的64位标准',
			'rpm_32' => '基于RPM的64位标准',
		],
	]
];