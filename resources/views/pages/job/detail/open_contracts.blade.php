@if ( count($cur_contracts) == 0 && count($old_contracts) == 0 )
    {{ trans('common.no_history') }}
@endif

@if ( count($cur_contracts) )
<a href="#" class="job-in-progress"><span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>&nbsp;&nbsp;{{ trans('job.jobs_in_progress') }} </a>
<div id="jobs_in_progress" class="multi-freelancer-needed margin-bottom-30">
    @foreach ( $cur_contracts as $contract )
    <div class="contract-item">
        <div class="row">
            <div class="col-xs-10">
                @if ( $contract->project )
                    @if ( $contract->project->isOpen() && $contract->project->isPublic() )
                        <a class="name" href="{{ _route('job.view', ['id' => $contract->project_id]) }}">{{ $contract->project->subject }}</a>
                    @else
                        <label>{{ $contract->project->subject }}</label>
                    @endif
                @else
                    <label>{{ $contract->title }}</label>
                @endif
                <span class="label label-info">{{ trans('job.job_in_progress') }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8 col-md-8">
                <div class="freelancer-name">
                    {{ trans('job.to_freelancer') }}: 
                    @if ( $contract->contractor )
                        @if ( !$contract->contractor->isSuspended() && $contract->contractor->profile->share == 0 )
                            <a href="{{ _route('user.profile', ['uid' => $contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
                        @elseif ( !$contract->contractor->isSuspended() && $contract->contractor->profile->share == 1 && $current_user )
                            <a href="{{ _route('user.profile', ['uid' => $contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
                        @else
                            {{ $contract->contractor->fullname() }}
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-xs-4 col-md-4">
                <div class="started-date">{{ format_date('M d, Y', $contract->started_at) }} - {{ $contract->ended_at ? format_date('M d, Y', $contract->ended_at) : '' }}</div>
                <div>
                    <div class="hourly-rate"> 
                        @if ( $contract->isHourly() )
                            {{ isset($contract->meter->total_mins) ? intval($contract->meter->total_mins / 60) : 0 }} {{ trans('common.hours') }}
                            @ ${{ $contract->price }} / {{ trans('common.hr') }}
                        @endif
                    </div>
                    <div class="earned">
                        <p>
                            {!! trans('common.earned_x', ['amount' => isset($contract->meter->total_amount) ? $contract->meter->total_amount : 0]) !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div> 
@endif