<?php
/**
* Proposal Page on Super Admin
*
* @author KCG
* @since July 17, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\File;

$proposal->status = $proposal->is_declined == ProjectApplication::IS_FREELANCER_DECLINED || $proposal->is_declined == ProjectApplication::IS_CLIENT_DECLINED?ProjectApplication::STATUS_WITHDRAWN:$proposal->status;

?>
@extends('layouts/admin/super'.(!empty($user)?'/user':''))

@section('content')

<div id="proposal">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <!-- <span class="caption-helper">Proposal:</span> -->
	            <span class="caption-subject font-green-sharp bold">Proposal Detail</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<div class="title-section">
				<div class="row">
					<div class="col-md-8">
						<span class="title"><i class="fa fa-bars"></i> {{ $proposal->project->subject }}</span>
						<span class="yellow">[{{ array_search($proposal->status, ProjectApplication::getOptions('status')) }}]</span>
					</div>
					<div class="col-md-4 text-right">
						<div class="link">
							<a class="text-underline" href="{{ route('admin.super.job.overview', ['id' => $proposal->project->id]) }}">{{ trans('common.view_original_job_posting') }}</a>
						</div>
					</div>
				</div>
			</div>
	    	<div class="row">
	    		<div class="col-md-4">
	    			<div class="portlet light short-info">
	    				<div class="user-short-info margin-top-20">
                            <div class="row">
                                <div class="col-sm-4">
                                    <img src="{{ avatar_url($proposal->project->client) }}" class="img-circle user-avatar" />
                                </div>
                                <div class="col-sm-8">
                                    <div class="user-name-loc">
                                        <div class="user-fullname">{{ $proposal->project->client->fullname }}</div>
                                        <div class="user-role">Buyer</div>
                                        <div class="user-location"><i class="fa fa-map-marker"></i> {{ $proposal->project->client->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .user-short-info -->
                        <div class="user-short-info margin-top-20">
                            <div class="row">
                                <div class="col-sm-4">
                                    <img src="{{ avatar_url($proposal->user) }}" class="img-circle user-avatar" />
                                </div>
                                <div class="col-sm-8">
                                    <div class="user-name-loc">
                                        <div class="user-fullname">{{ $proposal->user->fullname }}</div>
                                        <div class="user-role">Freelancer</div>
                                        <div class="user-location"><i class="fa fa-map-marker"></i> {{ $proposal->user->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .user-short-info -->
	    			</div>
	    		</div>
	    		<div class="col-md-8">
			    	<div class="summary-desc">
				    	<h4 class="block"><i class="fa fa-reorder"></i> Job Description</h4>
				    	<div class="well">
							 {!! nl2br($proposal->project->desc) !!}
						</div>
					</div>
					
					{!! render_files($proposal->project->files) !!}

					@if (!empty($proposal->cv) && ($proposal->provenance != ProjectApplication::PROVENANCE_OFFER))
						<div class="coverletter">
							<h4 class="block">
								<i class="fa fa-comments"></i> 
								{{ $proposal->provenance != ProjectApplication::PROVENANCE_OFFER?'Cover Letter Of Freelancer':'Messages From Buyer' }}
							</h4>
					    	<div class="well">
					    		{!! nl2br($proposal->cv) !!}
							</div>
							<div class="attachments">
								{!! render_files($proposal->files) !!}
							</div>								
						</div>
					@endif

					@if ($contract && $proposal->provenance == ProjectApplication::PROVENANCE_OFFER)
						@include('pages.admin.super.proposal.detail.offer_terms')
					@endif

					<!-- Messages -->
					@if ($messages->count() != 0)
						@include('pages.admin.super.proposal.detail.messages')
					@endif
	    		</div>
	    	</div>
	    </div>
	</div>
</div>

@endsection