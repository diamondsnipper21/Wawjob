<?php
use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\User;
?>
<div class="box-row object-item " data-id="{{ $offer->id }}">
    <div class="col-md-5 col-sm-4 break subject">
        <a href="{{ _route('job.overview', ['id' => $offer->project_id]) }}" class="main-cell">
            {{ $offer->title }}
            @if ( $offer->project->status == Project::STATUS_SUSPENDED )
            <span class="job-status status-cancelled">[{{ trans('common.suspended') }}]</span>
            @endif
        </a>
        <div class="posted margin-top-10">
            {{ trans('common.offer_sent') }} {{ ago($offer->created_at) }}
        </div>
    </div>

    <div class="col-md-5 col-sm-4 col-xs-7 user-price">
        <div class="row">
            <div class="col-sm-4 price">
                <div class="terms">
                    {!! $offer->term_string() !!}
                </div>
            </div>
            <div class="col-sm-8">
	            <div class="contractor-avatar">
                    <img src="{{ avatar_url($offer->contractor) }}" width="60" />
                </div>
                <div class="contractor-info">
                    <span>
                        @if ( $offer->contractor->isSuspended() )
                            {{ $offer->contractor->fullname() }} 
                            <span class="suspended">
                                <i class="fa fa-exclamation-circle"></i> {{ trans('common.suspended') }}
                            </span>
                        @else
                            <a href="{{ _route('user.profile', [$offer->contractor->id]) }}">{{ $offer->contractor->fullname() }}</a>
                        @endif
                    </span>
                    <br/>
                    @if ( $offer->contractor->contact->country )
                    <span>{{ $offer->contractor->contact->country->name }}</span><br/>
                    @endif
                    @if ( $offer->contractor->contact->timezone )
                    <span>{{ convertTz('now', $offer->contractor->contact->timezone->name, 'UTC', 'g:i A')}}</span>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-sm-4 col-xs-5 text-right action">
		<button type="button" class="btn btn-border btn-danger btn-withdraw {{ $current_user->isSuspended() || $offer->project->isSuspended() ? 'disabled' : '' }}">{{ trans('common.withdraw_offer') }}</button>

		@if ( !$current_user->isSuspended() && !$offer->project->isSuspended() )
		<div class="box-withdraw">
			<button type="button" class="close">&times;</button>
			<div class="box-title">
				{{ trans('common.withdraw_offer') }}
			</div>
			<div class="box-message">
				<form class="form-horizontal form-withdraw" action="{{ route('job.withdraw_offer.ajax') }}" method="post">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="id" value="{{ $offer->id }}">
					<div class="box-ctrl">
						<label>{{ trans('common.reason') }}</label>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="1" id="reason_1" checked="checked">
								{{ trans('common.mistake') }}
							</label>
						</div>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="2" id="reason_2">
								{{ trans('job.reason_hired_another_freelancer') }}
							</label>
						</div>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="3" id="reason_3">
								{{ trans('job.reason_irresponsive_freelancer') }}
							</label>
						</div>
						<div class="radiobox">
							<label>
								<input type="radio" name="reason" value="4" id="reason_4">
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
    </div>
</div>