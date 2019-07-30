<div class="client-info">
    <div class="sub-section">
        <div class="subtitle mb-3">{{ trans('job.about_the_client') }}</div>
        <div class="mb-2">
            @if ( $client->myBalance(false) > 0 )
            <div class="mb-4 payment-verified">
                <i class="icon-credit-card mr-2"></i>{{ trans('job.made_deposit') }}
            </div>
            @endif

            @if ( $client->stat )
                <div class="client-score">
                    <div class="stars" data-toggle="tooltip" title="{{ $client->stat->score }}" data-value="{{ $client->stat->score / 5 * 100 }}%"></div>
                    <div class="reviews">
                        {{ $client->stat->total_reviews }} {{ trans('common.reviews') }}
                    </div>
                    <div class="clearfix"></div>
                </div>
            @endif
        </div>

        <div class="sub-contents">{{ trans('common.member_since', ['time' => getFormattedDate($client->created_at)]) }}</div>
    </div>

    <div class="sub-section">
        <div class="subtitle"><img src="{{ asset('assets/images/common/flags/'.strtolower($client->contact->country_code).'.png') }}" />&nbsp;&nbsp;{{ $client->contact->country->name }}</div>
        <div class="sub-contents">
            {{ $client->contact->city ? $client->contact->city : '' }}
            @if ( $client->contact->timezone )
            {{ date_format(date_create('', timezone_open($client->contact->timezone->name)), 'h:i') }} {{ trans('common.' . date_format(date_create('', timezone_open($client->contact->timezone->name)), 'a')) }}
            @endif
        </div>
    </div>
    <div class="sub-section">
        <div class="subtitle">{{ trans('job.n_jobs_posted', ['n' => $client->stat->jobs_posted]) }}</div>
        <div class="sub-contents">
        	{{ trans('job.n_hire_rate', ['n' => $client->stat->hire_rate]) . ', ' . ($opened_job_count > $client->stat->jobs_posted ? $client->stat->jobs_posted : $opened_job_count) . ' ' . trans('common.open_jobs') }}
        </div>
    </div>

    @if ( $client->stat && $client->stat->total_spent > 0 )
    <div class="sub-section">
        <div class="subtitle">{{ $client->stat->total_spent_string() }}</div>
        <div class="sub-contents">{{ trans('job.n_hires', ['n' => $hired_count]) . ', ' . $active_count . ' ' . trans('common.active') }}</div>
    </div>
    @endif
    
    <!-- DONT'T USE THIS SECTION -->
    @if (false)
        @if ( $client->stat->avg_paid_rate > 0 )
        <div class="sub-section">
            <div class="subtitle">{{ trans('job.n_avg_hourly_paid', ['n' => $client->stat->avg_paid_rate]) }}</div>
            <div class="sub-contents">{{ $client->stat->total_paid_hrs }}&nbsp;{{ trans('common.hours') }}</div>
        </div>
        @endif
    @endif
</div>