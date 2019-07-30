@extends('layouts/frontend/index')

@section('css')
<link rel="stylesheet" href="{{ url('assets/styles/frontend/unsubscribe.css') }}">
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="title text-center">
						<h1>{{ trans('page.frontend.unsubscribe.'.$page_key.'.title') }}</h1>
						<div class="hover-line"></div>
					</div>

					<div class="content margin-top-20">
						<div class="success-message text-center mt-4 pt-5">{{ trans('page.frontend.unsubscribe.'.$page_key.'.desc') }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection