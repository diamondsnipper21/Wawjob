<?php

use iJobDesk\Models\Contract;

?>
<div class="row">
	<div class="col-sm-8 content pt-4">
		{{ show_warnings() }}
		
		<div class="title-section">
			<i class="hs-admin-server title-icon"></i>
			<span class="title">{{ trans('page.frontend.dashboard.title') }}</span>
			<a href="{{ route('job.create') }}" class="btn btn-primary {{ !$current_user->isAvailableAction() ? 'disabled' : '' }}">{{ trans('common.post_job') }}</a>
		</div>

		<div class="boxes clearfix">
			<!-- Job Postings -->
			<div class="box job-postings">
				<div class="box-icon"><i class="icon-docs"></i></div>
				<div class="box-content">
					<div class="box-value">{{ $job_postings }}</div>
					<div class="box-title">{{ trans('common.job_postings') }}</div>
				</div>
				<div class="clearfix"></div>
				<div class="box-bottom">
					<a href="{{ route('job.all_jobs') }}#job_postings">{{ trans('home.dashboard.view_job_postings') }}</a>
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

			<!-- Offer Sent -->
			<div class="box offer-sent">
				<div class="box-icon"><i class="icon-envelope-open"></i></div>
				<div class="box-content">
					<div class="box-value">{{ $offer_sents }}</div>
					<div class="box-title">{{ trans('common.offers_sent') }}</div>
				</div>
				<div class="clearfix"></div>
				<div class="box-bottom">
					<a href="{{ route('job.all_jobs') }}#offers">{{ trans('home.dashboard.view_offers') }}</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-4 content">
		<div class="profile">
			<div class="profile-image">
				<img class="img-circle avatar img-responsive" src="{{ avatar_url($current_user) }}" />
			</div>
			<div class="profile-info">
				<div class="profile-title">{{ trans('home.dashboard.welcome_back') }},</div>
				<div class="profile-name break">{{ $current_user->fullname() }}</div>
				<div class="profile-country"><i class="icon-location-pin mr-2"></i>{{ $current_user->contact->city ? $current_user->contact->city . ', ' : ''}}{{ $current_user->contact->country->name }}</div>
				<div class="profile-timezone"><i class="icon-clock mr-2"></i>{{ format_date($format_time, date('Y-m-d H:i:s')) }}</div>
				<div class="profile-score">
					<div class="stars" title="{{ number_format($current_user->stat->score, 1) }}" data-toggle="tooltip" data-value="{{ $current_user->stat->score / 5 * 100 }}%"></div>
					<div class="profile-reviews">
						<strong>{{ $current_user->stat->total_reviews}}</strong>&nbsp;
						{{ trans('common.reviews') }}
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="profile-action clearfix">
				<div class="row mb-3 funds">
					<div class="col-xs-6"><strong>{{ trans('home.dashboard.my_funds') }}</strong></div>
					<div class="col-xs-6 text-right">
						<a href="{{ route('report.transactions') }}">
						@if ( $balance >= 0 )
							${{ formatCurrency($balance) }}
						@else
							(${{ formatCurrency(abs($balance)) }})
						@endif
						</a>
					</div>
				</div>
				@if ( $holding_amount > 0 )
				<div class="holding pb-3">{{ trans('common.in_holdng_now', ['amount' => '$' . formatCurrency($holding_amount)]) }}  <i class="icon icon-question ml-2" data-toggle="tooltip" title="{{ trans('common.in_holding_now_reason') }}"></i></div>
				@endif
				<a href="{{ route('user.deposit') }}" class="btn btn-primary pull-right">{{ trans('home.dashboard.deposit_funds') }}</a>
			</div>
		</div>
	</div>
</div>