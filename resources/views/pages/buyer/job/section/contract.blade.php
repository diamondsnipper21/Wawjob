<?php
/**
* @author KYZ
* @since Jun 27, 2017
*/
use iJobDesk\Models\Contract;
?>
<div class="box-row object-item {{ $contract->status == Contract::STATUS_CLOSED ? ' closed' : '' }}">
    <div class="col-xs-5 subject">
        <a href="{{ _route('contract.contract_view', ['id' => $contract->id]) }}" class="main-cell">
        {{ $contract->title }}
        </a>
        <div class="details">
            <div class="posted">
                {{ trans('common.started') }} {{ ago($contract->started_at) }}
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

    <div class="col-xs-5 user-price">
        <div class="row">
            <div class="col-md-5 price">
                @if ( $contract->isHourly() )
                    @if ( $contract->isNoLimit() )
                        <div class="terms">
                            {{ trans('common.no_limit') }}
                        </div>
                        <div class="terms">
                            ${{ formatCurrency($contract->price) }} / {{ trans('common.hour') }}
                        </div>
                    @else
                        <div class="terms">
                            {{ trans('common.n_hours_week', ['n' => $contract->limit]) }}
                        </div>
                        <div class="terms">
                            ${{ formatCurrency($contract->price) }} / {{ trans('common.hour') }}
                        </div>
                    @endif
                @else
                    <div class="terms">
                        {{ trans('common.fixed_price') }}
                    </div>
                    <div class="terms">
                        ${{ formatCurrency($contract->price) }}
                    </div>
                @endif
            </div>
            <div class="col-md-7">
                <div class="contractor-avatar">
                    <img src="{{ avatar_url($contract->contractor) }}" width="60" />
                </div>
                <div class="contractor-info">
                    <span><a href="{{ _route('user.profile', [$contract->contractor->id]) }}">{{ $contract->contractor->fullname() }}</a></span><br/>
                    @if ( $contract->contractor->contact->country )
                    <span>{{ $contract->contractor->contact->country->name }}</span><br/>
                    @endif
                    @if ( $contract->contractor->contact->timezone )
                    <span>{{ convertTz('now', $contract->contractor->contact->timezone->name, 'UTC', 'g:i A')}}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-2 text-right work-diary">
        @if ( $contract->isOpen() && $contract->isHourly() )
        <a class="link" href="{{ _route('workdiary.view', ['cid'=>$contract->id]) }}">{{ trans('common.work_diary') }} <i class="fa fa-angle-right"></i></a>
        @endif
    </div>
</div>