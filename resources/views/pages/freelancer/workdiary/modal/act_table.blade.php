<table class="table table-bordered table-slot-act {{$class}}">
	<thead>
		<tr>
			<th>{{ trans('common.time') }}</th>
			<th>{{ trans('common.keyboard') }}</th>
			<th>{{ trans('common.mouse') }}</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($act as $time => $info)
		<tr>
			<td>{{ $time }}</td>
			<td>{{ $info['k'] }}</td>
			<td>{{ $info['m'] }}</td>
		</tr>
		@endforeach
	</tbody>
</table>