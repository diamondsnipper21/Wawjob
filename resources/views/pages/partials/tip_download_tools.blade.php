@if ($current_user->isFreelancer())
<div class="small-box-section no-padding">
	<div class="sub-section">
		<div class="divided-block">
			<h5>{{ trans('common.tips') }}</h5>
		</div>
		<div class="last-div-block">
			<div class="margin-bottom-10">{!! trans('contract.tip_download_tool_description') !!}</div>
			<a href="{{ route('frontend.download_tools') }}">{{ trans('common.download_now') }}</a>
		</div>              
	</div>
</div>
@endif