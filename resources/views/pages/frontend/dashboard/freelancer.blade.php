<?php

use iJobDesk\Models\Contract;

?>
<div class="row">
	<div class="col-sm-8 content pt-4">
		{{ show_warnings() }}

		<div class="title-section">
			<i class="hs-admin-server title-icon"></i>
			<span class="title">{{ trans('page.frontend.dashboard.title') }}</span>
		</div>

		<div class="boxes clearfix">
			<!-- Offers -->
			<div class="box offer-sent">
				<div class="box-icon"><i class="icon-envelope-open"></i></div>
				<div class="box-content">
					<div class="box-value">{{ $job_offers }}</div>
					<div class="box-title">{{ trans('common.job_offers') }}</div>
				</div>
				<div class="clearfix"></div>
				<div class="box-bottom">
					<a href="{{ route('job.my_proposals') }}">{{ trans('home.dashboard.view_offers') }}</a>
				</div>
			</div>

			<!-- Proposals -->
			<div class="box job-postings">
				<div class="box-icon"><i class="icon-briefcase"></i></div>
				<div class="box-content">
					<div class="box-value">{{ $proposals }}</div>
					<div class="box-title">{{ trans('common.proposals') }}</div>
				</div>
				<div class="clearfix"></div>
				<div class="box-bottom">
					<a href="{{ route('job.my_proposals') }}">{{ trans('home.dashboard.view_proposals') }}</a>
				</div>
			</div>

			<!-- Contracts -->
			<div class="box contracts">
				<div class="box-icon"><i class="icon-layers"></i></div>
				<div class="box-content">
					<div class="box-value">{{ $contracts }}</div>
					<div class="box-title">{{ trans('common.contracts') }}</div>
				</div>
				<div class="clearfix"></div>
				<div class="box-bottom">
					<a href="{{ route('contract.all_contracts') }}">{{ trans('home.dashboard.view_contracts') }}</a>
				</div>
			</div>
		</div>

		<div class="title-section title-blank"></div>
		<div class="pt-3 pb-3">
			<i class="icon-clock mr-2"></i><a href="{{ route('frontend.download_tools') }}">{{ trans('home.dashboard.track_time') }}</a>
		</div>
	</div>

	<div class="col-sm-4 content">
		<div class="profile">
			<div class="profile-image">
				<img class="img-circle avatar img-responsive" src="{{ avatar_url($current_user) }}" />
				<div class="profile-hourly-rate">${{ $current_user->profile->rate }}/hr</div>
			</div>
			<div class="profile-info">
				<div class="profile-title">{{ trans('home.dashboard.welcome_back') }},</div>
				<div class="profile-name break">{{ $current_user->fullname() }}</div>
				<div class="profile-country"><i class="icon-location-pin mr-2"></i>{{ $current_user->contact->city ? $current_user->contact->city . ', ' : ''}}{{ $current_user->contact->country->name }}</div>
				<div class="profile-timezone"><i class="icon-clock mr-2"></i>{{ format_date($format_time, date('Y-m-d H:i:s')) }}</div>
				<div class="profile-score clearfix">
					<div class="stars" title="{{ number_format($current_user->stat->score, 1) }}" data-toggle="tooltip" data-value="{{ $current_user->stat->score / 5 * 100 }}%"></div>
					<div class="profile-reviews">
						<strong>{{ $current_user->stat->total_reviews}}</strong>&nbsp;
						{{ trans('common.reviews') }}
					</div>
				</div>

                <div class="profile-success-percent">
                    {{ trans('profile.success_percent', ['n' => $current_user->stat->job_success]) }}
                    <div style="width: {{ $current_user->stat->job_success }}%;"></div>
                </div>
			</div>
			<div class="clearfix"></div>
			<div class="profile-action clearfix">
				<div class="row mb-3 funds">
					<div class="col-xs-6"><strong>{{ trans('home.dashboard.my_funds') }}</strong></div>
					<div class="col-xs-6 text-right balance">
						<a href="{{ route('report.transactions') }}">
						@if ( $balance >= 0 )
							${{ formatCurrency($balance) }}
						@else
							(${{ formatCurrency(abs($balance)) }})
						@endif
						</a>
					</div>
				</div>
				@if ( $balance > 0 )
				<a href="{{ route('user.withdraw') }}" class="btn btn-primary pull-right">{{ trans('common.withdraw') }}</a>
				@else
				<div>&nbsp;</div>
				@endif
			</div>
		</div>
	</div>
</div>