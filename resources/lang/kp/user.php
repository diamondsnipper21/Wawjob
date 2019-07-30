<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Buyer - Job
	|--------------------------------------------------------------------------
	|
	| The following language lines are used by the paginator library to build
	| the simple pagination links. You are free to change them to anything
	| you want to customize your views to better match your application.
	|
	*/

	'user_settings' => '사용자 설정',

	'action'=>[
		'edit' 		=> '편집', 
		'cancel' 	=> '취소', 
		'save'		=> '변경내용보관', 
	], 

	'my'=>[
		'first_name' => '이름', 
		'last_name'  => '성', 
		'language'	 => '언어', 
		'photo'		 => '사진', 
	], 

	'contact' => [
		'country' => '국적', 
		'city'		=> '도시', 
		'address'	=> '주소', 
		'address2'=> '주소 2', 
		'zip_code'=> '우편번호', 
		'phone'		=> '전화번호', 
	], 

	'account' => [
		'title' => '계정',
		'userid' 	=> '사용자 ID',
		'name' 	=> '이름',
		'firstName' 	=> '이름',
		'lastName' 		=> '성',
		'portrait' => '초상',
		'email' => '전자우편'
	],

	'location' => [
        'timezone' => '시간대',
        'address' => '주소',
        'phone' => '전화',
        'country' => '나라',
        'city' => '도시',
        'state' => '주',
    ],

    'invoice_address' => [
    	'address' => '주소',
    ],

	'change_password' => [
		'email'		=> '전자우편', 
		'username'=> '사용자식별자', 
		'in_order_to_change' => 'In order to change your password, you must first enter your old password.',
		'old'			=> '이전암호', 
		'new'			=> '새암호', 
		'confirm'	=> '암호재입력', 
		'error_mismatch_old_password' => 'Old password is mismatch. Retry again.',
	], 

	'change_security_question' => [
		'text_answer' => 'Answer your existing security question in order to create a new one.',
		'text_security_question' => 'Please choose a question you can remember! You will get locked out of your account if you are unable to answer correctly.', 
		'question'		=> 'Question', 
		'answer' => 'Answer', 
		'existing_question' => 'Existing Question',
		'new_question' => 'New Question',
		'important' => 'Important',
		'text_terms' => 'I understand my account will be locked if I am unable to answer this question', 
		'remeber_this_computer'	=> 'Remember this computer', 
		'success_create_security_question' => 'Your security question has been set up successfully.',
		'error_create_security_question' => 'Your security question has not been set up correctly.',
		'success_update_security_question' => 'Your security question has been updated successfully.',
		'error_update_security_question' => 'Your security question has not been updated correctly.',
		'error_mismatch_old_answer' => 'Your answer is mismatch. Retry again.',
	],	

	'notification_settings' => [
		'send_email_notification' => 'Send email notification to',
		'when' => 'when...',
		'message_success_update_notification_settings' => 'Your notification settings have been updated successfully.',
		'message_error_update_notification_settings' => 'Your notification settings have not been updated successfully.',
		'recruiting' => 'Recruiting',
		'freelancer_and_agency_proposals' => 'Freelancer and Agency Proposals',
		'contracts' => 'Contracts',
		'tips_and_advice' => 'Tips and Advice',
		'job_is_posted_or_modified' => 'A job is posted or modified',
		'proposal_is_received' => 'A proposal is received',
		'interview_is_accepted_or_offer_terms_are_modified' => 'An interview is accepted or offer terms are modified',
		'interview_or_offer_is_declined_or_withdrawn' => 'An interview or offer is declined or withdrawn',
		'offer_is_accepted' => 'An offer is accepted',
		'job_posting_will_expire_soon' => 'A job posting will expire soon',
		'job_posting_expired' => 'A job posting expired',
		'proposal_is_submitted' => 'A proposal is submitted',
		'interview_is_initiated' => 'An interview is initiated',
		'offer_or_interview_invitation_is_received' => 'An offer or interview invitation is received',
		'offer_or_interview_invitation_is_withdrawn' => 'An offer or interview invitation is withdrawn',
		'proposal_is_rejected' => 'A proposal is rejected',
		'applied_job_is_modified_or_canceled' => 'A job I applied to is modified or canceled',
		'hire_is_made_or_contract_begins' => 'A hire is made or a contract begins',
		'time_logging_begins' => 'Time logging begins',
		'contract_terms_are_modified' => 'Contract terms are modified',
		'contract_ends' => 'A contract ends',
		'timelog_is_ready_for_review' => 'A timelog is ready for review',
		'weekly_billing_digest' => 'Weekly billing digest',
		'contract_is_going_to_be_automatically_paused' => 'A contract is going to be automatically paused',
		'ijobdesk_has_tip_to_help_me_start' => 'iJobDesk has a tip to help me start',
	],
	
	'affiliate' => [
		'url'		=> 'URL', 
		'emails'	=> 'Emails',
		'send'		=> 'Send',
	], 

	'withdraw' => [
		'balance' => 'Balance',
		'get_paid_now' => 'Get Paid Now',
		'last_payment' => 'Last Payment',
		'payment_methods' => 'Payment Methods',
		'view_all_transactions' => 'View All Transactions',
		'paypal' => 'PayPal',
		'paypal_email' => 'PayPal Email',
		'available_balance'		=> 'Available Balance', 
		'new_balance'=> 'New Balance', 
		'amount'			=> 'Amount', 
		'payment_method'			=> 'Payment Method', 
		'preview'	=> 'Preview', 
		'get_paid' => 'Get Paid',
		'back' => 'Back',
	],

	'deposit' => [
		'deposit' => 'Deposit',
		'deposit_now' => 'Deposit Now',
		'no_deposit_transaction' => 'There is nothing deposit transaction.',
	],	

	'payment_method' => [
		'email'=> 'Email',
		'bank_name' => 'Bank Name',
		'bank_address' => 'Bank Address',
		'swift_code' => 'SWIFT CODE',
		'account_holder_name' => 'Account Holder Name',
		'account_holder_address' => 'Account Holder Address',
		'make_it_primary' => 'Make it primary',
		'add_a_payment_method' => 'Add a Payment Method',
		'select_one_of_the_payment_methods_below' => 'Selct one of the payment methods below',
		'confirm_delete_payment_method' => 'Are you sure to delete this payment method?',
		'message_success_set_as_primary.' => 'Your payment gateway has been successfully set as primary.',
		'message_failed_set_as_primary.' => 'An error occured while setting your payment gateway primary.',
		'message_success_add_payment_method.' => 'Your payment gateway has been successfully added.',
		'message_failed_add_payment_method.' => 'An error occured while adding your payment gateway.',
		'message_success_update_payment_method.' => 'Your payment gateway has been successfully updated.',
		'message_failed_update_payment_method.' => 'An error occured while updating your payment gateway.',
		'message_success_delete_payment_method.' => 'Your payment gateway has been successfully deleted.',
		'message_failed_delete_payment_method.' => 'An error occured while deleting your payment gateway.',
		'message_no_payment_method' => 'No payment method set up.',
	],	

	'close_my_account' => [
		'note_description' => 'Please note that your account will be deleted permanently and it won\'t be recoverable.',
		'reason' => 'Reason',
		'comment_for_ijobdesk' => 'Comment for iJobDesk',
		'go' => 'Go',
		'confirm' => 'Confirm',
		'confirm_close_account' => 'Are you sure to close your account?',
		'permanently_deleted_description' => 'It will be deleted permanently and it won\'t be recoverable.',
		'sorry_for_inconvenience' => 'We are sorry for any inconvenience',
		'message_success_closed_account' => 'You have closed your account successfully.',
		'message_error_closed_account' => 'You have not closed your account successfully.',
		'message_error_closed_account' => 'You have not closed your account successfully.',
		'message_closed_account' => 'You have closed your account.',
	],
];
