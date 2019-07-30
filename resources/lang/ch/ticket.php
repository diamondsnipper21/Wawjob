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
	'create_at' => '创建于',
	'Opened' => '已开放',
	'Assigned' => '已分配',
	'Solved' => '已解决',
	'Closed' => '已关闭',
	'content_length_less_5000' => "无法创建咨询单，因为它有超过5000个字符。",
	'you_have_no_opening_tickets' => '您没有活跃的咨询单',
	'you_have_no_archived_tickets' => '您没有存档的咨询单',
	'ticket_closed_successfully' => '咨询单已关闭。',
	'is_closed' => ':name已关闭',
	'Create' => '创建', 
	'Open' => '开放', 
	'Closed' => '关闭',
	'Reply' => '回复',
	'Close' => '关闭',
	'archived' => '存档',
	'status' => '状态',
	'new' => '新',
	'messages' => '消息',
	'type' => '类型',
	'load_more' => '加载更多',
	'no_more' => '没有更多',
	'details' => '细节',
	'No_ticket_Comments_Found' => '没有找到评论。',

	'btn_yes' => '是',
	'btn_no' => '否',

	'modal' => [
		'Close_Ticket' => '关闭咨询单',
		'Create_A_Ticket' => '创建一个咨询单',
		'Reply_Comment' => '回复',
		'Subject' => '主题',
		'Email' => '电子邮件地址',
		'FullName' => '全名',
		'your_name' => '您的名子',
		'type' => '类型',
		'select' => '选择',
		'content' => '内容',
		'message' => '消息',
		'Attachment' => '附件',
		'Your_file_here_please' => '在这里拖放您的文件',
		'Comment' => '评论',
		'captcha' => '请输入验证码。'
	],

	'contact_info' => '联系方式',

	'id_verification' => [
		'ticket_title'   => '身份验证',
		'ticket_content' => "请上传以下文件：<br />一，您目前有效的政府签发的带照片的身份证<br />二，最近的账单<br /><br />在提交文件之前请仔细阅读以下文章：<br /><a href=\"/help/article/identity-verification\" target=\"_blank\">身份验证指南</a><br /><br />您可以提交PNG，JPEG，PDF或BMP文件。<br />请不要以任何方式编辑或更改图像。",
		'ticket_content_company' => "请上传以下文件：<br />一，公司法定名称<br />二， 注册号码<br />三， 注册文件<br />四，最近的账单<br /><br />在提交文件之前请仔细阅读以下文章：<br /><a href=\"/help/article/identity-verification\" target=\"_blank\">身份验证指南</a><br /><br />您可以提交PNG，JPEG，PDF或BMP文件。<br />请不要以任何方式编辑或更改图像。"
	]
	
];
