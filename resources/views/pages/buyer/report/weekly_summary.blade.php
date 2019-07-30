<?php
    /**
    * Budget Page (report/weekly-summary)
    *
    * @author  - nada
    */

    use iJobDesk\Models\TransactionLocal;
?>
@extends('layouts/default/index')

@section('additional-css')
    <script src="{{ url('assets/plugins/amcharts/amcharts.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/plugins/amcharts/pie.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/plugins/amcharts/themes/light.js') }}" type="text/javascript"></script>
@endsection

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
<div class="page-content-section buyer-report-page report-weekly-summary-page">

    {{ show_messages() }}

    <div class="filter-section">
        <div class="row">
            <div class="col-md-6">
	            <div class="date-filter-section form-group">
	                <div class="date-filter">
	                    @if ($prev)
	                    <a class="btn btn-link prev-unit" data-from="{{ $prev }}"><i class="icon-arrow-left"></i></a>
	                    @endif
	                    <div class="input-group" id="date_range">
	                        @include("pages.snippet.daterange")
	                        <span class="input-group-btn">
	                            <button class="btn default date-range-toggle" type="button"><i class="fa icon-calendar"></i></button>
	                        </span>
	                    </div>
	                    @if ($next)
	                    <a class="btn btn-link next-unit" data-from="{{ $next }}"><i class="icon-arrow-right"></i></a>
	                    @endif

						@if ( $mode == 'current' )
	                    <a href="{{ route('report.weekly_summary', ['from' => $last_week_from]) }}" class="btn btn-border btn-primary pull-left ml-4">{{ trans('common.last_week') }}</a>
	                    	@if ( $is_in_review )
	                    	<span class="pull-left ml-3 mt-3 pt-1">{{ trans('common.in_review') }}</span>
	                    	@endif
	                    @else
	                    <a href="{{ route('report.weekly_summary') }}" class="btn btn-border btn-primary pull-left ml-4">{{ trans('common.this_week') }}</a>
	                    @endif

                        <div class="clearfix"></div>
	                </div><!-- .date-filter -->
	            </div><!-- .date-filter-section -->
	        </div>

	    	<div class="col-md-6 text-right">
                @if ($last_updated)
	    		<p>{{ trans('common.last_updated') }} {{ $last_updated }}</p>
	    		<p>{{ trans('report.all_dates_and_times_are_based_on_timezone', ['timezone' => $server_timezone_name]) }}</p>
                @endif
	    	</div>
	    </div>

	    <div class="row">
	    	<div class="col-md-4">
	    		<div class="title-week">{{ $week_title }}</div>
	    	</div>
	    </div>
    </div><!-- END OF .filter-section -->

    <div class="weekly-summary-section table-scrollable">
        <div class="summary-section section">
            <div class="section-content">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                    	<div class="infos">
                    		<table class="table">
                    			<thead>
                    				<tr>
                    					<th></th>
                    					<th>{{ trans('common.amount') }}</th>
                    				</tr>
                    			</thead>
                    			<tbody>
                    				<tr>
                    					<td>{{ trans('common.timesheet') }}</td>
                    					<td width="30%" class="text-right">
											{{ formatMinuteInterval($total['mins']) }} {{ trans('common.hrs') }}: 
											@if ($total['amount'] > 0)
											(${{ formatCurrency($total['amount']) }})
											@else
											${{ formatCurrency($total['amount']) }}
											@endif                    					
                    					</td>
                    				</tr>
                    				<tr>
                    					<td>{{ trans('report.fixed_price_and_other_payments') }}</td>
                    					<td>
											{{ $total['others'] < 0 ? '($' . formatCurrency(abs($total['others'])) . ')' : '$' . formatCurrency($total['others']) }}
                    					</td>
                    				</tr>
                    				<tr>
                    					<td>{{ trans('common.total') }}</td>
                    					<td>
											{{ $total['others'] - $total['amount'] < 0 ? '($' . formatCurrency(abs($total['others']-$total['amount'])) . ')' : '$' . formatCurrency($total['others'] - $total['amount']) }}
                    					</td>
                    				</tr>
                    			</tbody>
                    		</table>
	                    </div>
                    </div>

                    <div class="col-sm-6">
                        <div id="contract_amounts_data">
                            @foreach($contracts_amount as $contract)
                                <div class="contract_amount">
                                    <input type="hidden" class="contract_price" value="{{ $contract['amount'] }}"/>
                                    <input type="hidden" class="contract_title" value="{{ $contract['title'] }}"/>
                                </div>
                            @endforeach
                        </div><!-- #contract_amounts_data(hidden) -->
                        <div class="contract_label">
                            <p class="bg-info"></p>
                        </div>
                        <div id="contract_amount_chart" class="chart"></div>
                    </div>
                </div>
                
                <div class="note">
                    {{ trans('report.timesheet_description') }}
                </div>

            </div><!-- .section-content -->
        </div><!-- .summary-section -->

        <div class="timesheet-section section">
            <div class="section-title">
                <div class="row">
                	<div class="col-sm-4 col-xs-3">{{ trans('common.timesheet') }}</div>
                	<div class="col-sm-8 col-xs-9">
                		<div class="manual-alert">
							{!! trans('report.includes_n_hrs_manual_time', ['n' => formatMinuteInterval($total['manual'])]) !!}
						</div>
                	</div>
                </div>
            </div>
            
            <div class="section-content">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ trans('common.contract') }}</th>
                            @for($offset = 0; $offset < 7; $offset++)
                                <?php
                                    $one_date = date_add(date_create($dates['from']), date_interval_create_from_date_string("{$offset} days")); 
                                ?>
                                <th width="7%" class="day text-center{{ convertTz(date('Y-m-d H:i:s'), $user_timezone_name, $server_timezone_name, 'Y-m-d') == date_format($one_date, 'Y-m-d') ? ' today-label' : '' }}">
                                    <div>{{ trans('common.weekdays_abbr.' . date_format($one_date, 'N')) }}</div>
                                    <div>{{ date_format($one_date, $format_date) }}</div>
                                </th>
                            @endfor
                            <th width="7%">{{ trans('common.hours_cap') }}</th>
                            <th width="7%">{{ trans('common.rate') }}</th>
                            <!-- <th width="6%">{{ trans('common.manual_time') }}</th> -->
                            <th width="7%" class="text-right">{{ trans('common.amount') }}</th>
                            <th width="6%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @if ( !count($timesheets) )
                        <tr>
                        	<td colspan="12">
								<div class="not-found-result">
									<div class="heading">{{ trans('common.you_have_no_timelogs') }}</div>
								</div>
							</td>
						</tr>
                        @else
	                        @foreach ($timesheets as $cid => $cts)
    	                        <tr>
    	                            <td class="break">{{ $cts['contract']->contractor->fullname() .' - ' . $cts['contract']->title }}</td>
    	                            @for ($offset = 1; $offset <= 7; $offset++)
    	                            <td width="7%" class="day text-center">
    	                                {{ isset($cts['week'][$offset]) && $cts['week'][$offset]->mins > 0 ? formatMinuteInterval($cts['week'][$offset]->mins) : '-' }}
    	                            </td>
    	                            @endfor
    	                            <td>{{ $cts['mins'] > 0 ? formatMinuteInterval($cts['mins']) : '-' }}</td>
                                    <td>${{ formatCurrency($cts['contract']->price) }}/{{ trans('common.hr') }}</td>
                                    <!-- <td>
                                    @if ( $cts['total_manual'] && $cts['manual_time_allowed'] )
                                    	@if ( $mode == 'last' && $cts['contract']->isAvailableApproveManualTime() )
                                        <a class="btn-view-manual-time" data-json="{{ $cts['json'] }}">{{ trans('common.disallow') }}</a>
                                        @endif
                                    @endif
                                    </td> -->
    	                            <td class="text-right">${{ formatCurrency($cts['amount']) }}</td>
                                    <td>
                                    	@if ( $cts['contract']->isOpen() || $cts['contract']->isPaused() )
                                    	<a href="{{ _route('workdiary.view', ['cid' => $cts['contract']->id]) }}">{{ trans('report.work_diary') }}</a>
                                    	@endif
                                    </td>
    	                        </tr>
    	                        @if ( $cts['total_manual'] )
    	                        <tr>
    	                            <td class="text-right">{{ trans('common.manual_time_included') }}</td>
    	                            @for ($offset = 1; $offset <= 7; $offset++)
    	                            <td width="7%" class="text-center">
    	                                {{ isset($cts['week_manual'][$offset]) && $cts['week_manual'][$offset] ? '(' . formatMinuteInterval($cts['week_manual'][$offset]) . ')' : '' }}
    	                            </td>
    	                            @endfor
    	                            <td>({{ formatMinuteInterval($cts['total_manual']) }})</td>
                                    <td></td>
    	                            <td class="text-right">(${{ formatCurrency($cts['contract']->buyerPrice($cts['total_manual'])) }})</td>
                                    <td></td>
    	                        </tr>
    	                        @endif
	                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div><!-- .section-content -->
        </div><!-- .timesheet-section -->

        @if ( count($others) )
        <div class="fixed-other-section section">
            <div class="section-title">{{ trans('report.fixed_price_and_other_payments') }}</div>
            <div class="section-content table-scrollable">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="15%">{{ trans('common.freelancer') }}</th>
                            <th width="10%">{{ trans('common.date') }}</th>
                            <th width="15%">{{ trans('common.type') }}</th>
                            <th>{{ trans('common.description') }}</th>
                            <th width="15%" class="text-right">{{ trans('common.amount') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($others as $d)
                        <tr class="status-{{ strtolower($d->status_string()) }}">
                            <td>
                            @if ( in_array($d->type, [TransactionLocal::TYPE_AFFILIATE, TransactionLocal::TYPE_AFFILIATE_CHILD]) )
                                 - 
                            @else
                                {{ $d->contract ? $d->contract->contractor->fullname() : '' }}
                            @endif
                            </td>
                            <td>{{ format_date('M d, Y', $d->done_at) }}</td>
                            <td>{{ $d->type_string() }}</td>
                            <td class="break">
                            @if ( in_array($d->type, [TransactionLocal::TYPE_AFFILIATE, TransactionLocal::TYPE_AFFILIATE_CHILD]) )
                                 - 
                            @else
                                {!! $d->contract ? $d->contract->title . ($d->note ? ': ' . $d->note : '') : ($d->note ? $d->note : '') !!}
                            @endif
                            </td>
                            <td class="text-right">{{ $d->amount > 0 ? '$' . formatCurrency($d->amount) : '($' . formatCurrency(abs($d->amount)) . ')' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div><!-- .weekly-summary-section -->
</div>
@endsection