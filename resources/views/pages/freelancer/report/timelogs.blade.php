<?php
/**
 * Timelogs Page (report/timelogs)
 *
 * @author Ro Un Nam
 * @since Jun 09, 2017
 */

use iJobDesk\Models\TransactionLocal;
?>
@extends('layouts/default/index')

@section('content')

<script type="text/javascript">
@if (isset($dates))
	var date_from = '{{ date("Y-m-d", strtotime($dates['from'])) }}';
	var date_to   = '{{ date("Y-m-d", strtotime($dates['to'])) }}';
@endif
</script>

<div class="title-section">
	<span class="title">{{ trans('page.' . $page . '.title') }}</span>
</div>

<div class="page-content-section freelancer-report-page report-timelogs-page">

	<div class="filter-section">
		<div class="row">
			<div class="col-md-6 col-sm-8">
				<div class="date-filter-section clearfix">
					<span class="pull-left">{{ trans('common.week_of') }}</span>
					<div class="date-filter pull-left">
						@if ($prev)
						<a class="btn btn-link prev-unit" data-from="{{ $prev }}"><i class="icon-arrow-left"></i></a>
						@endif
						<div class="input-group" id="date_range">
							<input type="text" class="form-control" id="date_range_value" data-from="{{ $from }}" data-to="{{ $to }}" value="{{ date($format_date2, strtotime($from)) . ' - ' . date($format_date2, strtotime($to)) }}">
							<span class="input-group-btn">
								<button class="btn default date-range-toggle" type="button"><i class="fa icon-calendar"></i></button>
							</span>
						</div>
						@if ($next)
						<a class="btn btn-link next-unit" data-from="{{ $next }}"><i class="icon-arrow-right"></i></a>
						@endif
					</div>
				</div>
			</div>

			<div class="col-md-6 col-sm-4">
				<div class="info">
					{{ trans('report.all_dates_and_times_are_based_on_timezone', ['timezone' => $server_timezone_name]) }}
				</div>
			</div>
		</div>
	</div><!-- .filter-section -->

	@include ('pages.freelancer.report.section.timelogs_summary')

	@include ('pages.freelancer.report.section.timelogs_timesheet')

</div>
@endsection