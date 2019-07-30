<?php
/**
* Layout for user detail pages.
*
* @author KCG
* @since July 11, 2017
* @version 1.0
*/

use iJobDesk\Models\User;

?>

<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.2.0
Version: 3.1.3
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8"/>
	<title>Super Administrator</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="shortcut icon" href="{{ url('favicon.ico') }}">

	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	@include('layouts.section.fonts')
	<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ url('assets/plugins/jquery.uniform/css/uniform.default.min.css') }}">
	<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}">
	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
	@yield('additional-css')
	<!-- END PAGE LEVEL PLUGIN STYLES -->

	<!-- BEGIN PAGE STYLES -->
	<link rel="stylesheet" href="{{ url('assets/styles/admin/commons.css') }}">
	<link rel="stylesheet" href="{{ url('assets/styles/admin/super/user/commons.css') }}">
	<link rel="stylesheet" href="{{ url('assets/styles/admin/' . str_replace('.', '/', $page) . '.css') }}">
	<!-- END PAGE STYLES -->

	<!-- BEGIN THEME STYLES -->
	<link href="{{ url('assets/plugins/metronic/global/css/components-md.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/global/css/plugins-md.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/admin/layout3/css/layout.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/admin/layout3/css/themes/blue-steel.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/admin/layout3/css/custom.css') }}" rel="stylesheet" type="text/css"/>
	<!-- END THEME STYLES -->

	@include('layouts.section.script')
</head>

<body class="page-md admin-page-body admin-super-page-body super-user-page {{ str_replace('.', '-', $page) }}">
	<!-- BEGIN HEADER -->
	<div class="page-header">
		<!-- BEGIN HEADER TOP -->
		<div class="page-header-top">
			<!-- BEGIN LOGO -->
			<div class="page-logo">
				<a href="{{ route('admin.super.dashboard') }}" title="{{ config('app.name') }}"><img src="/assets/images/common/logo.png" /></a>
			</div>
			<!-- END LOGO -->
			<!-- BEGIN HEADER MENU -->
			@include('layouts.admin.commons.top_nav')
			<!-- END HEADER MENU -->
		</div>
		<!-- END HEADER TOP -->
		<!-- BEGIN HEADER MENU -->
		@include('layouts.admin.super.commons.top_menu')
		<!-- END HEADER MENU -->
	</div>
	<!-- END HEADER -->

	<!-- BEGIN PAGE CONTAINER -->
	<div class="page-container">
		<!-- BEGIN PAGE HEAD -->
		<div class="page-head hide">
			<div class="container-fluid">
				<!-- BEGIN PAGE TITLE -->
				<div class="page-title">
					<h1>{{ $page_title }} <small>statistics &amp; reports</small></h1>
				</div>
				<!-- END PAGE TITLE -->
			</div>
		</div>
		<!-- END PAGE HEAD -->
		<!-- BEGIN PAGE CONTENT -->
		<div class="page-content">
			<div class="container-fluid">
				<div class="">
					<div class="portlet light">
						<div class="portlet-title">
			                <div class="caption">
			                    <i class="icon-bar-chart font-green-sharp hide"></i>
			                    <span class="caption-helper"><span class="caption-subject font-green-sharp bold"><i class="icon-user"></i>&nbsp;&nbsp;User Details</span></span>
			                </div>
			                <div class="actions user-status-actions">
			                	<form id="form_user_status" action="{{ route('admin.super.user.change_status', ['user_id' => $user->id]) }}" method="post">
						    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />

						        	<div class="toolbar pull-right">
						        		<span><strong>Action</strong>&nbsp;</span>
										<select name="status" class="table-group-action-input form-control input-inline input-small input-sm select2 select-change-status" data-auto-submit="false">
											<option value="">Select...</option>
			                                @if ( $user->status == User::STATUS_AVAILABLE )
			                                <option value="{{ User::STATUS_SUSPENDED }}">Suspend</option>
			                                <option value="{{ User::STATUS_FINANCIAL_SUSPENDED }}">Financial Suspend</option>
			                                @endif
			                                @if ( $user->status == User::STATUS_SUSPENDED || $user->status == User::STATUS_FINANCIAL_SUSPENDED || $user->status == User::STATUS_DELETED)
			                                <option value="{{ User::STATUS_AVAILABLE }}">Activate</option>
			                                @endif
			                                <!-- Resetting security answer -->
			                                <option value="RESET_SECURITY_ANSWER">Reset Security Answer</option>

			                                @if ( $user->status != User::STATUS_DELETED )
			                                <option value="{{ User::STATUS_DELETED }}">Delete</option>
			                                @endif
										</select>
										<button class="btn btn-sm yellow table-group-action-submit button-change-status" type="button" disabled data-auto-submit="false"><i class="fa fa-check"></i> Submit</button>
									</div>
								</form>
								@if (!empty($message))
									{{ show_messages() }}
									<div class="user-hidden-status label label-{{ $user->colorByStatus() }}">
										{{ array_search($user->status, User::getOptions('status')) }}
									</div>
								@endif
			                </div>
			            </div>
			            <div class="portlet-body">
			            	<div class="user-short-info">
			            		<div class="row">
			            			<div class="col-md-6">
			            				<img src="{{ avatar_url($user) }}" class="img-circle user-avatar" width="100" />
			            				<div class="user-name-loc">
				            				<div class="user-fullname">{{ $user->fullname }} <span class="user-role">({{ $user->role_name }})</span>&nbsp;&nbsp;<span class="normal-case user-status">
				            					<span class="label label-{{ $user->colorByStatus() }}">{{ array_search($user->status, User::getOptions('status')) }}</span></span>
				            					@if ($user->id_verified == 1)
				            						<span class="label label-id-verified">ID Verified</span></span>
				            					@endif
				            					@if ($user->isLoginBlocked())
				            						<span class="label label-warning normal-case">Login Blocked</span></span>
				            					@endif
				            				</div>
				            				<div class="user-location"><i class="fa fa-map-marker"></i> {{ $user->location }}</div>
				            			</div>
			            			</div>
			            			<div class="col-md-6">
			            				@if ($user->role == User::ROLE_USER_BOTH)
			            				<div class="view-role pull-right">
			            					<label class="pull-left">View:</label>
			            					<select class="select2" name="role" data-width="150">
			            						<option value="{{ User::ROLE_USER_BUYER }}">Buyer</option>
			            						<option value="{{ User::ROLE_USER_FREELANCER }}">Freelancer</option>
			            					</select>
			            				</div>
			            				@endif
			            			</div>
			            		</div>
			            	</div>
			            	<div class="user-detail-message"></div>
			            	<div class="tabbable-custom">
			            		@if ($user->isFreelancer())
			            			@include('layouts.admin.super.user.freelancer')
			            		@else
			            			@include('layouts.admin.super.user.buyer')
			            		@endif
					            <div class="tab-content">
					                <div class="tab-pane active">
										<!-- BEGIN PAGE BREADCRUMB -->
										@include('layouts.admin.commons.breadcrumbs')
										<!-- END PAGE BREADCRUMB -->
					                    @yield('content')
					                </div>
					            </div><!-- .tab-content -->
					        </div><!-- .tabbable-custom -->	            	
			            </div><!-- .portlet-body -->
					</div><!-- .portlet -->
				</div>
			</div><!-- .container-fluid -->
		</div>
		<!-- END PAGE CONTENT -->
	</div>
	<!-- END PAGE CONTAINER -->

	@include('layouts.admin.commons.javascript_plugins')
	@yield('additional-js')
</body>
</html>