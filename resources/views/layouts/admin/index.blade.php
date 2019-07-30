<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="author" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ trans('page.admin.' . $page . '.title') }} - {{ trans('page.title') }}</title>

  <link rel="shortcut icon" href="{{ url('favicon.ico') }}">

  <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/plugins/simple-line-icons/simple-line-icons.min.css') }}">

  <link rel="stylesheet" href="{{ url('assets/styles/admin/fonts.css') }}">
  <link rel="stylesheet" href="{{ url('assets/styles/admin/components.css') }}">
  <link rel="stylesheet" href="{{ url('assets/styles/admin/layout.css') }}">

  @if (isset($css))
    @if (is_array($css) && count($css) > 0)
      {{-- multiple css --}}
      @foreach ($css as $css_path)
      <link rel="stylesheet" href="{{ url('assets/styles/admin/'.str_replace('.', '/', $css_path).'.css') }}">
      @endforeach
    @else
      {{-- single file --}}
      <link rel="stylesheet" href="{{ url('assets/styles/admin/'.str_replace('.', '/', $css).'.css') }}">
    @endif
  @endif

  @if (isset($component_css))
    @if (is_array($component_css) && count($component_css) > 0)
      {{-- multiple css --}}
      @foreach ($component_css as $css_path)
      <link rel="stylesheet" href="{{ url($css_path.'.css') }}">
      @endforeach
    @else
      {{-- single file --}}
      <link rel="stylesheet" href="{{ url($component_css.'.css') }}">
    @endif
  @endif

  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  @include('layouts.section.script')
</head>

<body class="page admin-page-body {{ str_replace('.', '-', $page) }}">
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    @include('layouts.admin.header')
    @include('layouts.admin.sidebar')
  </nav>
  <div class="page-container">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 page-bar">
          <h3>{{ trans('page.admin.' . $page . '.title') }}</h3>
          <ul class="page-actions">@yield('actions')</ul>
          @yield('toolbar')
        </div>
      </div>
      @yield('content')
    </div>
  </div>

  @yield('js')
  <script src="{{ url('assets/scripts/admin/config.js') }}"></script>
  <script src="{{ url('assets/plugins/requirejs/require.min.js') }}"></script>
  <script src="{{ url('assets/scripts/admin/app.js') }}"></script>
</body>
</html>
