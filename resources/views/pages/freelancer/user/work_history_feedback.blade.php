@forelse ($contracts as $contract)
    <div class="content">
        <div class="row">
            <div class="col-sm-8 col-xs-6">
                <ul class="items">
                    <li class="title break">
                        {{ $contract->title }}
                        @if ( $contract->isOpen() )
                        <span class="round-ribbon label-open">{{ trans('job.job_in_progress') }}</span>
                        @endif
                    </li>
                    @if ( $contract->feedback && $contract->feedback->buyer_feedback )
                    <li class="feedback">
                        <div class="stars" data-toggle="tooltip" title="{{ number_format($contract->feedback->buyer_score, 1) }}" data-value="{{ $contract->feedback->buyer_score / 5 * 100 }}%"></div>
                    </li>
                    <li class="desc">
                        "{{ $contract->feedback->buyer_feedback }}"
                        <div>- {{ $contract->buyer->fullname() }}</div>
                    </li>
                    @else
                    <li class="feedback not-give">{{ trans('contract.no_feedback_given') }}</li>
                    @endif
                </ul>
            </div>
            <div class="col-sm-4 col-xs-6 text-right {{ !$contract->isOpen()?'mt-5':'' }}">
                <ul class="items">
                    <li class="date">{{ format_date('M d, Y', $contract->started_at) }} - {{ $contract->ended_at?format_date('M d, Y', $contract->ended_at):trans('common.present') }}
                    </li>

                    <li class="rate"> 
                        <span>
                        @if ( !$user->profile->hide_earning || ($current_user && $current_user->canSeeUserEarning($user)) )
                            @if ( $contract->isHourly() )
                                <strong>{{ isset($contract->meter->total_mins) ? intval($contract->meter->total_mins / 60) : 0 }}</strong> {{ trans('common.hours') }}
                                <br>
                                @ ${{ $contract->price }} / {{ trans('common.hr') }}
                            @else
                                {!! trans('common.earned_x', ['amount' => isset($contract->meter->total_amount) ? $contract->meter->total_amount : 0]) !!}
                            @endif
                        @else
                            @if ( $contract->isHourly() )
                                {{ trans('common.hourly') }}
                                <br>
                                @ ${{ $contract->price }} / {{ trans('common.hr') }}
                            @else
                                {{ trans('common.fixed_price') }}
                            @endif
                        @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@empty
    <div class="not-found-result text-center">{{ trans('profile.message.No_Found_Work_History_and_Feedback') }}</div>
@endforelse

<div class="row margin-top-10">
    <div class="col-md-4 col-sm-3">
    </div>
    <div class="col-md-8 col-sm-9">
        <div class="datatable-paginate pull-right">{!! $contracts->render() !!}</div>
    </div>
</div>