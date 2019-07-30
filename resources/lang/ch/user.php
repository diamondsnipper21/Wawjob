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

	'user_settings' => '用户设置',

	'change_password' => [
		'in_order_to_change' => '请输入您的旧密码，以便更改密码',
		'old'			=> '旧密码', 
		'new'			=> '新密码', 
		'confirm'	=> '确认密码', 
		'error_mismatch_old_password' => '旧密码不匹配。请再试一次',
		'success' => '您的密码已被更改'
	], 

	'change_security_question' => [
		'text_answer' => '回答您现有的安全问题，以便创建一个新问题。',
		'text_security_question' => '请选择一个您能记住的问题！<br />如果您无法正确回答，您将被锁定在帐户之外。', 
		'question'		=> '问题', 
		'answer' => '回答', 
		'existing_question' => '现有问题',
		'new_question' => '新问题',
		'important' => '重要',
		'text_terms' => '我了解如果我无法回答此问题，我的帐户将被锁定', 
		'remeber_this_computer'	=> '记住这台电脑', 
		'success_create_security_question' => '您的安全问题已经设置完毕。',
		'error_create_security_question' => '更新安全问题时出了点问题。',
		'success_update_security_question' => '您的安全问题已更新。',
		'error_update_security_question' => '更新安全问题时出了点问题。',
		'error_mismatch_old_answer' => '您的答案不正确。请再试一次。',
		'warning_set_up_security_question' => '您的安全问题尚未设置。单击<a href=":url">此处</a>以设置安全问题和答案。',
	],

	'notification_settings' => [
		'send_email_notification' => '发送电子邮件通知',
		'when' => '什么时候...',
		'message_success_update_notification_settings' => '您的通知设置已更新。',
		'message_error_update_notification_settings' => '更新通知设置时出错。',
		'recruiting' => '招聘',
		'freelancer_proposals' => '威客提案',
		'contracts' => '合同',
		'tips_and_advice' => '提示和建议',
		'job_is_posted_or_modified' => '工作已过期或修改',
		'proposal_is_received' => '收到了一份提案',
		'interview_is_accepted_or_offer_terms_are_modified' => '接受采访或修改提议条款',
		'interview_or_offer_is_declined_or_withdrawn' => '面试或报价被拒绝或撤回',
		'offer_is_accepted' => '机会已接受',
		'job_posting_will_expire_soon' => '招聘消息即将到期',
		'job_posting_expired' => '招聘消息已过期',
		'proposal_is_submitted' => '提交方案',
		'interview_is_initiated' => '面试已开始',
		'offer_or_interview_invitation_is_received' => '收到要约或面试邀请',
		'offer_or_interview_invitation_is_withdrawn' => '要约或面试邀请被撤销',
		'proposal_is_rejected' => '提案被拒绝',
		'applied_job_is_modified_or_canceled' => '我申请的职位被修改或取消',
		'hire_is_made_or_contract_begins' => '聘用或合同开始',
		'time_logging_begins' => '时间记录开始',
		'contract_terms_are_modified' => '合同条款已修改',
		'contract_ends' => '合同结束',
		'timelog_is_ready_for_review' => '时间记录已准备好进行审核',
		'weekly_billing_digest' => '每周结算摘要',
		'ijobdesk_has_tip_to_help_me_start' => 'iJobDesk有一个提示可以帮助我开始',
		'freelancer_job_recommendations' => '威客工作推荐',
		'job_recommendations' => 'iJobDesk对我有工作推荐',
	],

	'affiliate' => [
		'affiliate_program' => '加盟计划',	
		'affiliate_description' => '通过iJobDesk赚取的方法不止一种
iJobDesk加盟计划允许您通过推荐买家或威客获得报酬。
点击<a href=":link">此处</a>了解详情。',
		'no_affiliated_users_found' => '没有找到附属用户',
		'invitations_sent' => '邀请已发送',
		'nothing_sent_invitations_yet' => '您尚未发送任何邀请。请使用下面的邀请表邀请您的朋友尝试iJobDesk。',
		'primary_affiliate_buyer_desc' => '主要会员 - 您邀请的买家已经验证了付款方式。',
		'primary_affiliate' => '主要会员',
		'secondary_affiliate' => '次要会员',
		'affiliate_buyer_desc' => '您朋友邀请的买家已经验证了付款方式。',
		'affiliate_paid_desc' => '推荐威客已经获得报酬。',
		'affiliate_refund_desc' => '推荐威客已经退款。',
		'registered' => '注册',
		'all_income' => '所有收入',
		'lifetime_earnings_by_affiliate_program' => '加盟计划的终身收益',
		'your_referrals' => '您的推荐',
		'it_shows_the_users_registered_by_your_referral' => '显示您的推荐注册的用户',
		'invite_as_buyer' => '邀请成为买家',
		'invite_as_freelancer' => '邀请成为一名威客',
		'let_them_know_url' => '让他们直接知道以下网址',
		'or_send_invitation_here' => '或者在这里发送邀请电子邮件',
		'note_comma_separated_email' => '注意：逗号分隔的电子邮件地址。',
		'message_success_sent_invitation' => '您发了个邀请。',
		'message_failed_sent_invitation' => '联盟电子邮件尚未发送。 - :email',
		'message_already_sent_invitation' => '您已经发了邀请。',
		'message_already_email_existed' => '以下电子邮件已经注册。（:emails）',
		'message_failed_sent_invitation_to_own' => '您无法向您的电子邮件地址发送邀请。',
		'message_failed_invalid_emails' => '您输入的电子邮件地址无效。请再试一次。',
	], 

	'withdraw' => [
		'withdraw_note' => '<strong>注意：</strong>每个工作日都会处理取款。第三方可能会通过PayPal和Skrill等付款方式收取额外费用。',
		'withdraw_footer_note' => '<strong>注意：</strong>因安全理由，您的付款方式在3天之内被激活',
		'withdraw_paypal_note' => '<strong>注意：</strong>手续费为4%',
		'tip_fee_of_withdraw_amount' => '费用为取款金额的:fee',
		'you_are_going_to_send_money_to_gateway' => '您将向您的<span class="gateway">:gateway</span>发送<span class="amount-label"><span class="amount">:amount</span><span class="currency">:currency</span></span>。',
		'gateway_note' => '您将收到:currency。处理取款请求时将适用汇率。它可能不同于当前汇率(1美元 = :rate:currency)。',
		'you_will_get_paid_in_currency' => '您将获得以下付款：currency。',
		'message_no_available_payment_method' => '没有可用付款方式。',
		'click_here_to_add_payment_method' => '点击<a href=":url">这里</a>添加付款方式',
		'message_success_withdrawn' => '您的$:amount取款请求已被接受。您很快就会得到报酬。',
		'message_failed_withdrawn' => '取款时出了点问题。',
		'message_failed_withdrawn_from_amount' => '取款金额应介于$:from和$:to之间',
		'message_failed_withdrawn_not_enough' => '您不能提取超过$:total_amount。',
		'message_failed_no_payment_method' => '未选择付款方式。',
		'message_failed_withdrawn_disabled_method' => '您无法使用禁用的付款方式。',
		'message_wait_until_pending_days' => '您在待处理期后才能使用付款方式。',
		'message_failed_withdrawn_from_gateway' => '您无法提取超过$:total_amount的所选付款方式。',
	],

	'deposit' => [
		'message_success_deposit' => '您的:amount:currency存款请求已被接受。',
		'message_failed_deposit' => '存款时出现问题。',
		'message_failed_payment' => '付款尚未进行。请检查您的付款方式。',
		'message_failed_no_payment_method' => '未选择付款方式。',
		'message_failed_waiting_for_wechat_qrcode' => '我们正在排队请求。 请在大约10分钟后再试一次。',
		'message_failed_exceed_maximum_amount' => '您不能存款超过:currency:amount.',

		'tip_fee_of_deposit_amount' => '费用为存款金额的:fee%。',

		'scan_qrcode_description' => '打开您的微信应用程序，然后选择“扫描QR码”。',
		'scan_qrcode_description2' => '付款后，请点击下面的“完整存款”按钮。<br />我们的支持小组会尽快确认付款并更新您的帐户。',
		'waiting_qrcode_description' => '请等待一分钟，直到加载QR码。',

		'your_account_will_be_charged_in_currency' => '您的帐号将以:currency收费',
		'you_are_going_to_deposit_amount' => '您将存入<span class="amount-label"><strong><span class="amount">0.00</span>:currency</strong></span>。',
		'you_are_depositing_amount' => '您存入了<strong>:amount:currency</strong>。',

		'bank_information' => '银行消息',
		'amount_to_send' => '发送金额',
		'wire_deposit_to' => '存款到',
		'date_of_deposit' => '存款日期',
		'deposit_reference' => '存款参考（收据或参考编号）',
		'deposit_reference_tooltip' => '您的银行交易编号或iJobDesk用户ID',

		'bank_deposit_instruction' => '存入资金后，单击按钮继续。',

		'bank_deposit_instruction2' => '输入您的存款详细消息，以便我们确认您的付款并更快地完成存款。存款后请从银行取一张收据或参考号。',

		'bank_deposit_note' => '注意：您的银行收取的任何交易费用将从总转账金额中扣除。在我们的银行收到资金后的下一个工作日，资金将记入您的余额。如果您有任何疑问，请联系在线服务',

		'manual_deposit_instruction' => '
			<label class="control-label">用:gateway完成存款的提示</label>
			<div class="pt-3">请转到您的<a href=":url" target="_blank">:gateway账户</a>且将钱汇到以下<strong>iJobDesk:gateway帐号：</strong>
				<div class="pt-3">
					<div class="row">
						<div class="col-md-8">
							<div class="copy-box bg-gray p-3">
								<div class="input-group">
								    <input type="text" class="form-control fs-20" value=":address" placeholder="iJobDesk Email Address" id="copy_address">
								    <span class="input-group-btn copy-tooltip" data-toggle="tooltip" data-placement="bottom" title="Copy to Clipboard">
								      	<button class="btn btn-primary" type="button" id="btn_copy">复制</button>
								    </span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pt-4">在您的:gateway账户中付款后，请点击下面的“完成存款”按钮。<br />我们客服会尽快确认付款并更新您的账户。很抱歉给您带来不便。</div>
		',
	],

	'payment_method' => [
		'qr_code' => '二维码',
		'country_of_bank' => '银行国家',
		'bank_name' => '银行名',
		'bank_branch' => '银行支行',
		'beneficiary_swift_code' => '受益人SWIFT代码',
		'iban' => 'IBAN',
		'iban_account_no' => 'IBAN / 账户号码',
		'account_number' => '账户号码',
		'account_name' => '用户名',
		'beneficiary_name' => '受益人名',
		'beneficiary_address1' => '受益人地址1',
		'beneficiary_address2' => '受益人地址2',

		'account_name_2' => 'A / C名称',
		'account_number_2' => '帐户＃',
		'bank_address' => '银行地址',
		'branch_address' => '分支地址',
		'aba_routing_number' => 'ABA / 路由号码',
		'bic' => 'BIC',
		'swift_code' => 'SWIFT代码',

		'receiving_account_sepa' => '欧元收款账户（SEPA)',
		'receiving_account_ach' => '美元收款账户（ACH)',

		'first_name' => '名',
		'last_name' => '姓',
		'phone_number' => '电话号码',
		'card_type' => '卡的种类',
		'card_number' => '卡号',
		'expiry_date' => '到期日',
		'cvv' => 'CVV',
		'visa' => '签证',
		'mastercard' => '万事达',
		'discover' => '发现',
		'american_express' => '美国运通',
		'add_a_payment_method' => '添加付款方式',
		'edit_a_payment_method' => '编辑付款方式',
		'view_qr_code' => '查看二维码',
		'description_qr_code' => '<p>你可以找到正确的QR代码，按照指令。</p><p>1. 打开你的手机上的微信应用程序。<br />2. 点击“我”标签在底部选项。<br />3. 点击“钱包”菜单。<br />4. 点击顶部菜单上的“收付款”。<br />5. 点击“二维码收款”选项。</p>',
		'select_one_of_the_payment_methods_below' => '选择以下付款方式之一',
		'confirm_delete_payment_method' => '您确定要删除此付款方式吗？',
		'message_success_set_as_primary' => '主要付款方式已更新。',
		'message_failed_set_as_primary' => '设置主要支付网关时出错了。',
		'message_non_setup_payment_method' => '目前您还没有付款方式。请添加您的付款方式。点击<a href=":url">此处</a>以添加付款方式。',
		'message_non_setup_payment_method_for_deposit' => '目前您还没有付款方式。请在存款前添加付款方式。点击<a href=":url">此处</a>以添加付款方式。',
		'message_non_setup_payment_method_for_withdraw' => '目前您还没有付款方式。请在退出前添加付款方式。点击<a href=":url">此处</a>以添加付款方式。',
		'message_non_setup_primary_payment_method' => '您目前没有主要付款方式。请在<a href=":url">此处</a>设置您的主要付款方式。',
		'message_success_add_payment_method' => '已添加付款方式。',
		'message_failed_add_payment_method' => '添加付款方式时出错了。',
		'message_success_update_payment_method' => '付款方式已更新。',
		'message_failed_update_payment_method' => '更新付款方式时出现问题。',
		'message_success_delete_payment_method' => '付款方式已被删除。',
		'message_failed_delete_payment_method' => '删除付款方式时出错了。',
		'message_no_payment_method' => '没有付款方式。',
		'message_same_duplicated_payment_method' => '付款方式已经添加。',
		'message_another_duplicated_payment_method' => '付款方式已被其他用户使用。',
		'message_deleted_duplicated_payment_method' => '之前已经有人添加了付款方式。',
		'message_invalid_credit_cardnumber' => '输入的信用卡号无效。',
		'message_invalid_expired_date' => '输入的过期日期无效。',
		'message_not_support_country' => '目前不能对您所在的国家提供服务。',
	],

	'close_my_account' => [
		'note_description' => '请注意，您的帐户将被永久删除，并且无法恢复。',
		'reason_poor_service' => '低劣的服务',
		'reason_irresponsive' => 'Irresponsive',
		'reason_complicated' => '复杂',
		'reason_poor_freelancers' => '可怜的自由职业者',		
		'confirm_close_account' => '您确定要关闭帐户吗？',
		'permanently_deleted_description' => '它将被永久删除，并且无法恢复。',
		'sorry_for_inconvenience' => '对此造成的不便，我们深表歉意。',
		'message_success_closed_account' => '您已关闭帐户。',
		'message_error_closed_account' => '关闭帐户时出错了。',
		'message_closed_account' => '您已关闭帐户。',
	],

	'login' => [
		'error_not_verified' => '您的帐户尚未经过验证。请查看您的邮箱。',
		'error_invalid_login' => '用户名或密码不匹配。',
		'error_blocked' => '您的帐户已被屏蔽，因为您输入了错误的安全答案5次。请联系客户支持。',
		'error_login_blocked' => '登录尝试已被阻止。更多说明已发送到您的电子邮件地址。',
		'error_suspended' => '您的帐户已被暂停。请联系客户支持。',
		'error_blocked_with_password' => '您的帐户已被阻止，因为您输入了错误密码5次。请联系客户支持。',
		'error_invalid_captcha' => '验证码不正确，请重新填写。',
	],

	'become_freelancer' => [
		'button' => '成为一名自由职业者',
		'description' => '您可以轻松地获得自由职业者的工作。',
	],

	'note_invisible_contact_fields' => '<strong>注意：</strong>电子邮件、电话号码以及地址将不会显示给别人。我们将用您的电话号码以确保账户安全。',

	'require_id_verification_username_changed' => '<strong>注意：</strong>更改姓名需要<a href="/help/article/identity-verification" target=\"_blank\">身份证</a>'

];
