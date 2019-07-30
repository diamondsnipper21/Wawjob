<?php
/**
 * Contract Detail Page (contract/{id})
 *
 * @author Ro Un Nam
 * @since Jun 03, 2017
 */

use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\Ticket;

?>

@extends($current_user->isAdmin()?(!empty($user_id)?'layouts/admin/'.$role_id.'/user':'layouts/admin/'.$role_id.''):'layouts/default/index')

@section('additional-css')
	@if ( $current_user->isBuyer() )
	<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap-datepicker/css/datepicker3.css') }}">
	@endif

	@if ($current_user->isAdmin())
		@if ($user_id && User::find($user_id)->isBuyer() )
	    <link rel="stylesheet" href="{{ url('assets/styles/layouts/freelancer/freelancer.css') }}">
	    @else
	    <link rel="stylesheet" href="{{ url('assets/styles/layouts/buyer/buyer.css') }}">
	    @endif
	@endif
@endsection

@section('content')

{!! Breadcrumbs::render('contract_detail', $contract) !!}

<div class="page-content-section shadow-box">
	<div class="view-section job-content-section">
		{{ show_warnings() }}
		
		{{ show_messages() }}
		<div class="title-section">
			<div class="row">
				<div class="col-md-9">
					<div class="title break">
						{{ $contract->title }}
						@if ( $contract->isSuspended() )
							@if ( $ticket && $ticket->isDispute() )
								<span class="contract-status status-suspended" data-toggle="tooltip" title="{{ ($ticket->user_id == $current_user->id) ? trans('contract.contract_has_been_suspended_by_you') : ($current_user->isBuyer() ? trans('contract.contract_has_been_suspended_by_freelancer') : trans('contract.contract_has_been_suspended_by_client')) }}">{{ trans('common.suspended') }}</span>

								@if ($current_user->isSuper())
								&nbsp;&nbsp;&nbsp;
								<a href="{{ route('admin.super.ticket.detail', ['id' => $ticket->id]) }}" class="view-related-ticket">{{ trans('contract.view_related_ticket') }}</a>
								@endif
							<!-- Contract Suspension by account suspension -->
							@elseif ($contract->buyer->isSuspended() || $contract->contractor->isSuspended())
								<span class="contract-status status-suspended" data-toggle="tooltip" title="{{ ($contract->buyer_id == $current_user->id && $contract->buyer->isSuspended()) || ($contract->contractor_id == $current_user->id && $contract->contractor->isSuspended()) ? trans('contract.contract_has_been_suspended_by_user_suspension_you') : ($contract->buyer->isSuspended() ? trans('contract.contract_has_been_suspended_by_user_suspension_client') : trans('contract.contract_has_been_suspended_by_user_suspension_freelancer')) }}">{{ trans('common.suspended') }}</span>
							@else
								<span class="contract-status status-suspended" data-toggle="tooltip" title="{{ trans('contract.contract_has_been_suspended_by_ijobdesk') }}">{{ trans('common.suspended') }}</span>
							@endif
						@elseif ( $contract->isCancelled() )
							<span class="contract-status status-cancelled">{{ trans('common.cancelled') }}</span>
						@elseif ( $contract->isClosed() )
							<span class="contract-status status-closed">{{ trans('common.closed') }}</span>
						@elseif ( $contract->isPaused() )
							<span class="contract-status status-paused">{{ trans('common.paused') }}</span>
						@endif
					</div>
				</div>

				<div class="col-md-3 text-right">
					<div class="link">
					@if (!$current_user->isAdmin())
						<a href="{{ _route('job.view', ['id' => $contract->project_id]) }}">
							{{ trans('common.view_original_job_posting') }}
						</a>

						@if ($contract->canLeaveFeedback())
						<br />
						<a href="{{ route('contract.feedback', ['id' => $contract->id]) }}" class="btn btn-primary btn-leave-feedback">{{ trans('common.leave_feedback') }} </a>
						@endif

					@elseif ($current_user->isSuper())
						<a href="{{ !empty($user_id)?route('admin.super.user.contracts', ['id' => $user_id]):route('admin.super.contracts') }}" class="back-list">&lt; Back to list</a>
					@endif
					</div>
				</div>
			</div>
		</div>

		<div class="tab-section">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#contract_overview" role="tab" data-toggle="tab">{{ trans('common.overview') }}</a>
				</li>

				<!-- Milestones -->
				@if ( !$contract->isHourly() )
				<li role="presentation" class="nav-milestones">
					<a href="#contract_milestones" role="tab" data-toggle="tab">{{ trans('common.milestones') }}</a>
				</li>
				@endif

				<!-- Transactions -->
				<li role="presentation">
					<a href="#contract_transactions" role="tab" data-toggle="tab">{{ trans('common.transactions') }}</a>
				</li>

				<!-- Dispute -->
				@if (!$current_user->isAdmin() && (($ticket && $ticket->isDispute()) || !$solved_tickets->isEmpty()))
				<li role="presentation">
					<a href="#contract_dispute" aria-controls="dispute" role="tab" data-toggle="tab" class="tab-dispute">{{ trans('common.dispute') }}</a>
				</li>
				@endif
				
				@if ($contract->isClosed() && ($contract->feedback && ($contract->feedback->buyer_feedback || $contract->feedback->freelancer_feedback )))
					<li role="presentation">
						<a href="#contract_review" role="tab" data-toggle="tab">{{ trans('common.feedback') }}</a>
					</li>
				@endif

				@if ($current_user->isAdmin())
				<li role="presentation">
					<a href="#contract_messages" role="tab" data-toggle="tab" class="tab-contract-messages">Messages</a>
				</li>
				@endif
				@if ($current_user->isSuper())
				<li role="presentation">
					<a href="#action_histories" role="tab" data-toggle="tab" class="tab-action-history">Action History</a>
				</li>
				@endif
			</ul>
		</div>

		<div class="tab-content">
			{{-- Contract Overview --}}
			@include ('pages.contract.section.contract_detail_overview')
			
			{{-- Milestones --}}
			@if ( !$contract->isHourly() )
				@if ( $this_user->isFreelancer() )
					@include ('pages.contract.section.contract_detail_milestones')
				@else
					@include ('pages.contract.section.contract_detail_milestones_buyer')
				@endif
			@endif

			{{-- Transactions --}}
			@if ( $this_user->isFreelancer() )
				@include ('pages.contract.section.contract_detail_transactions')
			@else
				@include ('pages.contract.section.contract_detail_transactions_buyer')
			@endif

			{{-- Dispute --}}
			@include ('pages.contract.section.contract_detail_dispute')

			<!-- Feedback -->
			@if ( $contract->isClosed() )
				@if (($contract->feedback && ($contract->feedback->buyer_feedback || $contract->feedback->freelancer_feedback) ))
					@include ('pages.contract.section.contract_detail_review')
				@endif
			@endif

			{{-- Action History --}}
			@if ($current_user->isAdmin())
				<div id="contract_messages" role="tabpanel" class="tab-pane page-content-section">
					@include ('pages.message.partials.messages', array_merge([
						'thread' => $contract->application->messageThread, 
						'keywords' => '',
						'tab' 	   => '',
					], $project_messages))
				</div>
				@include ('pages.admin.super.contract.action_history')
			@endif
		</div>
		
	</div><!-- .view-section -->
</div><!-- .page-content-section -->

@if ( !$contract->buyer->trashed() && !$contract->contractor->trashed() )
	@if ( !$this_user->isSuspended() )
		@if ( !$this_user->isFinancialSuspended() )
			@if ( $this_user->isFreelancer() )
				@if ( !$contract->buyer->isSuspended() && !$contract->buyer->isFinancialSuspended() && $total_paid < 0 )
					@include('pages.contract.modal.refund')
				@endif
			@else
				@if ( !$contract->contractor->isSuspended() && !$contract->contractor->isFinancialSuspended() )
					@include('pages.contract.modal.payment')
				@endif
			@endif
		@endif

		@if ( $contract->isHourly() && $this_user->isBuyer() )
			@include('pages.contract.modal.weekly_limit')
		@endif
	@endif
		
	@include('pages.contract.modal.dispute')
	@include('pages.contract.modal.cancel_dispute')
@endif

@endsection