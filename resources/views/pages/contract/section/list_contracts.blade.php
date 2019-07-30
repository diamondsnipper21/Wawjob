<div class="box-section">
	@if ( !$contracts->isEmpty() )
		<div class="row box-header">
			<div class="col-md-5">
				{!! render_pagination_desc('common.showing_of_contracts', $contracts) !!}
			</div>
			<div class="col-md-3 hidden-mobile">{{ trans('common.time_period') }}</div>
			<div class="col-md-2 hidden-mobile">{{ trans('common.terms') }}</div>
		</div>
		@foreach ( $contracts as $contract )
			@include ('pages.contract.section.contract_row')
		@endforeach

		<div class="row box-pagination">
			<div class="col-md-6">
				{!! render_pagination_desc('common.showing_of_contracts', $contracts) !!}
			</div>
			<div class="col-md-6 text-right">
				{!! $contracts->render() !!}
			</div>
		</div>
	@else
		<div class="not-found-result">
			<div class="row">
				<div class="col-md-12 text-center">
					<div class="heading">{{ trans('contract.you_have_no_contracts') }}</div>
				</div>
			</div>
		</div>
	@endif
</div>