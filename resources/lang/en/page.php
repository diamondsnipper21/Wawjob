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
		'title' => 'Home',
		'title1' => 'Home Page',
		'logo'  => 'iJobDesk',
	],

	// About Page
	'frontend' => [
		'contact_us' => [
			'title' 	=> 'Contact Us',
			'success' 	=> 'Thanks for contacting us. We’ll get back to you shortly.',
			'error_captcha' => 'Text in image next doesn’t match. Please try again.',
			'error_user' 	=> 'Your email is not existed in our site. Please try again.',
			'error' 	=> 'Something went wrong. Sorry for such inconvenience.',
			'desc' 	=> 'How can we help you?<br />Contact us and our Customer Support Team will get back to you right away.',
			'got_a_question' => 'Got a question? We\'d love to hear from you!',
		],
		'how_it_works' => [
			'title' => 'How It Works',
		],
		'help' => [
			'title' => 'How can we help you?',
		],
		'download_tools' => [
			'title' => 'Download Tools',
		],
		'dashboard' => [
			'title' => 'Dashboard',
		],
		'coming_soon' => [
			'title' => 'Coming Soon',
		],

		// Unsubscribe Page
		'unsubscribe' => [
			'title' => 'Unsubscribe',
			'guest' => [
				'title' => 'You have been unsubscribed',
				'desc'  => 'As per your request, you have been unsubscribed from iJobDesk marketplace.'
			],
			'user' => [
				'title' => 'Notification Settings Updated',
				'desc'  => 'As per your request, email notification settings have been updated for your account.'
			],
			'ignore' => [
				'title' => 'Notification Settings',
				'desc'  => 'You cannot opt out the important email notifications on iJobDesk.'
			],
		]
	],

	// About Page
	'about' => [
	],

	// Error Pages
	'errors' => [
		'404' => [
			'title' => 'Page Not Found',
			'desc'  	=> 'Oops! Sorry, we cannot find that page.',
			'sub_desc'  => 'Either something went wrong or that page doesn\'t exist anymore.',
		],
	],

	// Authenticate Pages
	'auth' => [
		// Login page
		'login' => [
			'title'             => 'Log in',
			'title_with_space'  => 'Log in',
			'learn_more'        => 'Learn more',
			'login_and_work'    => 'Log in and get to work',
			'username_or_email' => 'Username or Email',
			'password'          => 'Password',
			'remember'          => 'Remember me',
			'forgot'            => 'Forgot password',
			'signup'            => 'Don\'t have an account?&nbsp;&nbsp;<a href=":link">Sign Up</a>',
		],

		'logout' => [
			'title' => 'Log Out',
		],

		'forgot' => [
			'title' => 'Forgot Password',
			'description' => 'Please enter your username or email address. You will receive an email with a link to set a new password.',
			'back_to_login' => 'Back to log in',
			'new_password' => 'New Password',
			'confirm_password' => 'Confirm Password',
			'get_new_password' => 'Get New Password',
			'reset_password' => 'Reset Password',
			'username_or_email' => 'Username or Email',
			'invalid_email' => 'Your email is invalid.',
			'suspended_account' => 'Your account has been suspended.',
			'failed_get_new_password' => 'Failed to get new password.',
			'success_reset_password' => 'Your password has been reset.',
			'submit_get_new_password' => 'An email has been sent with a link. Please check your email.',
			'failed_get_new_password' => 'Something went wrong. Please try again later.',
			'invalid_token' => 'Token is invalid or expired.',
		],
	
		// Singup pages
		'signup_user' => [
			'title'                     => 'Signup',
		],

		'reset' => [
			'title' => 'Reset Your Password',
			'reset_password' => 'Reset Password',
		],
	
		// Singup pages
		'signup' => [
			'title'                     => 'Signup',
			'title_with_space'          => 'Sign up',
			'have_an_account'           => 'Already have a iJobDesk account?',
			'get_started'               => 'Let’s get started!',
			'what_you_are_looking_for'  => 'First, tell us what you’re looking for.',
			'hire_a_freelancer'         => 'I want to hire a freelancer.',
			'find_collaborate'          => 'Find, collaborate with, <br>and pay an expert.',
			'hire'                      => 'Hire',
			'looking_for_online_work'   => 'I’m looking for online work.',
			'find_freelance_projects'   => 'Find Freelance projects and<br>grow your business.',
			'work'                      => 'Work',
			  
			 // Buyer page
			'buyer' => [
				'title'   => 'Sign up as a Buyer',
				'create'  => 'Create a Buyer Account',
				'looking_work'  => 'Looking to work',
				'signup_as_freelancer' => 'Sign up as a freelancer'
			],

		  	// Freelancer page
			'freelancer' => [
				'title'         => 'Sign up as a Freelancer',
				'create'        => 'Create a Freelancer Account',
				'signup_as_buyer' => 'Sign up as a buyer',
				'looking_hire'  => 'Looking to hire',
			],

			'success' => [
				'title' => 'Success Signup Account',
				'verify_your_email_address' => 'Verify your email address',
				'sent_email_description' => 'We\'ve just sent an email to your address :email',
				'check_email_description' => 'Please check your email and click on the link provided to verify your address.',
				'change_email' => 'Change email',
				'new_email' => 'New Email',
				'resend_verification_email' => 'Please re-send that verification email',
				'message_success_changed_email' => 'Your email address has been changed.',
				'message_failed_changed_email' => 'Something went wrong while changing email address.',
				'message_failed_changed_duplicated_email' => 'The email address is already in use.',
				'success_sent_email' => 'We\'ve just sent an email to your email address :email',
				'success_resent_email' => 'We\'ve re-sent an email to your email address :email',
				'failed_sent_email' => 'Failed to send an email to your address :email',
			],

			'verify' => [
				'verify_your_email_address' => 'Verify your email address',
				'invalid_token' => 'Token is invalid or expired.',
				'success_verified' => 'Your email address has been verified.',
			],
		],

		'signup_success' => [
			'title' => 'Success'
		],
	],

	'contract' => [
		'my_contracts' => [
			'title' => 'Contracts'
		],

		'contract_detail' => [
			'title' => 'Contract Detail'
		],

		'feedback' => [
			'title' => 'Leave Feedback'
		],

		'end' => [
			'title' => 'End Contract'
		],
	],

	'report' => [
		'timesheet' => [
			'title' => 'Timesheet'
		]
	],

	'job' => [
		'job_detail' => [
			'title' => ':job'
		],
	],

	'user' => [
		'contact_info' => [
			'title' => 'My Account'
		],
		'account' => [
			'title' => 'Account',
		],
		'location' => [
			'title' => 'Location',        
		],
		'invoice_address' => [
			'title' => 'Invoice Address(Optional)',
		],
		'detail' => [
			'title' => 'Company Details'
		],
		'contact' => [
			'title' => 'Company Contact'
		],
		'change_password' => [
			'title' => 'Change Password'
		],
		'close_my_account' => [
			'title' => 'Close My Account',
		],
		'change_security_question' => [
			'title' => 'Change Security Question'
		],
		'security_question' => [
			'title' => 'Security Question'
		],		
		'payment_method' => [
			'title' => 'Payment Methods'
		],
		'notification_settings' => [
			'title' => 'Notification Settings'
		],
		'affiliate' => [
			'title' => 'Affiliate Program'
		],
		'withdraw' => [
			'title' => 'Withdraw Money'
		],
		'withdraw_preview' => [
			'title' => 'Withdrawal Preview'
		],
	],

	/* Freelancer Pages */
	'freelancer' => [
		'step' => [
			'start' => [
				'title' => 'Start Your Profile'
			],
			'security_question' => [
				'title' => 'Security Question'
			],
			'about_me' => [
				'title' => 'About Me'
			],
			'add_portfolio' => [
				'title' => 'Add Portfolio'
			],
			'add_certification' => [
				'title' => 'Add Certification'
			],
			'add_employment' => [
				'title' => 'Add Employment History'
			],
			'add_education' => [
				'title' => 'Add Education'
			],
			'add_experience' => [
				'title' => 'Add Other Experience'
			]
		],

		'user' => [
			'my_profile' => [
				'title' => 'My Profile'
			],

			'profile' => [
				'title' => ':user'
			],

			'profile_settings' => [
				'title' => 'Profile Settings',
			],
		],

		'job' => [
			'job_detail' => [
				'title' => ':job'
			],

			'job_apply' => [
				'title' => 'Apply to Job'
			],

			'my_applicant' => [
				'title' => 'My Proposal'
			],

			'my_proposals' => [
				'title' => 'My Proposals'
			],

			'my_archived' => [
				'title' => 'Archived Proposals'
			], 

			'search' => [
				'title' => 'Job Search'
			],

			'apply_offer' => [
				'title' => 'Job Offer'
			],

			'accept_invite' => [
				'title' => 'Job Invitation'
			],

			'saved_jobs' => [
				'title' => 'Saved Jobs'
			],
		],

		'contract' => [
			'my_contracts' => [
				'title' => 'My Contracts'
			],

			'my_all_jobs' => [
				'title' => 'My Jobs'
			],

			'contract_detail' => [
				'title' => 'Contract Detail'
			]
		],

		'workdiary' => [
			'viewjob' => [
				'title' => "Work Diary"
			]
		],

		'report' => [
			'overview' => [
				'title' => "Overview"
			],

			'timelogs' => [
				'title' => "Timelogs"
			],

			'transactions' => [
				'title' => "Transactions"
			],

			'timesheet' => [
				'title' => "Timesheet"
			],
		],

	],

	/* Buyer Pages */
	'buyer' => [
		'job' => [
			'create' => [
				'title' => 'Post a Job',
				'sub_title' => 'Post a Job'
			],
			'all_jobs' => [
				'title' => 'Job Postings'
			],
			'my_jobs' => [
				'title' => 'My Jobs'
			],
			'view' => [
				'title' => ':job'
			],
			'edit' => [
				'title' => 'Edit Posting',
				'sub_title' => 'Edit Posting - :job'
			],
			'invite_freelancers' => [
				'title' => 'Invite Freelancers - :job',      	
			],
			'interviews' => [
				'title' => 'Review Proposals - :job',
			],
			'hire_offers' => [
				'title' => 'Hire / Offers - :job',
			],
			'overview' => [
				'title' => 'Overview - :job',
			],
			'hire_user' => [
				'title' => 'Hire'
			],
			'hire' => [
				'title' => 'Hire'
			],
		],

		'contract' => [
			'all_contracts' => [
				'title' => "All Contracts"
			],

			'contract_view' => [
				'title' => "Contract Detail"
			],

			'my_freelancers' => [
				'title' => 'My Freelancers'
			],

			'my_saved_freelancers' => [
				'title' => 'My Saved Freelancers'
			],
		],
		'workdiary' => [
			'view' => [
				'title' => "Work Diary"
			]
		],
		'report' => [
			'weekly_summary' => [
				'title' => "Weekly Summary"
			],
			'transactions' => [
				'title' => "Transactions"
			],
			'timesheet' => [
				'title' => "Timesheet"
			], 
		],

		'user' => [
			'deposit' => [
				'title' => 'Deposit Funds'
			],
			'deposit_preview' => [
				'title' => 'Deposit Preview'
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
			'title' => 'Search for Jobs',
		],
		'user' => [
			'title' => 'Search for Freelancers',
		],
	],

	/* Message Pages*/
	'message' => [
		'list' => [
			'title' => 'Messages',
		],
		'threads' => [
			'title' => 'Message Rooms',
		],
		'detail' => [
			'title' => 'Messages',
		]
	],

	/* Notification Pages*/
	'notification' => [
		'list' => [
			'title' => 'Notifications',
		],
	],

	/* Ticket Pages*/
	'ticket' => [
		'list' => [
			'title' => 'Tickets',
		],
		'detail' => [
			'title' => 'Ticket Detail',
		],
	],

	'download_tools' => [
		'title' => 'Download Tools',
		'desc' => 'Please download the iJobDesk Desktop App to log time for your hourly contracts. It will capture your activities for your hourly contracts.',
		'download' => 'Download',
		'linux_modal' => [
			'title' => 'Download iJobDesk Desktop App',
			'desc' => 'You are going to download the Desktop App for Linux',
			'version' => 'Version',
			'select_placeholder' => 'Select download version...',
			'debian_64' => 'Debian-based 64-bit Standard',
			'debian_32' => 'Debian-based 32-bit Standard',
			'rpm_64' => 'RPM-based 64-bit Standard',
			'rpm_32' => 'RPM-based 32-bit Standard',
		],
	]
];