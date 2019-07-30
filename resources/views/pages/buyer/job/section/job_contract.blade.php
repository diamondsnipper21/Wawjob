<?php
/**
* @author Ro Un Nam
* @since May 30, 2017
*/
use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\User;
?>
<div class="box-row" data-id="{{ $contract->id }}" data-status="{{ $contract->status }}">

	@if ( $contract->isOpen() || $contract->isPaused() || $contract->isSuspended() || $contract->isClosed() )
	<div class="contract-title">
		<a href="{{ _route('contract.contract_view', ['id' => $contract->id]) }}" class="main-cell">{{ $contract->title }}</a>
	</div>
	@endif

	<div class="row">
		<div class="col-sm-7 col-xs-8">
			<a href="{{ _route('user.profile', [$contract->contractor->id]) }}"><img alt="{{ $contract->contractor->fullname() }}" class="img-circle pull-left" src="{{ avatar_url($contract->contractor) }}" width="50" height="50"></a>

			<div class="user-info">
				<div class="user-name">
					@if ( $contract->contractor->isSuspended() )
						{{ $contract->contractor->fullname() }}
						<span class="suspended">
							<i class="fa fa-exclamation-circle"></i> {{ trans('common.suspended') }}
						</span>
					@else
						<a href="{{ _route('user.profile', [$contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
					@endif
				</div>
				<div class="user-title">{{ $contract->contractor->profile->title }}</div>
			</div>
		</div>

		<div class="col-sm-5 col-xs-4 text-right">
			<div class="rate mt-2">
				<label>
					{{ formatCurrency($contract->price, $currency_sign) }}
					@if ( $contract->type == '1' )
						 / {{ trans('common.hr') }}
					@endif
				</label>

				@if ( $contract->isOffer() )
					<span class="round-ribbon label-sent">{{ trans('common.sent') }}</span>
				@elseif ( $contract->isOpen() )
					<span class="round-ribbon label-open">{{ trans('common.hired') }}</span>
				@elseif ( $contract->isPaused() )
					<span class="round-ribbon label-paused">{{ trans('common.contract') }} {{ trans('common.paused') }}</span>
				@elseif ( $contract->isSuspended() )
					<span class="round-ribbon label-suspended">{{ trans('common.suspended') }}</span>
				@elseif ( $contract->isRejected() )
					<span class="round-ribbon label-rejected" data-toggle="tooltip" title="{{ $contract->closed_reason }}">{{ trans('common.declined') }}</span>
				@elseif ( $contract->isWithdrawn() )
					<span class="round-ribbon label-withdraw" data-toggle="tooltip" title="{{ $contract->closed_reason }}">{{ trans('common.withdrawn') }}</span>
				@elseif ( $contract->isClosed() )
					<span class="round-ribbon label-closed">{{ trans('common.completed') }}</span>
				@endif
			</div>

			@if ( $contract->isOffer() )
				<div class="date">
					<span>{{ getFormattedDate($contract->created_at, $format_date2) }} {{ getFormattedDate($contract->created_at, $format_time) }}</span>
				</div>

				@if ( !$current_user->isAdmin() )
				<div class="user-action">
					<button type="button" class="btn btn-border btn-danger btn-withdraw {{ $current_user->isSuspended() || $job->status == Project::STATUS_SUSPENDED ? 'disabled' : '' }}">{{ trans('common.withdraw_offer') }}</button>

					@if ( !$current_user->isSuspended() && $job->status != Project::STATUS_SUSPENDED )
					<div class="box-withdraw">
						<button type="button" class="close">&times;</button>
						<div class="box-title">
							{{ trans('common.withdraw_offer') }}
						</div>
						<div class="box-message">
							<form class="form-horizontal form-withdraw" action="{{ route('job.withdraw_offer.ajax') }}" method="post">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="id" value="{{ $contract->id }}">
			  					<div class="box-ctrl">
									<label>{{ trans('common.reason') }}</label>
									<div class="radiobox">
										<label>
											<input type="radio" name="reason" value="1" checked="checked">
											{{ trans('common.mistake') }}
										</label>
									</div>
									<div class="radiobox">
										<label>
											<input type="radio" name="reason" value="2">
											{{ trans('job.reason_hired_another_freelancer') }}
										</label>
									</div>
									<div class="radiobox">
										<label>
											<input type="radio" name="reason" value="3">
											{{ trans('job.reason_irresponsive_freelancer') }}
										</label>
									</div>
									<div class="radiobox">
										<label>
											<input type="radio" name="reason" value="4">
											{{ trans('common.other') }}
										</label>
									</div>
								</div>
								<div class="box-ctrl margin-bottom-20">
									<label>{{ trans('common.message') }} (<span>{{ trans('common.optional') }}</span>)</label>
									<textarea name="message" class="form-control maxlength-handler" maxlength="{{ Contract::WITHDRAW_MESSAGE_MAX_LENGTH }}"></textarea>
								</div>
								<button type="button" class="btn btn-primary btn-submit-withdraw">{{ trans('common.withdraw') }}</button>
								<a class="btn btn-link btn-cancel-withdraw">{{ trans('common.cancel') }}</a>
							</form>
						</div>
					</div><!-- .box-withdraw -->
					@endif
				</div><!-- .user-action -->
				@endif
			@else
				<div class="date">
					<span>{{ getFormattedDate($contract->started_at, $format_date2) }} {{ getFormattedDate($contract->started_at, $format_time) }}</span>
				</div>
			@endif
		</div><!-- .col-md-3 -->
	</div><!-- .row -->

</div><!-- .box-row -->