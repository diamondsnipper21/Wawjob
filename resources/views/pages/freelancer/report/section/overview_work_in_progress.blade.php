<?php
/**
* @author Ro Un Nam
* @since Jun 08, 2017
*/

use iJobDesk\Models\ContractMilestone;
?>
<div class="section section-hourly">
	<div class="section-title">
		{{ trans('report.timesheet_for_period', ['period' => date_format(date_create($week['from']), $format_date) . ' - ' . date_format(date_create($week['to']), $format_date)]) }} ({{ trans('common.this_week') }}) <span class="sep">|</span> {{ trans('common.in_progress') }}
	</div>

	@if ( count($timesheets) )
	<div class="manual-alert">
		{!! trans('report.includes_n_hrs_manual_time', ['n' => formatMinuteInterval($total_work_in_progress['manual_hours'])]) !!}
	</div>
	
	<div class="section-content">		
		<table class="table">
			<thead>
				<tr>
					<th width="20%">{{ trans('common.contract') }}</th>
					@for ( $offset = 0; $offset < 7; $offset++ )
					<th width="4%" class="day text-center">
						<?php 
							$one_date = date_add(date_create($week['from']), date_interval_create_from_date_string("{$offset} days"));
						?>
						<div>{{ trans('common.weekdays_abbr.' . date_format($one_date, 'N')) }}</div>
						<div>{{ date_format($one_date, $format_date) }}</div>
					</th>
					@endfor
					<th>{{ trans('common.hours_cap') }}</th>
					<th>{{ trans('common.rate') }}</th>
					<th class="text-right">{{ trans('common.amount') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($timesheets as $cid => $cts)
				<tr>
					<td>
						<a href="{{ _route('contract.contract_view', ['id' => $cid]) }}" class="break">{{ $cts['contract']->title }}</a>
					</td>
					@for($offset = 1; $offset <= 7; $offset++)
					<td width="8%" class="day text-center">
						{{ isset($cts['week'][$offset])? formatMinuteInterval($cts['week'][$offset]->mins): '-' }}
					</td>
					@endfor
					<td>{{ formatMinuteInterval($cts['mins']) }}</td>
					<td class="text-right">${{ formatCurrency($cts['contract']->price) }}<br />(-${{ formatCurrency($cts['fee_rate']) }})</td>
					<td class="text-right">${{ formatCurrency($cts['amount']) }}<br />(-${{ formatCurrency($cts['fee_amount']) }})</td>
				</tr>
				@if ( $cts['total_manual'] )
				<tr>
					<td class="text-right">{{ trans('common.manual_time_included') }}</td>
					@for ($offset = 1; $offset <= 7; $offset++)
					<td width="7%" class="day text-center">
						{{ isset($cts['week_manual'][$offset]) && $cts['week_manual'][$offset] ? '(' . formatMinuteInterval($cts['week_manual'][$offset]) . ')' : '' }}
					</td>
					@endfor
					<td>({{ formatMinuteInterval($cts['total_manual']) }})</td>
					<td></td>
					<td class="text-right">(${{ formatCurrency($cts['contract']->buyerPrice($cts['total_manual'])) }})</td>
				</tr>
				@endif				
				@endforeach
				<tr>
					<td></td>
					<td colspan="7" class="day"></td>
					<td><strong>{{ formatMinuteInterval($total_work_in_progress['mins']) }}</strong></td>
					<td></td>
					<td class="text-right"><strong>${{ formatCurrency($total_work_in_progress['amount']) }}</strong></td>
				</tr>
			</tbody>
		</table>
	</div>
	@else
		<div class="not-found-result">
			<div class="heading">{{ trans('common.you_have_no_contracts') }}</div>
		</div>
	@endif	
</div><!-- .section -->

@if ( count($fixed_contracts) )
<div class="section section-fixed-price">
	<div class="section-title">
		{{ trans('report.fixed_price_milestones') }} <span class="sep">|</span> {{ trans('common.in_progress') }}
	</div>

	<div class="section-content">
		<table class="table">
			<thead>
				<tr>
					<th width="10%">{{ trans('common.assigned') }}</th>
					<th>{{ trans('common.job_milestone') }}</th>
					<th width="10%">{{ trans('common.amount') }}</th>
				</tr>
			</thead>
			<tbody>
			@foreach ( $fixed_contracts as $c )
				@if ( $c->milestones->count() )
					@foreach ( $c->milestones as $m )
						@if ( $m->isFunded() )
						<tr>
							<td>{{ date_format(date_create($m->start_time), 'M d, Y') }}</td>
							<td class="break">
								<a href="{{ _route('contract.contract_view', ['id' => $c->id]) }}">{{ $c->title }}</a>: {{ $m->name }}
							</td>
							<td>${{ formatCurrency($m->price) }}</td>
						</tr>
						@endif
					@endforeach
				@endif
			@endforeach
				<tr>
					<td colspan="4" class="text-right">
						<strong>${{ formatCurrency($total_fixed_milestones) }}</strong>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div><!-- .section -->
@endif