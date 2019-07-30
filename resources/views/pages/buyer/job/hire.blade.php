<?php
/**
 * Hire User
 *
 * @author - KCG
 * @since  - 2017/5/26
 */
use iJobDesk\Models\Project;
use iJobDesk\Models\File;

use iJobDesk\Models\StaticPage;

$static_pages = StaticPage::all();
$pages = [];
foreach ($static_pages as $p)
	$pages[$p->id] = $p->slug;

?>
@extends('layouts/default/index')

@section('content')

<!-- =========== Begin ===========  -->
<div class="page-content-section p-0">
	<div class="view-section job-content-section p-0">
		
		<div class="row">
        	<div class="col-md-9">
            	<div class="box-section page-content">

            		{{ show_warnings() }}
                    {{ show_messages() }}

			        <div class="job-top-section mb-4">
			            <div class="title-section">
			                <span class="title mt-1 pull-left">{{ trans('common.hire') }}</span>

							<div class="profile-info ml-4">
								<img src="{{ avatar_url($user) }}" class="img-circle" />
								<div>
									<div class="profile-name"><a href="{{ _route('user.profile', ['uid' => $user->id]) }}">{{ $user->fullname() }}</a></div>
									<div class="profile-title">{{ $user->profile->title }}</div>
								</div>
							</div>

							<div class="clearfix"></div>
			            </div>
			        </div><!-- .job-top-section -->

	            	<div class="p-4">
		            	<div class="row mb-4">
		            		<div class="col-sm-6">
		            			<div class="contract-subject hourly-title hidden">
		            				<i class="fa icon-hotel-restaurant-003 u-line-icon-pro pull-left"></i>
		            				<span class="pull-left">{{ trans('common.hourly_contract') }}</span>
		            				<div class="clearfix"></div>
		            			</div>
		            			<div class="contract-subject fixed-title hidden">
		            				<i class="fa hs-admin-pin-2 pull-left"></i>
		            				<span class="pull-left">{{ trans('common.fixed_contract') }}</span>
		            				<div class="clearfix"></div>
		            			</div>
		            		</div>
			            	<div class="col-sm-6 mb-2 switch-link text-right">
			            		<a id="switch_to_fixed_link" class="btn btn-link btn-switch-link hidden">{{ trans('job.switch_to_fixed') }}</a>
			            		<a id="switch_to_hourly_link" class="btn btn-link btn-switch-link hidden">{{ trans('job.switch_to_hourly') }}</a>
			            	</div>
			            </div>

						<div class="hire-request-form">
							@if ( $proposal )
							<form id="form_hire_form" method="post" action="{{ _route('job.hire', ['id' => $job->id, 'uid' => $user->id, 'pid' => $proposal->id])}}" enctype="multipart/form-data">
							@else
							<form id="form_hire_form" method="post" action="{{ _route('job.hire', ['id' => $job->id, 'uid' => $user->id])}}" enctype="multipart/form-data">
							@endif
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
					            <input type="hidden" name="_action" value="send" />
					            <input type="hidden" id="job_type" name="job_type" value="{{ $page_submitted ? (old('job_type') == Project::TYPE_HOURLY ? '1' : '0') : ( $job->type == Project::TYPE_HOURLY ? '1' : '0') }}" />
					            
					            <div class="pb-4">
					            	<div class="mb-4 pb-2">
										<label class="control-label">{{ trans('common.contract_title') }} <span class="required">*</span></label>
										<div class="row form-group">
											<div class="col-md-12">
												<input type="input" id="contract_title" name="contract_title" class="contract-title form-control maxlength-handler" value="{{ old('contract_title') ? old('contract_title') : $job->subject }}" data-rule-required="true" maxlength="200" />
											</div>
										</div>
					            	</div><!-- .sub-section -->

					            	<div class="mb-4 pb-2">
				            			@include('pages.buyer.job.hire.form_hourly')
				            			@include('pages.buyer.job.hire.form_fixed')
									</div><!-- .sub-section -->
									
									<div class="mb-4 pl-2 pb-2">
										<label class="control-label">{{ trans('common.billing_method') }}</label>
										<div class="radiobox pt-2">
											<label>
												<input type="radio" name="payment_method" value="ijobdesk" data-value="{{ $balance }}" checked> <strong class="mr-2">{{ trans('job.my_ijobdesk_account') }}</strong>   (${{ formatCurrency($balance) }} {{ trans('common.available_now') }})
											</label>
										</div><!-- .radio-list -->
										<div class="note info">{{ trans('job.you_can_deposit_after_hire') }}</div>
									</div><!-- .sub-section -->

									<div class="mb-4">
										<label class="control-label">{{ trans('job.work_description') }}</label>
										<div class="mb-2">
		                                	<textarea id="work_description" name="description" class="form-control description maxlength-handler" maxlength="5000" rows="6" data-rule-required="true">{{ $job->desc }}</textarea>
		                                </div>
		                                <div class="mb-2">
		                                    {!! render_file_element(File::TYPE_CONTRACT) !!}
		                                </div>
		                                <div class="mb-2 info">
		                                    {{ trans('job.attachments_explanation', ['max_upload_file_size' => config('filesystems.max_upload_file_size')]) }}
		                                </div>
					  				</div><!-- .sub-section -->

									<div class="border-top pt-4 mb-4">
										<div class="chk">
											<label>
												<input class="checkbox" type="checkbox" id="agree_on_term" value="1" /> {!! trans('common.accept_terms_policy') !!}
											</label>
										</div>
					  				</div>

				  					<div>
					  					<button type="submit" id="submit_button" class="btn btn-primary" disabled>
					  						{{ trans('common.hire') }} {{ $user->fullname() }}
					  					</button>
					  					<a href="{{ $proposal ? _route('job.interviews', ['id' => $job->id]) : route('job.hire_user', ['id' => $user->id]) }}" class="btn btn-link">{{ trans('common.cancel') }}</a>
					  				</div>
				  				</div><!-- .box-section -->
							</form><!-- #form_hire_form -->
						</div><!-- .hire-request-form -->
					</div>
				</div><!-- .box-section -->
			</div>

			<div class="col-md-3 page-content">
				<div class="instruction">
					<div id="hourly_explanation" class="hidden">
						<div class="title">{{ trans('common.hourly_contract') }}</div>
						<ul class="pb-4">
							<li class="mb-2">{{ trans('job.before_work_begins_for_hourly') }}</li>
							<li class="mb-4">{{ trans('job.workdiary_capture_snapshots') }}</li>
						</ul>
					</div>
					<div id="fixed_explanation" class="hidden">
						<div class="title">{{ trans('common.fixed_contract') }}</div>
						<ul class="pb-4">
							<li class="mb-2">{{ trans('job.before_work_begins_for_fixed') }}</li>
							<li class="mb-4">{{ trans('job.create_more_milestones') }}</li>
						</ul>
					</div>
				</div>
			</div><!-- .col-md-3 -->
		</div><!-- .row -->

		<div class="hidden template-milestone">
			@include('pages.buyer.job.hire.form_fixed_milestone', ['main' => false])
		</div>
	</div><!-- .view-section -->
</div>

<!-- =========== End ===========  -->

@endsection