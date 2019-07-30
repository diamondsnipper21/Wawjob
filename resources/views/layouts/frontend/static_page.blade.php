<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

{{-- Start Head --}}
<head>
	<meta charset="utf-8">
	<title>{{ parse_json_multilang($static_page->title) }}</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="author" content="">
	<meta name="description" content="{{ parse_json_multilang($static_page->desc) }}">
	<meta name="keywords" content="{{ parse_json_multilang($static_page->keyword) }}">
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
    <link rel="stylesheet" href="{{ url('assets/styles/layouts/home/home.css') . '?v=' . $res_version }}">

    <link rel="stylesheet" href="{{ url('assets/styles/' . str_replace('.', '/', $page) . '.css') . '?v=' . $res_version }}">

    @yield('css')
    @include('layouts.section.script')
</head>{{-- End Head --}}

<body class="layout layout-home page page-{{ str_replace('.', '-', $page) }} {{ $current_user?'page-logged-in':'page-guest'}}">
	@include('layouts.section.header')

	<div class="page-wrapper">
		{{-- Start Content --}}
		<div class="page-content">
			<div class="container-fluid">
				@yield('content')
			</div>
		</div>{{-- End Content --}}
	</div>

	@include('layouts.home.footer')

	<script src="{{ url('assets/scripts/config.js') }}"></script>
	<script src="{{ url('assets/plugins/requirejs/require.min.js') }}"></script>
	<script src="{{ url('assets/scripts/app.js') }}"></script>
</body>
</html>