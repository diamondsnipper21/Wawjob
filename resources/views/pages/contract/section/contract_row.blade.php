<?php
/**
* @author Ro Un Nam
* @since Jun 02, 2017
*/

use iJobDesk\Models\Contract;
?>
<div class="box-row">
	<div class="col-md-5 subject">
		<a href="{{ _route('contract.contract_view', ['id' => $contract->id]) }}" class="main-cell break">{{ $contract->title }}</a>
		<div class="details">
			<div class="contractor">
			@if ( $current_user->isFreelancer() )
				<span>{{ $contract->buyer->fullname() }}</span>
				@if ( $contract->buyer->isSuspended() )
				<span class="suspended">
					<i class="fa fa-exclamation-circle"></i> {{ trans('common.suspended') }}
				</span>
				@elseif ( $contract->buyer->isFinancialSuspended() )
				<span class="suspended">
					<i class="fa fa-exclamation-circle"></i> {{ trans('common.financial_suspended') }}
				</span>
				@elseif ( $contract->buyer->isDeleted() )
				<span class="suspended">
					<i class="fa fa-exclamation-circle"></i> {{ trans('common.deleted') }}
				</span>
				@endif
			@else
				<span>{{ $contract->contractor->fullname() }}</span>
			@endif
			</div>

			@if ( $contract->isSuspended() || $contract->isPaused() )
			<div class="block">
				<span class="paused">
					<i class="fa fa-exclamation-circle"></i> 
					@if ( $contract->isSuspended() )
						{{ trans('common.contract_suspended') }}
					@else
						{{ trans('common.contract_on_hold') }}					
					@endif
				</span>

				@if ( !$contract->isSuspended() && $contract->isPaused() )
				<span class="paused-by">
					<i class="fa fa-exclamation-circle"></i>
					@if ( $contract->isPausedByiJobDesk() )
						{{ trans('common.this_contract_has_been_paused_by_ijobdesk') }}
					@else
						@if ( $current_user->isFreelancer() )
							{{ trans('common.this_contract_has_been_paused_by_client') }}
						@else
							{{ trans('common.this_contract_has_been_paused_by_you') }}
						@endif
					@endif
				</span>
				@endif
			</div>
			@endif			
		</div>
	</div>
	<div class="col-md-3 period">
		{{ format_date('M j, Y', $contract->created_at) }}
		<span> - </span>
		@if ( $contract->isClosed() )
			{{ format_date('M j, Y', $contract->ended_at) }}
		@else
			{{ trans('common.present') }}
		@endif
	</div>
	<div class="col-md-2 col-xs-7 terms">
		@if ( $contract->isHourly() )
			@if ( $contract->isNoLimit() )
				<div>
					{{ trans('common.no_limit') }}
				</div>
			@else
				<div>
					{{ trans('common.n_hours_week', ['n' => $contract->limit]) }}
				</div>
			@endif
			<div>
				{{ formatCurrency($contract->price, $currency_sign) }} / {{ trans('common.hour') }}
			</div>
		@else
			<div>
				{{ trans('common.fixed_price') }}
			</div>
			<div>
				{{ formatCurrency($contract->price, $currency_sign) }}
			</div>
		@endif
	</div>
	<div class="col-md-2 col-xs-5 text-right action">
		@if ( $contract->isOpen() && $contract->isHourly() )
		<a class="link" href="{{ _route('workdiary.view', ['cid'=>$contract->id]) }}">{{ trans('common.work_diary') }} <i class="fa fa-angle-right"></i></a>
		@endif
		@if ($contract->{strtolower($current_user->role_name()) . '_need_leave_feedback'} == 1 && $contract->canLeaveFeedback())
		<a href="{{ route('contract.feedback', ['id' => $contract->id]) }}" class="btn btn-primary btn-leave-feedback">{{ trans('common.leave_feedback') }} </a>
		@endif
	</div>
</div>