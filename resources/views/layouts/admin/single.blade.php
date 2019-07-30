
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
	<title>iJobDesk Login</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>

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
	<link rel="stylesheet" href="{{ url('assets/styles/admin/' . str_replace('.', '/', $page) . '.css') }}">
	<link rel="stylesheet" href="{{ url('assets/styles/admin/commons.css?v='.$res_version) }}">
	<!-- END PAGE STYLES -->

	<!-- BEGIN THEME STYLES -->
	<link href="{{ url('assets/plugins/metronic/global/css/components-md.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/global/css/plugins-md.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/admin/layout/css/layout.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/admin/layout/css/themes/default.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ url('assets/plugins/metronic/admin/layout/css/custom.css') }}" rel="stylesheet" type="text/css"/>
	<!-- END THEME STYLES -->

	@include('layouts.section.script')
</head>

<body class="page admin-page-body {{ str_replace('.', '-', $page) }}">
	<a class="page-logo" href="{{ url('/') }}" title="{{ config('app.name') }}"><img src="/assets/images/common/logo_admin.png" /></a>
	<div class="content">
		@yield('content')
	</div>

	@include('layouts.admin.commons.javascript_plugins')
	@yield('additional-js')
</body>
</html>