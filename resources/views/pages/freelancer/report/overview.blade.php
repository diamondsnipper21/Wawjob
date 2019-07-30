<?php
/**
 * Report Overview Page (report/overview)
 *
 * @author Ro Un Nam
 * @since Jun 07, 2017
 */
?>
@extends('layouts/default/index')

@section('additional-css')
<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css') }}">
@endsection

@section('content')

<script type="text/javascript">
@if (isset($dates))
	var date_from = '{{ date("Y-m-d", strtotime($dates['from'])) }}';
	var date_to   = '{{ date("Y-m-d", strtotime($dates['to'])) }}';
@endif
</script>

<div class="page-content-section no-padding">
	<div class="view-section freelancer-report-page report-overview-page">
		<div class="title-section">
			<span class="title">{{ trans('page.freelancer.report.overview.title') }}</span>
		</div>

		{{ show_messages() }}

		<div class="tab-section">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#work_in_progress" aria-controls="work_in_progress" role="tab" data-toggle="tab">{{ trans('common.work_in_progress') }}
						<span class="amount">${{ formatCurrency($total_work_in_progress['amount'] + $total_fixed_milestones) }}</span>
					</a>
				</li>    
				<li role="presentation">
					<a href="#in_review" aria-controls="in_review" role="tab" data-toggle="tab">{{ trans('common.in_review') }}
						<span class="amount">${{ formatCurrency($total_in_review) }}</span>
					</a>
				</li>
				<li role="presentation">
					<a href="#pending" aria-controls="pending" role="tab" data-toggle="tab">{{ trans('common.pending') }}
						<span class="amount">
							${{ formatCurrency($total_pending) }}
						</span>
					</a>
				</li>
				<li role="presentation">
					<a href="#available" aria-controls="available" role="tab" data-toggle="tab">{{ trans('common.available') }}
						<span class="amount">${{ formatCurrency($balance) }}</span>
						<span class="info-display">{{ trans('common.last_payment') }}: ${{ formatCurrency($total_last_payment_amount) }}</span>
					</a>
				</li>
			</ul>
		</div><!-- .tab-section -->

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="work_in_progress">
			@include ('pages.freelancer.report.section.overview_work_in_progress')
			</div><!-- #work_in_progress -->

			<div role="tabpanel" class="tab-pane" id="in_review">
			@include ('pages.freelancer.report.section.overview_in_review')
			</div><!-- #in_review -->

			<div role="tabpanel" class="tab-pane" id="pending">
			@include ('pages.freelancer.report.section.overview_pending')
			</div><!-- #pending -->

			<div role="tabpanel" class="tab-pane" id="available">
			@include ('pages.freelancer.report.section.overview_available')
			</div><!-- #available -->

		</div><!-- .tab-content -->

		<div class="note">
			{{ trans('common.note') }}: {{ trans('report.this_report_is_updated_every_hour') }}
		</div>

	</div><!-- .view-section -->

</div><!-- .page-content-section -->
@endsection