
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
	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
	@yield('additional-css')
	<!-- END PAGE LEVEL PLUGIN STYLES -->

	<!-- BEGIN PAGE STYLES -->
	<link rel="stylesheet" href="{{ url('assets/styles/admin/commons.css') }}">
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

<body class="page-md admin-page-body admin-super-page-body {{ str_replace('.', '-', $page) }}">
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
					            <i class="fa fa-tasks font-green-sharp"></i>
					            <span class="caption-subject font-green-sharp bold">Job Details - {{ $job->subject }}&nbsp;&nbsp;&nbsp;<label class="label label-{{ strtolower($job->status_admin_string()) }}">{{ ucfirst($job->status_admin_string()) }}</label></span>
					        </div>
					        <div class="pull-right">
					        	<a href="{{ route('admin.super.job.jobs') }}" class="back-list">&lt; Back to list</a>
					        </div>
							<!-- BEGIN PAGE BREADCRUMB -->
							<!-- @include('layouts.admin.commons.breadcrumbs') -->
							<!-- END PAGE BREADCRUMB -->
					    </div>
					    <div class="portlet-body">
					    	<div class="tabbable-custom">
						        @include('layouts.admin.super.job_detail_nav')
						        <div class="tab-content">
						            <div class="tab-pane active">
						                @yield('content')
						            </div>
						        </div>
						    </div>
					    </div>
					</div>
					
				</div>
			</div>
		</div>
		<!-- END PAGE CONTENT -->
	</div>
	<!-- END PAGE CONTAINER -->

	@include('layouts.admin.commons.javascript_plugins')
	@yield('additional-js')
</body>
</html>