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

	'user_settings' => 'User Settings',

	'change_password' => [
		'in_order_to_change' => 'Please enter your old password to change your password.',
		'old'			=> 'Old Password', 
		'new'			=> 'New Password', 
		'confirm'	=> 'Confirm Password', 
		'error_mismatch_old_password' => 'Old password doesn\'t match. Please try again.',
		'success' => 'Your password has been changed'
	], 

	'change_security_question' => [
		'text_answer' => 'Answer your existing security question in order to create a new one.',
		'text_security_question' => 'Please choose a question you can remember!<br />You will get locked out of your account if you are unable to answer correctly.', 
		'question'		=> 'Question', 
		'answer' => 'Answer', 
		'existing_question' => 'Existing Question',
		'new_question' => 'New Question',
		'important' => 'Important',
		'text_terms' => 'I understand my account will be locked if I am unable to answer this question', 
		'remeber_this_computer'	=> 'Remember this computer', 
		'success_create_security_question' => 'Your security question has been set up.',
		'error_create_security_question' => 'Something went wrong while updating your security question.',
		'success_update_security_question' => 'Your security question has been updated.',
		'error_update_security_question' => 'Something went wrong while updating your security question.',
		'error_mismatch_old_answer' => 'Your answer is not correct. Please try again.',
		'warning_set_up_security_question' => 'Your security question has not been set up yet. Click <a href=":url">here</a> to set your security question and answer.',
	],

	'notification_settings' => [
		'send_email_notification' => 'Send email notification to',
		'when' => 'when...',
		'message_success_update_notification_settings' => 'Your notification settings have been updated.',
		'message_error_update_notification_settings' => 'Something went wrong while updating notification settings.',
		'recruiting' => 'Recruiting',
		'freelancer_proposals' => 'Freelancer Proposals',
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
		'applied_job_is_modified_or_canceled' => 'A job I applied to is modified or cancelled',
		'hire_is_made_or_contract_begins' => 'A hire is made or a contract begins',
		'time_logging_begins' => 'Time logging begins',
		'contract_terms_are_modified' => 'Contract terms are modified',
		'contract_ends' => 'A contract ends',
		'timelog_is_ready_for_review' => 'A timelog is ready for review',
		'weekly_billing_digest' => 'Weekly billing digest',
		'ijobdesk_has_tip_to_help_me_start' => 'iJobDesk has a tip to help me start',
		'freelancer_job_recommendations' => 'Job Recommendations',
		'job_recommendations' => 'iJobDesk has job recommendations for me',
	],

	'affiliate' => [
		'affiliate_program' => 'Affiliate Program',	
		'affiliate_description' => 'There is more than one way to earn through iJobDesk.
The iJobDesk Affiliate Program allows you to get paid for referring a buyer or a freelancer.
Click <a href=":link">here</a> to read more.',
		'no_affiliated_users_found' => 'No affiliated users found',
		'invitations_sent' => 'Invitations Sent',
		'nothing_sent_invitations_yet' => 'You have not sent any invitations yet. Please invite your friends to try iJobDesk by using the invitation form below.',
		'primary_affiliate_buyer_desc' => 'Primary Affiliate - The buyer you invited has verfied the payment method.',
		'primary_affiliate' => 'Primary Affiliate',
		'secondary_affiliate' => 'Secondary Affiliate',
		'affiliate_buyer_desc' => 'The buyer that your friend invited has verfied the payment method.',
		'affiliate_paid_desc' => 'The referral freelancer has got paid.',
		'affiliate_refund_desc' => 'The referral freelancer has refunded.',
		'registered' => 'Registered',
		'all_income' => 'All Income',
		'lifetime_earnings_by_affiliate_program' => 'Lifetime Earnings by Affiliate Program',
		'your_referrals' => 'Your Referrals',
		'it_shows_the_users_registered_by_your_referral' => 'It shows the users registered by your referral',
		'invite_as_buyer' => 'Invite as a Buyer',
		'invite_as_freelancer' => 'Invite as a Freelancer',
		'let_them_know_url' => 'Let them know the following URL directly',
		'or_send_invitation_here' => 'Or send invitation emails here',
		'note_comma_separated_email' => 'Note: Comma separated email addresses.',
		'message_success_sent_invitation' => 'You\'ve sent an invitation.',
		'message_failed_sent_invitation' => 'Affiliate email has not been sent. - :email',
		'message_already_sent_invitation' => 'You\'ve already sent an invitation.',
		'message_already_email_existed' => 'The following email(s) has already been registered. (:emails)',
		'message_failed_sent_invitation_to_own' => 'You cannot send invitation to your email address.',
		'message_failed_invalid_emails' => 'You entered invalid email addresses. Please try again.',
	], 

	'withdraw' => [
		'withdraw_note' => '<strong>Note:</strong> Withdrawals are processed every business day. Additional fees may also be charged by third-party offering the payment methods such as PayPal and Skrill.',
		'withdraw_footer_note' => '<strong>Note:</strong> For security reasons, your new payment method will become active in three days.',
		'withdraw_paypal_note' => '<strong>Note:</strong> Handling fee 4% will be applied.',
		'tip_fee_of_withdraw_amount' => 'The fee is :fee of the withdraw amount.',
		'you_are_going_to_send_money_to_gateway' => 'You are going to send <span class="amount-label"><span class="currency">:currency</span><span class="amount">:amount</span></span> to your <span class="gateway">:gateway</span>.',
		'gateway_note' => 'You will receive in :currency. Exchange rate will be applied when processing your withdrawal request. It might be different than current exchange rate (1 USD = :rate :currency).',
		'you_will_get_paid_in_currency' => 'You will get paid in :currency',
		'message_no_available_payment_method' => 'No available payment methods.',
		'click_here_to_add_payment_method' => 'Click <a href=":url">here</a> to add payment methods.',
		'message_success_withdrawn' => 'Your withdrawal request for $:amount has been accepted. You will get paid shortly.',
		'message_failed_withdrawn' => 'Something went wrong while withdrawing funds.',
		'message_failed_withdrawn_from_amount' => 'Withdrawn amount should be between $:from and $:to.',
		'message_failed_withdrawn_not_enough' => 'You can not withdraw more than $:total_amount.',
		'message_failed_no_payment_method' => 'No payment method selected.',
		'message_failed_withdrawn_disabled_method' => 'You cannot withdraw funds to the disabled payment method.',
		'message_wait_until_pending_days' => 'You can use the payment method after pending period.',
		'message_failed_withdrawn_from_gateway' => 'You cannot withdraw more than $:total_amount to the selected payment method.',
	],

	'deposit' => [
		'message_success_deposit' => 'Your deposit request for :currency:amount has been accepted.',
		'message_failed_deposit' => 'Something went wrong while depositing funds.',
		'message_failed_payment' => 'Payment has not been proceeded. Please check your payment method.',
		'message_failed_no_payment_method' => 'No payment method selected.',
		'message_failed_waiting_for_wechat_qrcode' => 'We are proceeding a request in queue. Please try again in about 10 minutes.',
		'message_failed_exceed_maximum_amount' => 'You can not deposit more than :currency:amount.',

		'tip_fee_of_deposit_amount' => 'The fee is :fee% of the deposit amount.',

		'scan_qrcode_description' => 'Open your WeChat app and select "Scan QR Code".',
		'scan_qrcode_description2' => 'After paying, please click "Complete Deposit" button below.<br />Our support team will confirm the payment and update your account as soon as possible.',
		'waiting_qrcode_description' => 'Please wait for a minute until the QR code is loaded.',

		'your_account_will_be_charged_in_currency' => 'Your account will be charged in :currency',
		'you_are_going_to_deposit_amount' => 'You are going to deposit <span class="amount-label"><strong>:currency <span class="amount">0.00</span></strong></span>.',
		'you_are_depositing_amount' => 'You are depositing <strong>:currency :amount</strong>.',

		'bank_information' => 'Bank Information',
		'amount_to_send' => 'Amount to send',
		'wire_deposit_to' => 'Wire deposit to',
		'date_of_deposit' => 'Date of deposit',
		'deposit_reference' => 'Deposit Reference (Receipt or reference number)',
		'deposit_reference_tooltip' => 'Reference number from your bank transaction or your iJobDesk user ID',

		'bank_deposit_instruction' => 'Once you have deposited the funds, click the button to proceed.',

		'bank_deposit_instruction2' => 'Enter your deposit details so we can identify your payment and finish the deposit faster. Please take a receipt or reference number from your bank after depositing.',

		'bank_deposit_note' => 'Note: Any transaction fees charged by your bank will be deducted from the total transfer amount. Funds will be credited to your balance on the next business day after the funds are received by our bank. If you have any questions please contact customer support.',

		'manual_deposit_instruction' => '
			<label class="control-label">Tips for completing a deposit by :gateway</label>
			<div class="pt-3">Please go to your <a href=":url" target="_blank">:gateway account</a> and send the money to the following <strong>iJobDesk :gateway Account:</strong>
				<div class="pt-3">
					<div class="row">
						<div class="col-md-8">
							<div class="copy-box bg-gray p-3">
								<div class="input-group">
								    <input type="text" class="form-control fs-20" value=":address" placeholder="iJobDesk Email Address" id="copy_address">
								    <span class="input-group-btn copy-tooltip" data-toggle="tooltip" data-placement="bottom" title="Copy to Clipboard">
								      	<button class="btn btn-primary" type="button" id="btn_copy">Copy</button>
								    </span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pt-4">After paying in your :gateway Account, please click "Complete Deposit" button below.<br />Our support team will confirm the payment and update your account as soon as possible. Sorry for such inconvenience.</div>
		',
	],

	'payment_method' => [
		'qr_code' => 'QR Code',
		'country_of_bank' => 'Country of Bank',
		'bank_name' => 'Bank Name',
		'bank_branch' => 'Bank Branch',
		'beneficiary_swift_code' => 'Beneficiary SWIFT Code',
		'iban' => 'IBAN',
		'iban_account_no' => 'IBAN / Account No',
		'account_number' => 'Account Number',
		'account_name' => 'Account Name',
		'beneficiary_name' => 'Beneficiary Name',
		'beneficiary_address1' => 'Beneficiary Address 1',
		'beneficiary_address2' => 'Beneficiary Address 2',

		'account_name_2' => 'A/C Name',
		'account_number_2' => 'Account#',
		'bank_address' => 'Bank Address',
		'branch_address' => 'Branch Address',
		'aba_routing_number' => 'ABA/Routing Number',
		'bic' => 'BIC',
		'swift_code' => 'SWIFT Code',

		'receiving_account_sepa' => 'EUR Receiving Account (SEPA)',
		'receiving_account_ach' => 'USD Receiving Account (ACH)',

		'first_name' => 'First Name',
		'last_name' => 'Last Name',
		'phone_number' => 'Phone Number',
		'card_type' => 'Card Type',
		'card_number' => 'Card Number',
		'expiry_date' => 'Expiry Date',
		'cvv' => 'CVV',
		'visa' => 'Visa',
		'mastercard' => 'MasterCard',
		'discover' => 'Discover',
		'american_express' => 'American Express',
		'add_a_payment_method' => 'Add a Payment Method',
		'edit_a_payment_method' => 'Edit a Payment Method',
		'view_qr_code' => 'View QR Code',
		'description_qr_code' => '<p>You can find your correct QR code as following the instructions.</p><p>1. Open your WeChat app on your phone.<br />2. Click "Me" tab at the bottom tab.<br />3. Click "Wallet" menu.<br />4. Click "Money" on the top menus.<br />5. Click "Receive Money" tab.</p>',
		'select_one_of_the_payment_methods_below' => 'Select one of the payment methods below',
		'confirm_delete_payment_method' => 'Are you sure to delete this payment method?',
		'message_success_set_as_primary' => 'Primary payment method has been updated.',
		'message_failed_set_as_primary' => 'Something went wrong while setting primary payment gateway .',
		'message_non_setup_payment_method' => 'You have no payment methods at the moment. Please add your payment methods. Click <a href=":url">here</a> to add payment methods.',
		'message_non_setup_payment_method_for_deposit' => 'You have no payment methods at the moment. Please add your payment methods before you deposit. Click <a href=":url">here</a> to add payment methods.',
		'message_non_setup_payment_method_for_withdraw' => 'You have no payment methods at the moment. Please add your payment methods before you withdraw. Click <a href=":url">here</a> to add payment methods.',
		'message_non_setup_primary_payment_method' => 'You have no primary payment method at the moment. Please set up your primary payment method <a href=":url">here</a>.',
		'message_success_add_payment_method' => 'A payment method has been added.',
		'message_failed_add_payment_method' => 'Something went wrong while adding a payment method.',
		'message_success_update_payment_method' => 'A payment method has been updated.',
		'message_failed_update_payment_method' => 'Something went wrong while updating a payment method.',
		'message_success_delete_payment_method' => 'A payment method has been deleted.',
		'message_failed_delete_payment_method' => 'Something went wrong while deleting a payment method.',
		'message_no_payment_method' => 'No payment method',
		'message_same_duplicated_payment_method' => 'The payment method has been added already.',
		'message_another_duplicated_payment_method' => 'The payment method has been used by another user.',
		'message_deleted_duplicated_payment_method' => 'The payment method had been added by someone before.',
		'message_invalid_credit_cardnumber' => 'Invalid credit card number entered.',
		'message_invalid_expired_date' => 'Invalid expired date entered.',
		'message_not_support_country' => 'Your country is not supported at the moment.',
	],

	'close_my_account' => [
		'note_description' => 'Please note that your account will be deleted permanently and it won\'t be recoverable.',
		'reason_poor_service' => 'Poor Service',
		'reason_irresponsive' => 'Irresponsive',
		'reason_complicated' => 'Complicated',
		'reason_poor_freelancers' => 'Poor Freelancers',		
		'confirm_close_account' => 'Are you sure to close your account?',
		'permanently_deleted_description' => 'It will be deleted permanently and it won\'t be recoverable.',
		'sorry_for_inconvenience' => 'We are sorry for any inconvenience.',
		'message_success_closed_account' => 'You have closed your account.',
		'message_error_closed_account' => 'Something went wrong while closing your account.',
		'message_closed_account' => 'You have closed your account.',
	],

	'login' => [
		'error_not_verified' => 'Your account has not been verified yet. Please check your email.',
		'error_invalid_login' => 'Username or password doesn\'t match.',
		'error_blocked' => 'Your account has been blocked because you entered wrong security answer 5 times. Please contact customer support.',
		'error_login_blocked' => 'A login attempt has been blocked. More instructions has been sent to your email address.',
		'error_suspended' => 'Your account has been suspended. Please contact customer support.',
		'error_blocked_with_password' => 'Your account has been blocked because you entered wrong password 5 times. Please contact customer support.',
		'error_invalid_captcha' => 'Text in image below doesn’t match. Please try again.',
	],

	'become_freelancer' => [
		'button' => 'Become a freelancer',
		'description' => 'You can get a job as freelancer easily.',
	],

	'note_invisible_contact_fields' => '<strong>Note:</strong> Email, Phone number and Address won\'t be visible to others. We will use your Phone number for account security.',

	'require_id_verification_username_changed' => '<strong>Note:</strong> Changing your name will require <a href="/help/article/identity-verification" target=\"_blank\">Identity verification</a>'

];
