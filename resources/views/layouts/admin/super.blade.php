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
	<title>{{ $current_user->isFinancial() ? 'Financial Management' : 'Super Administrator' }}</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="shortcut icon" href="{{ url('favicon.ico') }}">

	<!-- BEGIN GLOBAL MANDATORY STYLES -->
  	<link rel="stylesheet" href="{{ url('assets/plugins/fontawesome/css/font-awesome.min.css') }}">
  	<link rel="stylesheet" href="{{ url('assets/plugins/simple-line-icons/simple-line-icons.min.css') }}">
	<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ url('assets/plugins/jquery.uniform/css/uniform.default.min.css') }}">
	<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}">
	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
	@yield('additional-css')
	<!-- END PAGE LEVEL PLUGIN STYLES -->

	<!-- BEGIN PAGE STYLES -->
	<link rel="stylesheet" href="{{ url('assets/styles/admin/commons.css?v='.$res_version) }}">
	<link rel="stylesheet" href="{{ url('assets/styles/admin/' . str_replace('.', '/', $page) . '.css?v='.$res_version) }}">
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
	<div class="page-body">
		<div id="page_body_inner" class="page-body-inner">
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
				@if ( $current_user->isFinancial() )
					@include('layouts.admin.financial.commons.top_menu')
				@else
					@include('layouts.admin.super.commons.top_menu')
				@endif
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
						<div class="hide">
							<!-- BEGIN PAGE BREADCRUMB -->
							
							<!-- END PAGE BREADCRUMB -->
						</div>
						<!-- <div class="margin-top-10"> -->
							@yield('content')	
						<!-- </div> -->
					</div>
				</div>
				<!-- END PAGE CONTENT -->
			</div>
			<!-- END PAGE CONTAINER -->
		</div>
	</div>

	@include('layouts.admin.commons.javascript_plugins')
	@yield('additional-js')
</body>
</html>