<?php
/**
* Report Timesheet Page (report/timesheet)
*/
?>
@extends($current_user->isAdmin() ? 'layouts/admin/super/user' : 'layouts/default/index')

@section('content')

<div class="title-section row">
    <div class="col-sm-6">
        <span class="title">
            <span>{{ trans('page.' . $page . '.title') }}</span>
            <span class="admin-title hide caption-subject font-green-sharp bold"><i class="fa icon-calendar font-green-sharp"></i>&nbsp;{{ trans('report.timesheet') }}</span>
        </span>
    </div>
    <div class="col-sm-6 report-mode-section clearfix">
        <div class="section-content pull-right" role="group">
            <div class="mode-item{{ $mode == 'd' ? ' active' : '' }}" data-mode="d">{{ trans('common.in_days') }}</div>
            <div class="mode-item{{ $mode == 'w' ? ' active' : '' }}" data-mode="w">{{ trans('common.in_weeks') }}</div>
            <div class="mode-item{{ $mode == 'm' ? ' active' : '' }}" data-mode="m">{{ trans('common.in_months') }}</div>
        </div>
    </div>
</div>
<div class="page-content-section {{ $current_user->isBuyer() ? 'buyer' : 'freelancer' }}-report-page report-timesheet-page">
    <div class="filter-section margin-bottom-30">
        <form id="frm_timesheet_filter" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            {{ show_messages() }}
            <div class="row">
                <div class="col-sm-6">
                    <div class="date-filter-section form-group">
                        <div class="date-filter">
                            <div class="input-group" id="date_range">
                                <input type="text" class="form-control" id="date_range_value" data-from="{{ $from }}" data-to="{{ $to }}" data-mode="{{ $mode }}" value="{{ date_format(date_create($dates['from']), $format_date2) . ' - ' . date_format(date_create($dates['to']), $format_date2) }}">
                                <span class="input-group-btn">
                                    <button class="btn default date-range-toggle" type="button"><i class="fa icon-calendar"></i></button>
                                </span>
                            </div>

                            <button type="submit" class="btn btn-primary btn-filter pull-left">{{ trans('common.go') }}</button>
                        </div>

                        <div class="clearfix"></div>
                    </div><!-- .date-filter-section -->
                </div>
                @if ( count($contracts) )
                <div class="col-sm-6">
                    <div class="contract-filter-section">						
						<select class="contract-filter form-control select2" id="contract_selector" name="contract_id" data-required="1" aria-required="true">
						    <option value="0" {{ $contract_id == 0 ? 'selected' : ''  }}>{{ $current_user->isFreelancer() ? trans('common.all_contracts') : trans('common.all_freelancers') }}</option>
						    @foreach($contracts as $c)
						    <option value="{{ $c->id }}" {{ $contract_id == $c->id ? 'selected' : '' }}>{{ ($current_user->isFreelancer() ? $c->buyer->fullname() : $c->contractor->fullname()) . ' - ' . $c->title }}</option>
						    @endforeach
						</select>
                    </div>
                </div>
                @endif
            </div>
        </form>
    </div><!-- END OF .filter-section -->

    <div class="timesheet-section table-scrollable">
        <table class="table">
            <thead>
                <tr>
                    <th width="20%">{{ trans('common.date') }}</th>
                    <th width="20%">{{ trans('common.freelancer') }}</th>
                    <th>{{ trans('common.contract') }}</th>
                    <th width="15%">{{ trans('common.hours_cap') }}</th>
                    <th width="15%" class="text-right">{{ trans('common.amount') }}</th>
                </tr>
            </thead>
            <tbody>

                @if (empty($r_data))
                    <tr>
                        <td colspan="5">
                            <div class="not-found-result">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div class="heading">{{ trans('common.no_data_found') }}</div>
                                    </div>
                                </div>
                            </div>                                
                        </td>
                    </tr>
                @else
                    @foreach ($r_data as $d => $timesheet)
                        @foreach ( $timesheet as $k => $ts )
                        <tr>
                            <td>{{ $k == 0 ? $d : '' }}</td>
                            <td>{{ $ts['user'] }}</td>
                            <td>{{ $ts['description'] }}</td>
                            <td>{{ formatMinuteInterval($ts['mins']) }}</td>
                            <td class="text-right">${{ formatCurrency($ts['amount']) }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td colspan="3">{{ trans('common.total') }}</td>
                        <td>
                            <strong>{{ formatMinuteInterval($total['mins']) }}</strong>
                        </td>
                        <td class="text-right">
                            <strong>${{ formatCurrency($total['amount']) }}</strong>
                        </td>
                    </tr>
                @endif

            </tbody>
        </table>
    </div><!-- END OF .timesheet-section -->

    <div class="row">
        <div class="col-xs-6">
            {{ trans('common.last_updated') }} {{ $last_updated }}
        </div>
        <div class="col-xs-6 text-right">
            {{ trans('report.all_dates_and_times_are_based_on_timezone', ['timezone' => $server_timezone_name]) }}
        </div>
    </div>
</div><!-- .page-content-section -->

@endsection