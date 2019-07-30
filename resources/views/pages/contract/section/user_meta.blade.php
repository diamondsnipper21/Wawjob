@if ( $user->contact->country )
<div class="meta mb-2 pt-1">
	<i class="icon-location-pin pull-left"></i>
	<div class="location pull-left ml-2">
		<div class="mb-2">{{ $user->contact->country->name }}</div>
		<span>{{ $user->contact->city }}</span>
	</div>
</div>
@endif

<div class="timezone">
	{{ trans('common.weekdays_abbr.' . convertTz(date('Y-m-d H:i:s'), $user->contact->timezone ? $user->contact->timezone->name : $server_timezone_name, 'UTC', 'N')) }} {{ convertTz(date('Y-m-d H:i:s'), $user->contact->timezone ? $user->contact->timezone->name : $server_timezone_name, 'UTC', 'h:i') }} {{ trans('common.' . convertTz(date('Y-m-d H:i:s'), $user->contact->timezone ? $user->contact->timezone->name : $server_timezone_name, 'UTC', 'a')) }}
</div>