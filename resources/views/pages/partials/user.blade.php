<div class="user-avatar">
	<a href="{{ _route('user.profile', [$user->id]) }}" target="_blank"><img alt="{{ $user->fullname() }}" class="img-circle" src="{{ avatar_url($user) }}" width="100" height="100"></a>
</div>

<div class="user-info">
	<div class="row">
		<div class="col-md-9 col-sm-8">
			<h4 class="name">
				<a href="{{ _route('user.profile', [$user->id]) }}" target="_blank">{{ $user->fullname() }}</a>
			</h4>
			<div class="user-title break my-3">
				@if ( $user->contact->country )
					<img src="/assets/images/common/flags/{{ strtolower($user->contact->country->charcode) }}.png" data-toggle="tooltip" title="{{ $user->contact->country->name }}" class="flag mr-1">
				@endif
				{{ $user->profile->title }}
			</div>
		</div>

		<div class="col-md-3 col-sm-4">
			<div class="user-hourly-rate"><strong>{{ $currency_sign }}{{ number_format($user->profile->rate, 2, '.', ' ') }}</strong> / {{ trans('common.hr') }}</div>
		</div>	
	</div>

	<div class="row-1 row">
		<div class="col-md-4 col-xs-12">
			<div class="score" data-toggle="tooltip" title="{{ $user->stat->score }}">
				<div class="stars" data-value="{{ $user->stat ? $user->stat->score / 5 * 100:0 }}%"></div>
			</div>

			<div class="reviews">
				<strong>{{ $user->stat->total_reviews}}</strong>&nbsp;
				{{ trans('common.reviews') }}
			</div>
		</div>
		
		<div class="col-md-2 col-sm-6 col-xs-12">
			@if ( $user->stat->earning && !$user->profile->hide_earning )
			<div class="earned pt-1">
				<strong>{{ $currency_sign }}{{ formatEarned($user->stat->earning) }}</strong> 
				{{ trans('common.earned') }}
			</div>
			@endif
		</div>
		
		<div class="col-md-3 col-sm-6 col-xs-12">
			@if ( $user->stat->job_success )
			<div class="profile-success-percent">
                {{ trans('profile.success_percent', ['n' => $user->stat->job_success]) }}
                <div style="width: {{ $user->stat->job_success }}%;"></div>
            </div>
            @endif
		</div>
	</div>

	<div class="user-description">
		<p>{{ $user->shortDescription() }}</p>
	</div>

	@if ( count($user->skills) )
	<div class="user-skills mb-2">
		@for($i = 0; $i < count($user->skills); $i++)
			<span class="rounded-item{{ $i >= 4 ? ' hidden' : '' }}">
				{{ parse_multilang($user->skills[$i]->name, App::getLocale()) }}
			</span>
		@endfor
		@if ( count($user->skills) > 4 )
			<a class="more">{{ count($user->skills) - 4 }}&nbsp;{{ trans('common.more') }}</a>
		@endif

		<div class="clearfix"></div>
	</div>
	@endif

	@if ( $user->stat )
	<div class="row row-3">
		@if ( $user->stat->contracts )
		<div class="col-md-2 col-xs-6">
			<span>{{ trans('common.jobs_done') }}: </span>
			<strong>{{ $user->stat->contracts }}</strong>
		</div>
		@endif
		
		@if ( $user->stat->total_portfolios )
		<div class="col-md-2 col-xs-6">
			<span>{{ trans('common.portfolios') }}: </span>
			<strong>{{ $user->stat->total_portfolios }}</strong>
		</div>
		@endif
	</div>
	@endif
</div><!-- .user-info -->