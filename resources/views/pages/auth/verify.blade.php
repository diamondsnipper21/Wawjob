@extends('layouts/auth/signup')

@section('content')
<div class="title-section">
	{{ trans('page.auth.signup.verify.verify_your_email_address') }}
</div>

<div class="row">
	<div class="col-md-3">
		<div class="inbox-icon">
			<i class="fa fa-inbox"></i>
		</div>
	</div>

	<div class="col-md-6 col-sm-12">
		{{ show_messages() }}
	</div>
</div>
@endsection