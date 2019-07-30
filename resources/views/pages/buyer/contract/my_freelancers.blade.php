<?php
/**
* My Freelancers Page (my-freelancers)
*
* @author  - nada
*/

use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\User;

?>
@extends('layouts/default/index')

@section('content')
<div id="my_freelancers" class="my-freelancers-page">
    <form method="post" action="{{ $tab == 'hired'?route('contract.my_freelancers'):route('contract.my_freelancers', ['tab' => 'saved']) }}">
    <div class="row margin-bottom-20">
        <div class="col-md-12">
            <div class="title-section">
                <span class="title">{!! $tab == 'saved'?trans('common.saved_freelancers'):trans('common.my_freelancers') !!}</span>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs">
        <li class="{{ $tab == 'hired'?'active':'' }}">
            <a class="tab" href="{{ $tab == 'hired'?'':route('contract.my_freelancers') }}">{!! trans('common.hired_freelancers') !!}</a>
        </li>
        <li class="{{ $tab == 'saved'?'active':'' }}">
            <a href="{{ $tab == 'saved'?'':route('contract.my_freelancers', ['tab' => 'saved']) }}" class="tab">{!! trans('common.saved_freelancers') !!}</a>
        </li>
    </ul>

    <div class="page-content-section no-padding">
        <div id="form_my_freelancers" class="form-section">
            {{ show_messages() }}

            <div class="row row-filters">
                <div class="col-md-6 col-sm-4"></div>
                <div class="col-md-6 col-sm-8 pt-1 pb-1 bg-gray">
                    <div class="row">
                        <div class="col-xs-6">
		                    <div class="input-group">
		                        <input class="form-control" type="text" placeholder="{{ trans('common.search') }}..." name="keywords" value="{{ $keywords }}">
		                        <span class="input-group-btn">
		                            <button type="submit" class="btn btn-primary"><i class="icon-magnifier"></i></button>
		                        </span>
		                    </div>
		                </div>
		                @if ($tab == 'hired')
		                <div class="col-xs-6">
                            <span class="pull-left w-25 mt-2">{{ trans('common.sort_by') }}</span>
	                        <div class="pull-right w-75">
	                            <select class="sort-selection form-control select2" name="sort_by" id="sort_by">
	                                <option value="0" {{ $filter_sort_by == 0 ? 'SELECTED' : '' }}>{{ trans('common.sort_by') }}</option>
	                                <option value="1" {{ $filter_sort_by == 1 ? 'SELECTED' : '' }}>{{ trans('common.recent_hires') }}</option>
	                                <option value="2" {{ $filter_sort_by == 2 ? 'SELECTED' : '' }}>{{ trans('common.job_success') }}</option>
	                                <option value="3" {{ $filter_sort_by == 3 ? 'SELECTED' : '' }}>{{ trans('common.reviews') }}</option>
	                                <option value="4" {{ $filter_sort_by == 4 ? 'SELECTED' : '' }}>{{ trans('common.availability') }}</option>
	                                <option value="5" {{ $filter_sort_by == 5 ? 'SELECTED' : '' }}>{{ trans('common.high_hourly_rate') }}</option>
	                                <option value="6" {{ $filter_sort_by == 6 ? 'SELECTED' : '' }}>{{ trans('common.lower_hourly_rate') }}</option>
	                            </select>
	                        </div>
		                </div>
		                @endif
		            </div>
		        </div>
            </div>

            <div class="freelancers-section user-contracts">
            @forelse ($freelancers as $c)
                <div class="user-item clearfix contractor-item">
                    @include('pages.partials.user', ['user' => $c])
                    <div class="user-action">
                        <a href="{{ _route('job.hire_user', ['uid' => $c->id]) }}" class="btn btn-primary btn-hire{{ $current_user->isSuspended() ? ' disabled' : '' }}">{{ trans('common.hire_now') }}</a>
                    </div>

                    <div class="clearfix"></div>

                    @if ($tab == 'hired')
                    <div class="contractor-feedback clearfix">
                        <div class="contract-item">
                            <a class="show-feedback"><span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>&nbsp;&nbsp;{{ trans('common.my_feedback_to_the_freelancer') }} </a>
                        </div>
                        <div class="feedbacks">
                            <?php $num = 0; ?>
                            @foreach ($c->contracts as $_c)
                            @if ( $_c->isClosed() )
                            <div class="contract-block margin-bottom-10">
                                <div class="row">
                                    <div class="col-md-8 col-sm-6">
                                    	<a href="{{ _route('contract.contract_view', ['id'=>$_c->id]) }}" class="title">{{ $_c->title }}</a>
                                        <div class="freelancer-review">
                                            <div class="freelancer-score">
                                                <div class="stars" data-value="{{ $_c->feedback ? $_c->feedback->buyer_score/5*100 : 0}}%" data-toggle="tooltip" title="{{ number_format($_c->feedback ? $_c->feedback->buyer_score : 0, 1)}}"></div>
                                            </div>
                                            <div class="freelancer-feedback">
                                                {{ $_c->feedback ? $_c->feedback->buyer_feedback : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="contract-date">
                                            {{ format_date('M d, Y', $_c->started_at) }} ~ {{ format_date('M d, Y', $_c->ended_at) }}
                                        </div>
                                        <div class="hourly_rate">
                                            @if ( $_c->isHourly() )
                                                {{ trans('common.hourly') }} - <span>${{ $_c->price }} / {{ trans('common.hr') }}</span> &nbsp;
                                                @if ( $_c->limit >= 0 )
                                                <span>({{ trans('common.limit') }}: {{ trans('common.n_hours', ['n' => $_c->limit]) }})</span>
                                                @else
                                                <span>({{ trans('common.no_limit') }})</span>
                                                @endif
                                            @else
                                                {{ trans('common.fixed') }} - <span>${{ $_c->price }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $num++; ?>
                            @endif
                            @endforeach
                            @if($num == 0)
                            <div class="contract-block margin-bottom-10">
                                {{ trans('profile.message.No_Feedback_Yet') }}
                            </div>
                            @endif
                        </div>
                    </div><!-- .contractor-feedback -->
                    @endif
                </div>
            @empty
                <div class="not-found-result">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="heading">{{ trans('job.you_have_no_freelancers') }}</div>
                        </div>
                    </div>
                </div>
            @endforelse

            @if ($tab == 'hired' && $filter_sort_by == 1)
            <div class="content-box">
                <div class="row margin-bottom-10">
                    <div class="col-sm-12 text-right" id="pagination_wrapper">{!! $freelancers->render() !!}</div>
                </div>
            </div>
            @endif
            </div><!-- END OF .freelancers-section -->
        </div>
    </div>
    </form>
</div>
@endsection