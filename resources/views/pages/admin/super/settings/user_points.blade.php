<?php
/**
* Fees on Super Admin
*/

use iJobDesk\Models\Settings;
?>
@extends('layouts/admin/super')

@section('content')

<div id="user_points">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Freelancer Rankings</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-horizontal" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
	    		<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10 pl-4">
    				<p>Here comes the Freelancer Ranking Algorithm. After changing the weights, you should run Cronjob to apply them.</p>
    				<strong>
    					∑ Profile Type Item's Weight + <br />	
						∑ Earnings in a closed contract * Score Per Dollar * (Review Score / 5) * (Job Success Score / 100) * Review Coeff + <br />
						∑ Activity Type Item's Weight + <br />
						∑ Earnings in an open contract * Score Per Dollar * Open Contract Coeff	
					</strong>
    			</div>

			    <div class="row margin-bottom-10">
			        <div class="col-md-6 pull-right">
			        	<div class="toolbar toolbar-table pull-right">
							<button type="button" class="btn blue button-submit">Update</button>
						</div>
			        </div>
			    </div>

	            <div class="table-container">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr role="row" class="heading">
								<th>Item</th>
								<th width="25%">Type</th>
								<th width="25%">Weighit</th>
								<th width="20%">Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<label class="control-label ml-2">Portrait</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you uploaded your portrait"></i>
								</td>
								<td class="text-center">Profile</td>
								<td><input type="text" class="form-control" id="point_portrait" name="point_portrait" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_PORTRAIT') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_portrait_enabled" {{ Settings::get('POINT_PORTRAIT_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Portfolio</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you add at least a portfolio"></i>
								</td>
								<td class="text-center">Profile</td>
								<td><input type="text" class="form-control" id="point_portfolio" name="point_portfolio" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_PORTFOLIO') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_portfolio_enabled" {{ Settings::get('POINT_PORTFOLIO_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Certification</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you add at least a certification"></i>
								</td>
								<td class="text-center">Profile</td>
								<td><input type="text" class="form-control" id="point_certification" name="point_certification" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_CERTIFICATION') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_certification_enabled" {{ Settings::get('POINT_CERTIFICATION_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Employment History</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you add at least an employment history"></i>
								</td>
								<td class="text-center">Profile</td>
								<td><input type="text" class="form-control" id="point_employment_history" name="point_employment_history" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_EMPLOYMENT_HISTORY') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_employment_history_enabled" {{ Settings::get('POINT_EMPLOYMENT_HISTORY_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Education</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you add at least an education"></i>
								</td>
								<td class="text-center">Profile</td>
								<td><input type="text" class="form-control" id="point_education" name="point_education" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_EDUCATION') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_education_enabled" {{ Settings::get('POINT_EDUCATION_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">ID Verified</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you verified your ID"></i>
								</td>
								<td class="text-center">Profile</td>
								<td><input type="text" class="form-control" id="point_id_verified" name="point_id_verified" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_ID_VERIFIED') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_id_verified_enabled" {{ Settings::get('POINT_ID_VERIFIED_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">New Freelancer</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you are a newbie registered in less than 3 months from now"></i>
								</td>
								<td class="text-center">Profile</td>
								<td><input type="text" class="form-control" id="point_new_freelancer" name="point_new_freelancer" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_NEW_FREELANCER') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_new_freelancer_enabled" {{ Settings::get('POINT_NEW_FREELANCER_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Review Coeff (last 12 Months)</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="Multiplier for reviews in last 12 months"></i>
								</td>
								<td class="text-center">Earnings</td>
								<td><input type="text" class="form-control" id="point_last_12months" name="point_last_12months" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_LAST_12MONTHS') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_last_12months_enabled" {{ Settings::get('POINT_LAST_12MONTHS_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Review Coeff (older than last 12 Months)</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="Multiplier for reviews older than 12 months"></i>
								</td>
								<td class="text-center">Earnings</td>
								<td><input type="text" class="form-control" id="point_lifetime" name="point_lifetime" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_LIFETIME') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_lifetime_enabled" {{ Settings::get('POINT_LIFETIME_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Point Score Per Dollar</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="Score per dollar"></i>
								</td>
								<td class="text-center">Earnings</td>
								<td><input type="text" class="form-control" id="point_score_per_dollar" name="point_score_per_dollar" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_SCORE_PER_DOLLAR') }}"></td>
								<td class="text-center"></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Non-Review Score</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="Default review score for non-review contracts"></i>
								</td>
								<td class="text-center">Earnings</td>
								<td><input type="text" class="form-control" id="point_score_non_review" name="point_score_non_review" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_SCORE_NON_REVIEW') }}"></td>
								<td class="text-center"></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Recent Activity</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you login in last 3 days"></i>
								</td>
								<td class="text-center">Activity</td>
								<td><input type="text" class="form-control" id="point_activity" name="point_activity" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_ACTIVITY') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_activity_enabled" {{ Settings::get('POINT_ACTIVITY_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Dispute Lost</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="When you lost in a dispute"></i>
								</td>
								<td class="text-center">Activity</td>
								<td><input type="text" class="form-control" id="point_dispute" name="point_dispute" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_DISPUTE') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_dispute_enabled" {{ Settings::get('POINT_DISPUTE_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
							<tr>
								<td>
									<label class="control-label ml-2">Open Contract Coeff</label>
									<i class="icon icon-question pull-right ml-2" data-toggle="tooltip" data-placement="top" title="Multiplier for earnings in current open contracts"></i>
								</td>
								<td class="text-center">Open Jobs</td>
								<td><input type="text" class="form-control" id="point_open_jobs" name="point_open_jobs" data-rule-required="true" data-rule-number="true" value="{{ Settings::get('POINT_OPEN_JOBS') }}"></td>
								<td class="text-center"><div class="toggle-checkbox mt-3">
									<input type="checkbox" name="point_open_jobs_enabled" {{ Settings::get('POINT_OPEN_JOBS_ENABLED') == '1' ? 'checked' : '' }} value="1" />
								</div></td>
							</tr>
						</tbody>
					</table>
				</div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>

</div>

@endsection