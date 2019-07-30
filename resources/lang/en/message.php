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
				'success_create' => 'Job has been posted.', 
				'success_update' => 'Job has been updated.',
				'success_repost' => 'Job has been reposted.',
				'success_draft' => 'Draft has been saved.',
				'success_close' => 'Job has been closed.', 
				'success_cancel' => 'Job has been cancelled.', 
				'success_delete_draft' => 'Draft has been deleted.', 
				'failed_payment_for_featured' => 'Something went wrong while processing payment.',
			],
			'offer' => [
				'success_withdraw' => 'You have withdrawn an offer.',
			],			
			'contract' => [
				'already_opened' => 'You have already started contract with :sb',
				'already_offered' => 'You have already sent a job offer to :sb',
				'already_hired' => 'You have already hired a contractor.',
			], 
			'invite' => [
				'success' => ':contractor_name has been invited to ":job_title".', 
				'failure' => 'You have already sent an offer or an invitation to :contractor_name.',
			],
		],

		'payment' => [
			'failed' => 'Something went wrong while sending your payment request.',
			'failed_user_suspended' => 'You cannot perform the action because your freelancer has been suspended.',
			'contract' => [
				"success_paid" => "You have paid $:amount.", 
			]
		],

		'contract' => [
			'changed_term' => 'Your contract term has been changed.',
			'paused' => 'Your contract has been paused.',
			'restarted' => 'Your contract has been restarted.',
			'close' => [
				'success_close' => 'You have closed job ":contract_title".',
			],
			'send_offer' => [
				'success_offer' => 'You have sent an offer.',
				'failure_validation' => 'Please check all required fields.',
				'failure' => 'Something went wrong while sending an offer. Please try again later.',
			],
			'milestones' => [
				'fund_milestone_success' => 'You have deposited funds in Escrow for a milestone ":milestone".',
				'fund_milestone_error' => 'Something went wrong while depositing funds for a milestone ":milestone".',
				'release_milestone_success' => 'You have released funds in Escrow for a milestone ":milestone".',
				'release_milestone_error' => 'Something went wrong while releasing funds for a milestone ":milestone".',
				'success_added_milestone' => "A milestone has been created.",
				'success_updated_milestone' => "A milestone has been updated.",
				'success_deleted_milestone' => "A milestone has been deleted.",
				'failed_added_milestone' => "Something went wrong while creating a milestone.",
				'failed_updated_milestone' => "Something went wrong while updating a milestone.",
				'failed_deleted_milestone' => "Something went wrong while deleting a milestone.",
				'error_milestone_date' => "Please input the correct milestone date.",
			]
		],
	],

	'freelancer' => [
		'job' => [
			'apply' => [
				'success_apply' => 'You have submitted a proposal to ":job_title".',
			],
			'invite' => [
				'accept' => 'You have accepted the invitation to ":job_title".',
				'decline' => 'You have declined the invitation to ":job_title".',
				'error_max_fixed_price' => 'Billing amount should be less than $:amount.',
				'error_max_hourly_price' => 'Hourly billing amount should be less than $:amount.',
			],
			'offer' => [
				'success_accept' => 'You have accepted the offer.',
				'success_reject' => 'You have declined the offer.',
				'error_accept' => 'Something went wrong while accepting the offer',
			],
			'proposal' => [
				'success_withdraw' => 'You have withdrawn the proposal.',
				'success_revise_term' => 'You have revised the term.',
			],
		],

		'contract' => [
			'close' => [
				'success_close' => 'You have closed a job ":contract_title".',
			],
		],

		'payment' => [
			'failed' => 'Something went wrong while sending payment request.',	
			'failed_user_suspended' => 'You cannot perform the action because your client has been suspended.',
			'contract' => [
				'success_refund' => 'You have refunded $:amount.', 
				'success_refund_fund' => 'You have refunded the funds in Escrow.', 
				'success_request_payment' => 'You have send a notification to ask the client to release the funds in Escrow.', 
				'failed_refund_amount_over_paid_amount' => 'You cannot refund more than you received.', 
				'failed_refund_amount_over_balance' => 'You have insufficient funds in your account.',
				'failed_refund_fund' => 'You cannot refund the funds in Escrow.',
			],
			'withdraw' => [
				'success_withdraw' => 'You have withdrawn $:amount.', 
				'failed_request_payment' => 'You cannot request the payment.', 
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

	'no_such_job_posting' => 'There are no such job postings.',
	'not_enough_balance' => 'You have insufficient funds in your account.',
	'load_more_messages' => 'Load More Messages',
	'no_messages' => 'No Messages',
	'error_empty_price' => 'Please input the valid price.', 

	'api' => [
		'error' => [
			'1' => 'Username or password doesn\'t match.',
			'2' => 'Your account has not been verified yet. Please check your email and follow the instruction.',
			'3' => 'Your account has been suspended. Please contact customer support.',
			'4' => 'Your account has been blocked because you entered wrong password 10 times. Please contact customer support.',
			'5' => 'Invalid login token.',
			'6' => 'No such contracts',
			'7' => 'You have reached the weekly limit for this week.',
			'8' => 'The contract has been paused.',
			'9' => 'The contract has been suspended.',
			'10' => 'The contract has been closed.',
			'11' => 'The client account has been suspended.',
			'12' => 'Your screenshot is invalid.',
			'13' => 'The client has insufficient funds in their account.',
			'14' => 'Invalid logs.',
			'15' => 'The client has insufficient funds in their account.',
			'99' => 'An error occurred while checking database.',
		]
	]

];