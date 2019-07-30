<?php

use iJobDesk\Models\Contract;

?>
<div class="offer-terms">
@if ($contract->type == Contract::TYPE_HOURLY)
    <h4 class="block"><i class="fa fa-list"></i> Job Terms</h4>
    <div class="well">
         <div class="term-section margin-bottom-20">
            <div class="term-label">Hourly Rate:</div>
            <div class="term-data">{{ $contract->price }} $ / hr</div>
            <div class="clearfix"></div>
        </div><!-- .term-section -->
        <div class="term-section margin-bottom-20">
            <div class="term-label">Weekly Limit:</div>
            <div class="term-data">
                {{ ($contract->limit == 0?trans('common.no_limit'):trans('common.n_hours_week', ['n' => $contract->limit])) }}
            </div>
            <div class="clearfix"></div>
        </div><!-- .term-section -->
        <div class="term-section margin-bottom-20">
            <div class="term-label">Manual Time:</div>
            <div class="term-data">
                {{ $contract->is_allowed_manual_time ? trans('common.allowed') : trans('common.not_allowed') }}
            </div>
            <div class="clearfix"></div>
        </div><!-- .term-section -->
    </div>
@else
    <h4 class="block"><i class="fa fa-list"></i> {{ trans('common.milestones') }}</h4>
    <div class="well">
        <table class="table table-striped table-bordered table-advance table-hover text-center margin-bottom-30">
            <thead>
                <tr>
                    <th>{{ trans('common.milestone') }}</th>
                    <th width="15%">{{ trans('common.start_date') }}</th>
                    <th width="15%">{{ trans('common.end_date') }}</th>
                    <th width="15%">{{ trans('common.status') }}</th>
                    <th width="15%" class="text-right">{{ trans('common.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contract->milestones as $milestone)
                    <tr>
                        <td class="text-left">
                            <span>{{ $milestone->name }}</span>
                        </td>
                        <td class="text-left">
                            <span>{{ format_date(null, $milestone->start_time) }}</span>
                        </td>
                        <td class="text-left">
                            <span>{{ format_date(null, $milestone->end_time) }}</span>
                        </td>
                        <td class="text-left">
                            <span>{{ $milestone->fund_status_string() }}</span>
                        </td>
                        <td class="text-right" style="text-align: right">
                            <span>{{ '$'.formatCurrency(abs($milestone->price)) }}</span>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" style="text-align: right">
                        {{ trans('common.total') }}: ${{ formatCurrency($milestone_total_price) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endif
</div>