<?php
/**
* @author Ro Un Nam
* @since Jun 09, 2017
*/
?>

@if ( count($timesheets) )
<div class="timesheet-detail-section section">
	<div class="section-title">{{ trans('report.timesheet_detail') }}</div>
	<div class="desc">{{ trans('report.click_on_any_day_hours_to_view_work_diary') }}</div>

	<div class="section-content">
		<table class="table">
			<thead>
				<tr>
					<th>{{ trans('common.client') }}</th>
					@for($offset = 0; $offset < 7; $offset++)
					<th width="8%" class="day">
						<?php 
							$one_date = date_add(date_create($from), date_interval_create_from_date_string("{$offset} days")); 
						?>
						<div>{{ trans('common.weekdays_abbr.' . date_format($one_date, 'N')) }}</div>
					</th>
					@endfor
					<th width="10%">{{ trans('common.total_hours') }}</th>
					<th width="10%">{{ trans('common.rate') }}</th>
					<th width="10%">{{ trans('common.amount_billed') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($timesheets as $cid => $cts)
				<tr>
					<td>
						@if ( $cts['mins'] )
							<a href="{{ route('report.timesheet') }}?contract_selector={{ $cid }}">
							{{ $cts['client'] }}
							</a>
						@else
							{{ $cts['client'] }}
						@endif
					</td>
					@for($offset = 1; $offset <= 7; $offset++)
					<td class="day">
						{{ isset($cts['week'][$offset]) ? formatMinuteInterval($cts['week'][$offset]->mins) : '' }}
					</td>
					@endfor
					<td>
						{{ formatMinuteInterval($cts['mins']) }}
					</td>
					<td>
						${{ formatCurrency($cts['rate']) }}
					</td>
					<td>
						${{ formatCurrency($cts['amount']) }}
					</td>
				</tr>
				@endforeach

				<tr class="bg-white border-light-bottom">
					<td>{{ trans('common.sub_total') }}</td>
					@for($offset = 1; $offset <= 7; $offset++)
					<td class="day">
						{{ isset($total['week'][$offset]) ? formatMinuteInterval($total['week'][$offset]) : '' }}
					</td>
					@endfor
					<td>
						{{ formatMinuteInterval($total['mins']) }}
					</td>
					<td></td>
					<td>
						${{ formatCurrency($total['amount']) }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div><!-- .timesheet-detail-section -->
@endif