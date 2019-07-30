<div class="contract-item">
    <div class="row heading">
        <div class="col-xs-12">
            @if ( $contract->project )
                @if ( $contract->project->isOpen() && $contract->project->isPublic() )
                <a class="name" href="{{ _route('job.view', ['id' => $contract->project_id]) }}">
                    {{ $contract->project->subject }}
                </a>
                @else
                <label class="name">{{ $contract->project->subject }}</label>
                @endif
            @else
                <label class="name">{{ $contract->title }}</label>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8 col-md-8">
        @if ( $contract->feedback )
            <div class="client-review margin-top-10">
                <div class="client-score">
                    <div class="stars" data-toggle="tooltip" title="{{ number_format($contract->feedback->freelancer_score, 1) }}" data-value="{{ $contract->feedback->freelancer_score / 5 * 100 }}%"></div>
                </div>

                <div class="client-review-desc">
                    @if ( $contract->feedback->is_freelancer_feedback_public && !empty($contract->feedback->freelancer_feedback) )
                        <div class="blockquote">"{{ $contract->feedback->freelancer_feedback }}"</div>
                        <div class="client-reviewer">
                            - 
                            @if ( !$contract->contractor->isSuspended() )
                                <a href="{{ _route('user.profile', ['uid' => $contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
                            @else
                                {{ $contract->contractor->fullname() }}
                            @endif

                            @if ( empty($contract->feedback->buyer_feedback) )
                            <div class="not-give-feedback freelancer mt-0 gray-text-color">{{ trans('job.not_given_feedback_to_freelancer') }}</div>
                            @else
                            <a href="#" class="show-buyer-feedback">{{ trans('common.feedback_to_the_freelancer') }}&nbsp;&nbsp;<i class="icon-arrow-down"></i></a>
                            @endif
                        </div>
                    @elseif ( empty($contract->feedback->freelancer_feedback) )
                        <div class="not-give-feedback">{{ trans('job.not_given_feedback') }}</div>
                        <a href="#" class="show-buyer-feedback">{{ trans('common.feedback_to_the_freelancer') }}&nbsp;&nbsp;<i class="icon-arrow-down"></i></span>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>

            @if ( !empty($contract->feedback->buyer_feedback) )
            <div class="freelancer-review client-review margin-top-10 clearfix">
                <div class="freelancer-name">
                    <span>{{ trans('common.to') }} : </span>
                    @if ( !$contract->contractor->isSuspended() )
                        <a href="{{ _route('user.profile', ['uid' => $contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
                    @else
                        {{ $contract->contractor->fullname() }}
                    @endif
                </div>
                <div class="client-score">
                    <div class="stars" data-toggle="tooltip" title="{{ number_format($contract->feedback->buyer_score, 1) }}" data-value="{{ $contract->feedback->buyer_score / 5 * 100 }}%"></div>
                </div>
                <div class="clearfix"></div>
                <div class="client-review-desc">
                    @if ( $contract->feedback->is_buyer_feedback_public && !empty($contract->feedback->buyer_feedback) )
                        <div class="blockquote">"{{ $contract->feedback->buyer_feedback }}"</div>
                    @elseif ( empty($contract->feedback->buyer_feedback) )
                        <div class="not-give-feedback">{{ trans('job.not_given_feedback') }}</div>
                    @endif
                </div>
                <div class="buyer-name text-left">
                    - {{ $contract->buyer->fullname() }}, {{ trans('job.the_client') }}
                </div>
            </div>
            @endif
        @else
            <div class="not-give-feedback">
            {{ trans('job.not_given_feedback') }}
            </div>
        @endif
        </div>
        <div class="col-xs-4 col-md-4">
            <div class="started-date">{{ format_date('M d, Y', $contract->started_at) }} - {{ format_date('M d, Y', $contract->ended_at) }}</div>
            <div class="hourly-rate">
                @if ( $contract->isHourly() )
                    {{ isset($contract->meter->total_mins) ? intval($contract->meter->total_mins / 60) : 0 }} {{ trans('common.hours') }}
                    @ ${{ $contract->price }} / {{ trans('common.hr') }}
                @endif
            </div>
            <div class="earned">
                {{ trans('common.billed') }}: <strong>${{ isset($contract->meter->total_amount) ? $contract->meter->total_amount : 0 }}</strong>
            </div>
        </div>
    </div>
</div>