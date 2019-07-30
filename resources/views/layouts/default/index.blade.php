<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8">
	<title>{{ !empty($page_title) ? $page_title : ($page == 'freelancer.user.profile' ? trans('page.' . $page . '.title', ['user' => $user->fullname()]) : trans('page.' . $page . '.title')) . ' - ' . trans('page.title') }}</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="author" content="">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="shortcut icon" href="{{ url('favicon.ico') }}">
	
	<link rel="apple-touch-icon" type="image/png" sizes="180x180" href="{{ url('favicon.png') }}">

  	<!--[if lte IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/dist/css/bootstrap.min.css') . '?v=' . $res_version }}">
    @include('layouts.section.fonts')

    <link rel="stylesheet" href="{{ url('assets/styles/common/common.css') . '?v=' . $res_version }}">
    <link rel="stylesheet" href="{{ url('assets/styles/common/components.css') . '?v=' . $res_version }}">
    <link rel="stylesheet" href="{{ url('assets/styles/common/fonts.css') . '?v=' . $res_version }}">
    <link rel="stylesheet" href="{{ url('assets/styles/layouts/' . "$role_id/$role_id" . '.css') . '?v=' . $res_version }}">

    @yield('additional-css')
    @stack('stylesheets')
    <link rel="stylesheet" href="{{ url('assets/styles/' . str_replace('.', '/', $page) . '.css') . '?v=' . $res_version }}">
    @include('layouts.section.script')

</head><!-- END HEAD -->

<body class="layout layout-{{ $role_id }} {{ $role_id }}-page {{ str_replace('.', '-', $page) }} {{ $current_user?'page-logged-in':'page-guest'}}">
	@include('layouts.section.header')

	<!-- BEGIN CONTENT -->
	<div class="page-wrapper page-user">
		<div class="page-section {{ !empty($fullwidth)?'container-fluid':'container' }} default-boxshadow">
			<div class="page-content">
				@if ($page != 'user.security_question' && 
					 $page != 'search.user' && 
					 $page != 'frontend.dashboard' && 
					 $page != 'freelancer.job.my_proposals' && 
					 $page != 'freelancer.job.my_applicant' && 
					 $page != 'buyer.job.all_jobs' && 
					 $page != 'buyer.job.overview' && 
					 $page != 'buyer.job.invite_freelancers' && 
					 $page != 'buyer.job.interviews' && 
					 $page != 'buyer.job.hire_offers' && 
					 $page != 'contract.contract_detail' && 
					 $page != 'message.threads')
					{{ show_warnings() }}
				@endif

				@yield('content')
			</div>
		</div>
	</div><!-- END CONTENT -->

	@include('layouts.section.footer')

	<script src="{{ url('assets/scripts/config.js') }}"></script>
	<script src="{{ url('assets/plugins/requirejs/require.min.js') }}"></script>
	<script src="{{ url('assets/scripts/app.js') }}"></script>
</body>
</html>