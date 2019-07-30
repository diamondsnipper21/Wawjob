<?php
/**
 * @author Ro Un Nam
 * @since Jun 03, 2017
 */

use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;
?>
<div id="contract_overview" role="tabpanel" class="tab-pane active">
	<div class="tab-inner">
		@if ($contract->isHourly() && $current_user->isFreelancer())
		<div class="download-tools-tips">
			<strong>{{ trans('common.tips') }}:</strong> {!! trans('contract.tip_download_tool_description', ['link' => route('frontend.download_tools')]) !!}
		</div>
		@endif

		<div class="row">
			<div class="col-md-6">
				<div class="left-content">
				<?php
					$_this_user = $this_user->replicate();
					$users = [$contract->buyer];

					if ($current_user->isAdmin())
						$users = [$contract->contractor, $contract->buyer];
					elseif ($current_user->isBuyer())
						$users = [$contract->buyer];
					elseif ($current_user->isFreelancer())
						$users = [$contract->contractor];
				?>
				@foreach ($users as $this_user)
					@if ( !$current_user->isAdmin() || ($current_user->isAdmin() && $this_user->isFreelancer()) )
					<div class="row data-row">
						<div class="col-sm-3 col-xs-4">
							<label class="control-label">{{ trans('common.type') }}</label>
						</div>
						<div class="col-sm-9 col-xs-8 mt-1">
							@if ($contract->isHourly())
								{{ trans('common.hourly') }} {{ trans('common.contract') }}
							@elseif ($contract->isFixed())
								{{ trans('common.fixed') }} {{ trans('common.contract') }}
							@endif
						</div>
					</div>
					@endif

					<div class="row data-row">
						<div class="col-sm-3 col-xs-4">
							<label class="label-name control-label">
							@if ( $this_user->isFreelancer() )
								{{ trans('common.buyer') }}
							@elseif ($this_user->isBuyer())
								{{ trans('common.contractor') }}
							@endif
							</label>
						</div>
						<div class="col-sm-9 col-xs-8 mt-1">
							@if ( $this_user->isFreelancer() )
								<img src="{{ avatar_url($contract->buyer) }}" class="img-circle avatar pull-left" width="100" />
							@else
								<img src="{{ avatar_url($contract->contractor) }}" class="img-circle avatar pull-left" width="100" />
							@endif

							<div class="user-info">
								<div class="name mb-2 mt-1">
									@if ( $this_user->isFreelancer() )
										<span>
											@if ($current_user->isSuper())
												<a href="{{ route('admin.super.user.overview', ['user_id' => $contract->buyer->id]) }}">{{ $contract->buyer->fullname() }}</a>
											@else
												{{ $contract->buyer->fullname() }}
											@endif
										</span>

										@if ( $contract->buyer->isSuspended() )
										<span class="suspended">
											<i class="fa fa-exclamation-circle"></i> {{ trans('common.suspended') }}
										</span>
										@elseif ( $contract->buyer->isFinancialSuspended() )
										<span class="suspended">
											<i class="fa fa-exclamation-circle"></i> {{ trans('common.financial_suspended') }}
										</span>
										@elseif ( $contract->buyer->trashed() )
										<span class="suspended">
											<i class="fa fa-exclamation-circle"></i> {{ trans('common.deleted') }}
										</span>
										@endif
									@else
										<span>
											@if ($current_user->isSuper())
												<a href="{{ route('admin.super.user.overview', ['user_id' => $contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
											@else
												<a href="{{ _route('user.profile', ['uid' => $contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a>
											@endif
										</span>
										@if ( $contract->contractor->isSuspended() )
										<span class="suspended">
											<i class="fa fa-exclamation-circle"></i> {{ trans('common.suspended') }}
										</span>
										@elseif ( $contract->contractor->isFinancialSuspended() )
										<span class="suspended">
											<i class="fa fa-exclamation-circle"></i> {{ trans('common.financial_suspended') }}
										</span>
										@elseif ( $contract->contractor->trashed() )
										<span class="suspended">
											<i class="fa fa-exclamation-circle"></i> {{ trans('common.deleted') }}
										</span>
										@endif
									@endif
								</div>

								@if ( $this_user->isFreelancer() )
									@include ('pages.contract.section.user_meta', ['user' => $contract->buyer])
								@else
									@include ('pages.contract.section.user_meta', ['user' => $contract->contractor])
								@endif
							</div>
						</div>			
					</div>
				@endforeach
				<?php $this_user = $_this_user->replicate(); ?>
					@if ( $contract->isHourly() )
						<!-- Billing Rate -->
						<div class="row data-row billing-rate-row">
							<div class="col-sm-3 col-xs-4">
								<label class="control-label">{{ trans('common.billing_rate') }}</label>
							</div>
							<div class="col-sm-9 col-xs-8 mt-1">
								<span class="pull-left">
									{{ formatCurrency($contract->price, $currency_sign) }}
									@if ( $contract->isHourly() )
										 / {{ trans('common.hr') }}
									@endif
								</span>
							</div>
						</div>
						<!-- Weekly Limit -->
						<div class="row data-row">
							<div class="col-sm-3 col-xs-4">
								<label class="control-label">{{ trans('common.weekly_limit') }}</label>
							</div>
							<div class="col-sm-9 col-xs-8 mt-1">
								<span class="pull-left w-30">
									{{ $contract->weekly_limit_string() }}
								</span>

								@if ( $this_user->isBuyer() && $contract->isAvailableAction() && !$contract->isClosed() )
									<a class="btn btn-link btn-change-limit pull-left" href="#modalWeeklyLimit" data-toggle="modal" data-backdrop="static">{{ trans('common.change') }}</a>
								@endif
								<div class="clearfix"></div>

								@if ( $contract->isChangedLimit() )
									<div class="pt-2 info">{{ $contract->weekly_new_limit_string() }} ({{ trans('contract.new_weekly_limit_applied_next_week') }})</div>
								@endif
							</div>
						</div>

						<!-- Manual Time -->
						<div class="row data-row">
							<div class="col-sm-3 col-xs-4">
								<label class="control-label">{{ trans('common.manual_time') }}</label>
							</div>
							<div class="col-sm-9 col-xs-8 mt-1">
								<span class="pull-left w-30">{{ $contract->isAllowedManualTime() ? trans('common.allowed') : trans('common.not_allowed') }}</span>

								@if ( $this_user->isBuyer() && $contract->isAvailableAction() && !$contract->isClosed() )
									<form id="form_allow_manual_time" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id]) }}" class="pull-left">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" name="AllowManualTime" value="1">
										<a class="btn btn-link btn-change-manual pull-left{{ $contract->isAllowedManualTime() ? ' allowed' : '' }}">{{ trans('common.change') }}</a>
									</form>
								@endif
							</div>
						</div>

						<!-- Allow Over Time -->
						@if ( $this_user->isBuyer() )
						<div class="row data-row">
							<div class="col-sm-3 col-xs-4">
								<label class="control-label">{{ trans('common.over_time') }}</label>
							</div>
							<div class="col-sm-9 col-xs-8 mt-1">
								<span class="pull-left w-30">{{ $contract->isAllowedOverTime() ? trans('common.allowed') : trans('common.not_allowed') }}</span>

								@if ( $contract->isAvailableAction() && !$contract->isClosed() )
									<form id="form_allow_over_time" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id]) }}" class="pull-left">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" name="AllowOverTime" value="1">
										<a class="btn btn-link btn-change-overtime pull-left{{ $contract->isAllowedOverTime() ? ' allowed' : '' }}">{{ trans('common.change') }}</a>
									</form>
								@endif
							</div>
						</div>
						@endif					
					@endif

					<!-- Status -->
					<div class="row data-row">
						<div class="col-sm-3 col-xs-4">
							<label class="control-label status-label">{{ trans('common.status') }}</label>
						</div>
						<div class="col-sm-9 col-xs-8">
							<span class="pull-left">{{ $contract->status_string() }}</span>
						</div>
					</div>

					<!-- Start Date -->
					<div class="row data-row">
						<div class="col-sm-3 col-xs-4">
							<label class="control-label">{{ trans('common.start_date') }}</label>
						</div>
						<div class="col-sm-9 col-xs-8 mt-1">
							{{ format_date('M j, Y', $contract->started_at) }}
						</div>
					</div>

					<!-- End Date -->
					<div class="row data-row">
						<div class="col-sm-3 col-xs-4">
							<label class="control-label">{{ trans('common.end_date') }}</label>
						</div>
						<div class="col-sm-9 col-xs-8 mt-1">
							@if ( $contract->isClosed() )
								{{ format_date('M j, Y', $contract->ended_at) }}
							@else
								-
							@endif
						</div>
					</div>
				</div>
			</div><!-- .col-md-6 -->

			<div class="col-md-6">
				<div class="right-content">
					<div class="row">
						@if ( $contract->isHourly() )
						<div class="col-md-offset-1 col-md-10 p-4 hourly default-boxshadow">
							<div class="">
								<div class="row">
									<div class="col-xs-4 week">
										<label class="control-label">{{ trans('common.this_week') }}</label>
										<div class="hours">
											{{ formatMinuteInterval($contract->meter->this_mins) }} {{ trans('common.hrs') }}
										</div>
										<div class="amount">
											{{ formatCurrency($contract->meter->this_amount, $currency_sign) }}
										</div>
									</div>

									<div class="col-xs-4 week">
										<label class="control-label">{{ trans('common.last_week') }}</label>
										<div class="hours">
											{{ formatMinuteInterval($contract->meter->last_mins) }} {{ trans('common.hrs') }}
										</div>
										<div class="amount">
											{{ formatCurrency($contract->meter->last_amount, $currency_sign) }}
										</div>
									</div>
									<div class="col-xs-4 week">
										<label class="control-label">{{ trans('common.total_hours') }}</label>
										<div class="hours">
											{{ formatMinuteInterval($contract->meter->this_mins + $contract->meter->total_mins) }} {{ trans('common.hrs') }}
										</div>
										<div class="amount">
											{{ formatCurrency($contract->meter->this_amount + $contract->meter->total_amount - $total_bonus + $total_refunded, $currency_sign) }}
										</div>
									</div>
								</div>
							</div>

							<div class="border-top pt-4 mt-4 total">
								<div class="row">
									<div class="col-xs-8">
										<label class="control-label mt-1">{{ trans('common.total_amount_paid') }}</label>
									</div>
									<div class="col-xs-4">
										<div class="amount">
											{{ formatCurrency(abs($total_paid), $currency_sign) }}
										</div>
									</div>
								</div>
							</div>
						</div>
						@else
						<div class="col-md-offset-1 col-md-10 fixed default-boxshadow pt-4 pb-4">
							<div class="row">
								<div class="col-xs-6 text-center">
									<label class="control-label">{{ trans('common.funds_in_escrow') }}</label>
									<div class="value pt-4 pb-2">
										{{ formatCurrency($total_funded, $currency_sign) }}
									</div>
									<div class="pt-2">
										<label>{{ trans('common.in_progress') }}</label>
									</div>
								</div>

								<div class="col-xs-6 text-center">
									<label class="control-label">{{ trans('common.total_amount') }}</label>
									<div class="value pt-4 pb-2">
										{{ formatCurrency($total_gross, $currency_sign) }}
									</div>
									<div class="pt-2">
										<label>{{ trans('common.paid') }}</label>
									</div>
								</div>
							</div>
						</div>
						@endif
					</div><!-- .row -->

					<div class="text-right pt-5 pr-5 mr-4 invisible-in-admin">
						<!-- Send Message -->
						@if ( !$contract->isSuspended() )
							<a class="btn btn-link" href="{{ $contract->application && $contract->application->messageThread ? _route('message.list', ['id' => $contract->application->messageThread->id]) : _route('message.list') }}">{{ trans('common.send_message')}}</a>
						@endif

						<!-- View Workdiary -->
						@if ( !$contract->isSuspended() && $contract->isOpen() && $contract->isHourly() )
							<span class="div">|</span>
							<a class="btn btn-link" href="{{ _route('workdiary.view', ['cid' => $contract->id]) }}">{{ trans('common.view_work_diary') }}</a>
						@endif
					</div>
				</div><!-- .right-content -->
			</div><!-- .col-md-6 -->
		</div><!-- .row -->
		
		@if ( !$contract->buyer->trashed() && !$contract->contractor->trashed() )
			<div class="action-buttons invisible-in-admin">
				<div class="row">
					<div class="col-md-8 col-md-offset-4">
						<div class="text-right pt-4 pb-4 pr-5">
							@if ( $contract->isSuspended() && $contract->isOpen() && $contract->isHourly() )
								<a class="btn btn-primary" href="{{ _route('workdiary.view', ['cid' => $contract->id]) }}">{{ trans('common.view_work_diary') }}</a>
							@endif

							@if ( $contract->isAvailableAction(true) )
								@if ( $this_user->isFreelancer() && $contract->isAvailableRefund() )
									@if ( $total_paid < 0 )
									<a class="btn btn-primary" href="#modalRefund" data-type="{{ TransactionLocal::TYPE_REFUND }}" data-toggle="modal" data-backdrop="static">{{ trans('common.give_a_refund') }}</a>
									@endif
								@else
									@if ( $contract->isOpen() )
										<a class="btn btn-primary" href="#modalPayment" data-type="{{ TransactionLocal::TYPE_BONUS }}" data-toggle="modal" data-backdrop="static">{{ trans('common.give_bonus') }}</a>
									@endif
								@endif
							@endif

							<!-- Restart & Pause -->
							@if ( $current_user->isBuyer() && $contract->isAvailableAction() )
							<form id="form_change_status" method="post" action="{{ route('contract.contract_view', ['id' => $contract->id]) }}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								@if ( $contract->isPaused() )
									<input type="hidden" name="_action" value="restart">
									<button type="submit" class="btn btn-primary btn-restart">{{ trans('common.restart_contract') }}</button>
								@elseif ( $contract->isOpen() )
									<input type="hidden" name="_action" value="pause">
									<button type="submit" class="btn btn-primary btn-pause">{{ trans('common.pause_contract') }}</button>
								@endif
							</form>
							@endif
							
							<!-- Cancel & End -->
							@if ( !$contract->isClosed() && !$contract->isCancelled() )
								@if ( !$contract->canLeaveFeedback() && $current_user->isBuyer() && $total_paid >= 0 )
								<button id="btnCancelContract" class="btn btn-danger" data-url="{{ route('contract.feedback', ['id' => $contract->id]) }}" {{ !$contract->isAvailableAction() ? 'disabled': '' }}>{{ trans('common.cancel_contract') }}</button>
								@else
								<a id="btnEndContract" class="btn btn-danger" href="{{ route('contract.feedback', ['id' => $contract->id]) }}" {{ !$contract->isAvailableAction() ? 'disabled': '' }}>{{ trans('common.end_contract') }}</a>
								@endif
							@endif
						</div>
					</div>
				</div>
			</div>
			
			@if ( $contract->isAvailableDispute() && !$ticket && !$current_user->isAdmin())<?php /* this contract is not on dispute now... */ ?>
			<div class="row">
				<div class="col-md-6 col-md-offset-6">
					<div class="border-bottom mt-3 mb-3"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-md-offset-6">
					<div class="text-right pt-4 pr-5">
						<p>{{ $this_user->isFreelancer() ? trans('contract.freelancer_trouble') : trans('contract.buyer_trouble') }}</p>
						@if ( $ticket )
							<a class="btn btn-link btn-dispute">{{ trans('common.file_dispute') }}</a>
						@else
							<a class="btn btn-link" href="#modalDispute" data-toggle="modal" data-backdrop="static">{{ trans('common.file_dispute') }}</a>
						@endif
					</div>
				</div>
			</div>
			@endif
		@endif
	</div><!-- .tab-inner -->
</div><!-- .tab-pane -->