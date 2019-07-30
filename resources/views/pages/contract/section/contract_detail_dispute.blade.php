<?php
/**
 * @author Ro Un Nam
 * @since Jun 03, 2017
 */

use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\File;

?>
<div id="contract_dispute" role="tabpanel" class="tab-pane">
	<div class="tab-inner clearfix">
		@if (!$solved_tickets->isEmpty())
		<a href="#modal_dispute_history" data-toggle="modal" data-backdrop="static" class="pull-right view-history">View history</a>
		@endif
		@if ( $ticket && $ticket->isDispute() )
		<div class="row margin-bottom-10">
			<div class="left-content col-md-4 col-sm-4">
				<div class="user-info">
					<div class="buyer-info clearfix margin-bottom-50">
						<div class="avatar-panel">
							<a href="{{ _route('user.profile', ['uid'=>$contract->buyer->id]) }}">
								<img src="{{ avatar_url($contract->buyer) }}" class="img-circle big-img-circle" />
							</a>
						</div>
						<div class="name-panel">
							<div class="profile-name margin-bottom-10">
								<span>{{ $contract->buyer->fullname() }}</span>

								@if ($ticket->user_id == $contract->buyer->id)
									<span class="initiator-label"> Initiator </span>
								@endif

								<br />
								<span>({{ trans('common.buyer') }})</span>
							</div>
							<div class="profile-title margin-bottom-10">
								<i class="fa fa-map-marker"></i> {{ $contract->buyer->location() }}
							</div>
							<div>
								<i class="icon-clock"></i>&nbsp;&nbsp;{{ format_date('g:i a', date('Y-m-d H:i:s'), $contract->buyer) }}
							</div>
						</div>
					</div>
					<div class="freelancer-info clearfix margin-bottom-50">
						<div class="avatar-panel">
							<a href="{{ _route('user.profile', ['uid' => $contract->contractor->id]) }}">
								<img src="{{ avatar_url($contract->contractor) }}" class="img-circle big-img-circle" />
							</a>
						</div>
						<div class="name-panel">
							<div class="profile-name margin-bottom-10">
								<span>
									<a href="{{ _route('user.profile', ['uid'=>$contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
								</span>
								@if ($ticket->user_id == $contract->contractor->id)
									<span class="initiator-label"> Initiator </span>
								@endif

								<br />
								<span>({{ trans('common.freelancer') }})</span>
							</div>
							<div class="profile-title margin-bottom-10">
								<i class="fa fa-map-marker"></i> {{ $contract->contractor->location() }}
							</div>
							<div  class="profile-timezone">
								<i class="icon-clock"></i>&nbsp;&nbsp;{{ format_date('g:i a', date('Y-m-d H:i:s'), $contract->contractor) }}
							</div>
						</div>
					</div>
					<div class="devider margin-bottom-20 hide"></div>

					<!-- Start Date -->
					<div class="start-date row clearfix margin-bottom-20">
						<div class="col-md-3 label_text"><strong>{{ trans('common.initiated') }}</strong></div>
						<div class="col-md-9">{{ format_date('M j, Y g:i A', $ticket->created_at) }}</div>
					</div>

					<!-- End Date  -->
					@if ($ticket->status == Ticket::STATUS_SOLVED || $ticket->status == Ticket::STATUS_CLOSED || $contract->status == Contract::STATUS_CLOSED)
					<div class="end-date row clearfix margin-bottom-20">
						<div class="col-md-4 col-sm-4 label_text">{{ trans('common.end_date') }} : </div>
						<div class="col-md-8 col-sm-8">
							{{ format_date('M j, Y g:i A', $ticket->ended_at) }}
						</div>
					</div>
					<!-- {{-- This ticket was created by ME, show button for cancelling dispute --}} -->
					@elseif (!$current_user->isAdmin() && $ticket->user_id == $current_user->id)
					<div class="text-center">
						<button class="btn btn-primary btn-border" data-toggle="modal" data-target="#modal_cancel_dispute">{{ trans('contract.cancel_dispute') }}</button>
					</div>
					@endif
				</div>
			</div>

			<div id="contract_disputes" class="right-content col-md-8 col-sm-8">
                @include('pages.partials.messages', [
                                'id' => $ticket->id, 
                                'messages' => $messages, 
                                'type' => File::TYPE_TICKET_COMMENT, 
                                'class' => 'Frontend\\Contract\\Dispute', 
                                'can_send' => 
                                	!$ticket->isClosed() && !$current_user->isAdmin(), 
                                'totals' => $message_count,
                                'limit' => $message_limit
                ])
			</div>
		</div>
		@else
			@if ( !$this_user->isSuspended() && !$contract->isSuspended() && !$contract->isClosed())
			<div class="dispute row">
				<div class="col-md-12 text-right">
					<p>{{ $this_user->isFreelancer() ? trans('contract.freelancer_trouble') : trans('contract.buyer_trouble') }}</p>
					@if ( $ticket && $ticket->isDispute() )
						<a class="btn btn-link btn-dispute">{{ trans('common.file_dispute') }}</a>
					@else
						<a class="btn btn-link" href="#modalDispute" data-toggle="modal" data-backdrop="static">{{ trans('common.file_dispute') }}</a>
					@endif				
				</div>
			</div>
			@endif
		@endif
	</div>
	@include ('pages.contract.section.contract_detail_dispute_history')
</div>