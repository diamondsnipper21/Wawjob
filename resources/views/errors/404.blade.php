<?php 
	$user = Auth::user();
?>
@extends($user && $user->isAdmin()?'layouts.admin.error.index':'layouts.error.index')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2 text-center">
			<div class="title margin-top-100">
				<h1>404</h1>
				<div class="hover-line"></div>
			</div>

			<div class="content margin-top-20">
				<div class="desc">{{ trans('page.errors.404.desc') }}</div>
			</div>
			<div class="sub-desc mt-4">{{ trans('page.errors.404.sub_desc') }}</div>

			@if (!$user || !$user->isAdmin())
			<div class="margin-top-40 margin-bottom-40">
				<a href="/" class="btn btn-primary px-4 py-2">{{ trans('page.home.title1') }}</a>
			</div>
			@endif
		</div>
	</div>
</div>
@endsection