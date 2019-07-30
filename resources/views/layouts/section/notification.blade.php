@if ( isset($system_notifications) && count($system_notifications) > 0)
	<div id="sysnotification">
	@foreach ($system_notifications as $sn)
		<div class="alert alert-warning alert-dismissable">
			<button type="button" class="close sysnotification" sysnotification-id="{{$sn->id}}" data-dismiss="alert" aria-hidden="true">&times;</button>
			{!! nl2br($sn->notification) !!}
		</div>
	@endforeach
	</div>
@endif