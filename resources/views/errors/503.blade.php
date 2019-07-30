@extends('layouts.error.index')

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 text-center">
					<div class="title margin-top-40">
						<h1>503</h1>
						<div class="hover-line"></div>
					</div>

					<div class="content margin-top-20">
						<div class="desc">{{ trans('page.errors.404.desc') }}</div>
					</div>
					<div class="sub-desc mt-4">{{ trans('page.errors.404.sub_desc') }}</div>
					<div class="margin-top-40 margin-bottom-40">
						<a href="/" class="btn btn-primary px-4 py-2">{{ trans('page.home.title1') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection