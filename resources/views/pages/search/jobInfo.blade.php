<?php
/**
* JobInfo Page (search/job)
*
* @author  - so gwang
*/

use iJobDesk\Models\Project;
use iJobDesk\Models\UserSavedProject;

?>
<div class="content-box"> 
    <div class="row mb-3">
        <div class="col-xs-10 break">
            <a class="subject" href="{{ _route('job.view', ['id' => $job->id]) }}" target="_blank">{{ $job->subject }}</a>
		    @if ( $job->isFeatured() )
		    <span class="label-featured round-ribbon">{{ trans('common.featured') }}</span>
		    @endif
            @if ( $current_user && $current_user->isFreelancer() && $job->isSentProposal($current_user) )
            <span class="flag ml-2" data-toggle="tooltip" title="{{ trans('job.you_sent_proposal_this_job_posting') }}"><i class="icon-flag"></i></span>
            @endif

            @if ( $job->isClosed() )
            <span class="ml-3 label-closed round-ribbon">{{ trans('common.ended') }}</span>
            @endif
        </div>

        @if ( $current_user && $current_user->isFreelancer() )
        <div class="col-xs-2 text-right save">
            @if ( !$job->isSaved() )
                <a data-id="{{ $job->id }}" data-action="create"><i class="fa fa-heart-o"></i></a>
            @else
                <a data-id="{{ $job->id }}" data-action="destroy"><i class="fa fa-heart"></i></a>
            @endif
        </div>
        @endif
    </div>

    <div class="summary mb-3">  
        <div class="w-15 pull-left">
	        @if ( $job->isHourly() ) 
	        	<i class="fa icon-hotel-restaurant-003 u-line-icon-pro"></i>
	        @else
	        	<i class="fa hs-admin-pin-2"></i>
	        @endif
        	<strong>{{ $job->type_string() }}</strong>
        </div>
        <div class="w-20 pull-left"><span>{{ $job->exp_lv_string() }}</span></div>
        
        <div class="w-25 pull-left">
        	<span>{{ trans('common.estimated_abbr') }} {{ trans('common.budget') }}</span>
        	<span>
        		@if ( $job->isHourly() )
        			{{ $job->affordable_rate_string() }}
        		@else
        			{{ $job->price_string(true) }}
        		@endif
        	</span>
    	</div>

        <div class="w-20 pull-left">
        	<span>{{ trans('common.posted' ) }}</span>
        	<span>{{ ago($job->created_at) }}</span>
        </div>

        <div class="clearfix"></div>
    </div>

    <div class="desc break">
        @if (mb_strlen(strip_tags($job->desc)) > 400)
        <div class="description"> 
            {{ mb_substr(strip_tags($job->desc), 0, 400) }}...
            <a href="{{ _route('job.view', ['id'=>$job->id]) }}" target="_blank">{{ trans('common.more') }}</a>
        </div>
        @else
            {{ strip_tags($job->desc) }}
        @endif
    </div>

    @if ( count($job->skills) )
    <div class="project-skills mt-3 clearfix">
        @foreach ( $job->skills as $skill )
        <span class="rounded-item">{{ parse_multilang($skill->name) }}</span>
        @endforeach
    </div>
    @endif
</div>