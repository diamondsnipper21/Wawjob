<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<!-- BEGIN HEAD -->
<head>
  <meta charset="utf-8">
  <title>Coming Soon</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">

  <meta name="author" content="">
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link rel="shortcut icon" href="{{ url('favicon.ico') }}">
  <link rel="apple-touch-icon" type="image/png" sizes="180x180" href="{{ url('favicon.png') }}">
  
  <!--[if lte IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/dist/css/bootstrap.min.css') . '?v=' . $res_version }}">
  <link rel="stylesheet" href="{{ url('assets/styles/error/coming_soon.css') . '?v=' . $res_version }}">
  @include('layouts.section.script')
</head><!-- END HEAD -->

<body class="page page-coming-soon-full-page">
  <div class="row">
    <div class="col-md-12 page-coming-soon">
      @yield('content')
    </div>
  </div>
</body>
</html>
