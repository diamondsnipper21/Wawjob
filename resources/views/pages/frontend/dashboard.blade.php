@extends('layouts/default/index')

@section('content')

<script type="text/javascript">
	var show_congratulation = {{ $show_congratulation?'true':'false' }};
</script>

@if ($current_user->isBuyer())
	@include('pages.frontend.dashboard.buyer')
@else
	@include('pages.frontend.dashboard.freelancer')
@endif

{{-- Show modal for congratulations --}}
@if ($show_congratulation)
	@include('pages.frontend.dashboard.modal_congratulations')
@endif

@endsection