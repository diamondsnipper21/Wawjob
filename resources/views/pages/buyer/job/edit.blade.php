<?php
/**
 * Post Job Page
 *
 * @author - Ro Un Nam
 */
use iJobDesk\Models\Project;
use iJobDesk\Models\Category;
use iJobDesk\Models\File;
use iJobDesk\Models\Skill;
use iJobDesk\Models\Settings;
?>
@extends('layouts/default/index')

@section('content')
<div class="page-content-section">
	<div class="form-section job-content-section">
		<div class="row">
			<div class="col-md-9 col-sm-9 col-xs-12">
				<div class="title-section">
					<span class="title break">{{ empty($job->id) ? trans('page.buyer.job.create.sub_title') : trans('page.buyer.job.edit.sub_title', ['job' => $job->subject]) }}</span>
				</div>

				{{ show_messages() }}

				<div class="box-section border-0">
					<form id="form_job_post" class="{{ old('job_type') === (String)Project::TYPE_FIXED ? "fixed-job" : "hourly-job" }}" method="post" action="{{ !empty($job->id) ? _route('job.edit', ['id' => $job->id]) : route('job.create')}}" enctype="multipart/form-data">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="action" id="action" value="post_job">
						<input type="hidden" name="invite_to" value="{{ $invite_to }}">
						<input type="hidden" id="post_status" value="{{ empty($job->id) ? 'posting' : 'editing' }}">

						<div class="sub-title">{{ trans('job.describe_job') }}</div>

						<div class="sub-section">
							<!-- Category -->
							<div class="row">
								<div class="col-md-9">
                                    <div class="form-group">
                                        <label class="control-label" for="job_category">{{ trans('common.choose_category') }}</label> <span class="required">*</span>
                                        <select class="form-control select2-project-category" id="job_category" name="category" data-rule-required="true">
											<option value="">{{ trans('common.please_select') }}</option>
											@foreach(Category::projectCategories() as $id => $category1)
											<option value="{{ $category1['id'] }}" {{ old('category') == $category1['id'] || (old('category') == null && $job->category_id == $category1['id']) ? "selected" : ""  }} >{{ parse_multilang($category1['name']) }}</option>
												@if ( isset($category1['children']) && is_array($category1['children']) )
													@foreach($category1['children'] as $id=>$category2)
													<option value="{{ $category2['id'] }}" {{ old('category') == $category2['id'] || (old('category') == null && $job->category_id == $category2['id']) ? "selected" : ""  }} data-parent="{{ $category1['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ parse_multilang($category2['name']) }}</option>
													@endforeach
												@endif
											@endforeach
										</select>
                                    </div>
                                </div>
							</div>

							<!-- Name -->
							<div class="form-group">
								<label class="control-label">{{ trans('job.name_your_job_posting') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-9 col-sm-12">
										<input type="text" class="form-control maxlength-handler" id="job_title" name="title" placeholder="{{ trans('job.place_holder_name_your_job_posting') }}" value="{{ old('title') ? old('title') : $job->subject }}" data-rule-required="true" maxlength="200" />
									</div>
								</div>
							</div>

							<!-- Description -->
							<div class="form-group">
								<label class="control-label">{{ trans('job.job_description') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-9 col-sm-12">
										<textarea type="text" class="form-control maxlength-handler" id="description" name="description" rows="10" maxlength="5000" placeholder="{{ trans('job.place_holder_job_description') }}" data-rule-required="true">{{ old('description') ? old('description') : $job->desc }}</textarea>
									</div>
								</div>
							</div>

							<!-- File -->
							<div class="form-group">
								<label class="control-label">{{ trans('common.attach_file') }}</label>
								<label class="control-desc">{{ trans('common.attach_file_description', ['max_upload_file_size' => config('filesystems.max_upload_file_size')]) }}</label>
								<div class="row">
									<div class="col-md-9 col-sm-12">
										{!! render_file_element(File::TYPE_PROJECT, $job->files) !!}
									</div>
								</div>
							</div>

							<!-- Term -->
							<div class="form-group">
								<label class="control-label">{{ trans('job.what_type_of_project_do_you_have') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<div class="radio-box">
											<label>
												<input type="radio" name="term" id="term_one" value="1" {{(old('term') == "1" || $job->term == '1' || (!$job->id && !$page_submitted) ? "checked" : "")}} data-rule-required="true" />
												{{ trans('common.one_time_project') }}
											</label>
										</div>
										<div class="radio-box">
											<label>
												<input type="radio" name="term" id="term_ongoing" value="2" {{(old('term') == "2" || $job->term == '2' || (!$job->id && !$page_submitted) ? "checked" : "")}}>
												{{ trans('common.ongoing_project') }}
											</label>
										</div>
										<div class="radio-box">
											<label>
												<input type="radio" name="term" id="term_not_sure" value="0" {{(old('term') == "0" || $job->term == '0' || (!$job->id && !$page_submitted) || (!$job->id && !$page_submitted) ? "checked" : "")}}>
												{{ trans('common.i_am_not_sure') }}
											</label>
										</div>
									</div>
								</div>
							</div>

							<!-- Limit of freelancers -->
							<div class="form-group">
								<label class="control-label">{{ trans('job.how_many_freelancers_do_you_need_to_hire_for_this_job') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<div class="radio-box">
											<label>
												<input type="radio" name="contract_limit" id="contract_limit_one" value="1" {{ (old('contract_limit') == '1' || $job->contract_limit == '1' || (!$job->id && !$page_submitted) ? "checked" : "") }} data-rule-required="true" />
												{{ trans('job.i_want_to_hire_one_freelancer') }}
											</label>
										</div>
										<div class="radio-box">
											<label>
												<input type="radio" name="contract_limit" id="contract_limit_more" value="0" {{ (old('contract_limit') === '0' || (String)$job->contract_limit === '0' ? "checked" : "") }} />
												{{ trans('job.i_need_to_hire_more_than_one_freelancer') }}
											</label>
										</div>
									</div>
								</div>
							</div>

							<!-- Skills -->
							<div class="form-group">
								<label class="control-label">{{ trans('job.enter_skills_needed') }} ({{ trans('common.optional') }})</label>
								<div class="row">
									<div class="col-md-9 col-sm-12">
										<select id="job_skills" name="job_skills[]" class="form-control select2-ajax" data-url="{{ route('job.search_skills.ajax') }}" data-maximum-selection-length="10" multiple>
										@if ( $page_submitted && old('job_skills') )
											@foreach (old('job_skills') as $skill_id)
												<option value="{{ $skill_id }}" selected>{{ Skill::getName($skill_id) }}</option>
											@endforeach
										@else
											@foreach ($job->skills as $skill)
												<option value="{{ $skill->id }}" selected>{{ $skill->name }}</option>
											@endforeach
										@endif
										</select>
									</div>
								</div>
							</div>
						</div><!-- .sub-section -->

						<hr>

						<div class="sub-title">{{ trans('job.rate_and_availability') }}</div>

						<div class="sub-section">
							<!-- Hourly or Fixed -->
							<div class="form-group">
								<label class="control-label">{{ trans('job.how_would_you_like_to_pay') }}</label>
								<div class="row">
									<div class="col-md-4 col-sm-6">
										<select class="form-control select2" id="job_type" name="job_type" data-rule-required="true">
											<option value="{{ Project::TYPE_HOURLY }}" {{ old('job_type') === (String)Project::TYPE_HOURLY || $job->type == Project::TYPE_HOURLY ? "selected" : "" }}>{{ trans('job.pay_by_the_hour') }}</option>
											<option value="{{ Project::TYPE_FIXED }}" {{ old('job_type') === (String)Project::TYPE_FIXED || $job->type == (String)Project::TYPE_FIXED ? "selected" : "" }}>{{ trans('job.pay_a_fixed_price') }}</option>
										</select>
									</div>
								</div>
							</div>

							<!-- Budget -->
							<div class="form-group fixed-job-section">
								<label class="control-label">{{ trans('common.budget') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-5 col-sm-6">
										<select class="form-control select2" id="price" name="price">
										@foreach (Project::$str_project_price as $key => $value)
											<option value="{{ $key }}" {{ old('price') == $key  || $job->price == $key ? "selected" : "" }}>{{ Project::price_string_with_param($key) }}</option>
										@endforeach
										</select>
									</div>
								</div>                        
							</div>

							<!-- Affordable hourly rate -->
							<div class="hourly-job-section form-group">
								<label class="control-label">{{ trans('job.affordable_hourly_rate') }}</label>
								<div class="row">
									<div class="col-md-4">
										<select class="form-control select2" id="job_rate" name="affordable_rate" data-rule-required="true">
											<option value="{{ Project::RATE_NOT_SURE }}" {{ (old('affordable_rate') != '' && old('affordable_rate') == Project::RATE_NOT_SURE) || $job->affordable_rate == Project::RATE_NOT_SURE ? "selected" : "" }}>{{ trans('common.not_sure') }}</option>
											<option value="{{ Project::RATE_BELOW_10 }}" {{ old('affordable_rate') == Project::RATE_BELOW_10 || $job->affordable_rate == Project::RATE_BELOW_10 ? "selected" : "" }}>{{ trans('job.$10_and_below') }}</option>
											<option value="{{ Project::RATE_10_30 }}" {{ old('affordable_rate') == Project::RATE_10_30 || $job->affordable_rate == Project::RATE_10_30 ? "selected" : "" }}>{{ trans('job.$10_$30') }}</option>
											<option value="{{ Project::RATE_30_60 }}" {{ old('affordable_rate') == Project::RATE_30_60 || $job->affordable_rate == Project::RATE_30_60 ? "selected" : "" }}>{{ trans('job.$30_$60') }}</option>
											<option value="{{ Project::RATE_ABOVE_60 }}" {{ old('affordable_rate') == Project::RATE_ABOVE_60 || $job->affordable_rate == Project::RATE_ABOVE_60 ? "selected" : "" }}>{{ trans('job.$60_and_above') }}</option>
										</select>
									</div>
								</div>
							</div>

							<!-- Desired Experience Level -->
							<div class="form-group desired-exper-level">
								<label class="control-label">{{ trans('job.desired_experience_level') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-4 col-sm-6">
										<div class="radio-box">
											<label>
												<input type="radio" name="experience_level" id="level_entry" value="0" {{(old('experience_level') == "0" || $job->experience_level == '0' ? "checked" : "")}} data-rule-required="true" />
												{{ trans('job.entry_level') }}
											</label>
											<i class="hs-admin-help-alt pull-right" data-toggle="tooltip" title="{{ trans('job.entry_level_description') }}"></i>
										</div>
										<div class="radio-box">
											<label>
												<input type="radio" name="experience_level" id="level_intermediate" value="1" {{(old('experience_level') == "1" || $job->experience_level == '1' || (!$job->id && !$page_submitted) ? "checked" : "")}} />
												{{ trans('job.intermediate') }}
											</label>
											<i class="hs-admin-help-alt pull-right" data-toggle="tooltip" title="{{ trans('job.intermediate_description') }}"></i>
										</div>
										<div class="radio-box">
											<label>
												<input type="radio" name="experience_level" id="level_expert" value="2" {{(old('experience_level') == "2" || $job->experience_level == '2' ? "checked" : "")}} />
												{{ trans('job.expert') }}
											</label>
											<i class="hs-admin-help-alt pull-right" data-toggle="tooltip" title="{{ trans('job.expert_description') }}"></i>
										</div>
									</div>
								</div>
							</div>

							<!-- Workload -->
							<div class="hourly-job-section form-group">
								<label class="control-label">{{ trans('job.what_time_commitment_is_required_for_this_job') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<div class="radio-box">
											<label>
												<input type="radio" name="workload" id="workload_more" value="MT" {{(old('workload') == "MT" || $job->workload == 'MT' ? "checked" : "")}} data-rule-required="true" />
												{{ trans('job.more_than_30_hours_week') }}
											</label>
										</div>
										<div class="radio-box">
											<label>
												<input type="radio" name="workload" id="workload_less" value="LT" {{(old('workload') == "LT" || $job->workload == 'LT' ? "checked" : "")}}>
												{{ trans('job.less_than_30_hours_week') }}
											</label>
										</div>
										<div class="radio-box">
											<label>
												<input type="radio" name="workload" id="workload_not_sure" value="NS" {{(old('workload') == "NS" || $job->workload == 'NS' || (!$job->id && !$page_submitted) ? "checked" : "")}}>
												{{ trans('common.i_am_not_sure') }}
											</label>
										</div>
									</div>
								</div>
							</div>

							<!-- Duration -->
							<div class="form-group">
								<label class="control-label">{{ trans('job.how_long_do_you_expect_this_job_to_last') }}</label> <span class="required">*</span>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										@foreach (Project::$str_project_duration as $key => $label)
										<div class="radio-box">
											<label>
												<input type="radio" name="duration" value="{{ $key }}" {{ old('duration') == $key || $job->duration == $key || (!$job->id && !$page_submitted) ? "checked" : "" }} data-rule-required="true" />
												{{ $label }}
											</label>
										</div>
										@endforeach
									</div>
								</div>
							</div>
						</div><!-- .sub-section -->

						<hr>
						<div class="sub-title">{{ trans('common.preferences') }}</div>

						<div class="sub-section">
							<!-- Is Public -->
							<div class="form-group">
								<label class="control-label">
									<i class="icon-magnifier"></i> {{ trans('job.freelancers_to_find_and_apply_job') }}
								</label>
								<div class="row">
									<div class="col-md-12">
										<div class="radio-box">
											<label>
												<input type="radio" name="job_public" id="job_public" value="1" {{ old('job_public') == '1' || $job->is_public == '1' || (!$job->id && !$page_submitted) ? "checked" : "" }}> {{ trans('job.public_search_engines_can_find_job') }}
											</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="radio-box">
											<label>
												<input type="radio" name="job_public" id="job_public_for_ijobdesk" value="2" {{ old('job_public') == (String)Project::STATUS_PROTECTED || $job->is_public == (String)Project::STATUS_PROTECTED ? "checked" : "" }}> {{ trans('job.only_ijobdesk_users_can_find_this_job') }}
											</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="radio-box">
											<label>
												<input type="radio" name="job_public" id="job_private" value="0" {{ old('job_public') == (String)Project::STATUS_PRIVATE || $job->is_public == (String)Project::STATUS_PRIVATE ? "checked" : "" }}> {{ trans('job.only_freelancers_invited_can_find_this_job') }}
											</label>
										</div>
									</div>
								</div>
							</div>

							<!-- Preferred Qualifications -->
							<div class="form-group">
								<label class="control-label">
									<i class="icon-like"></i> {{ trans('job.preferred_qualifications') }}
								</label>
								<label class="control-desc">{{ trans('job.preferred_qualifications_description') }}</label>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<button type="button" class="btn btn-normal" id="btn_qualifications">{{ trans('job.show_qualifications') }}</button>
									</div>
								</div>

								<div class="qualifications-section">
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<label class="control-label">{{ trans('common.job_success_score') }}</label>
										</div>
										<div class="col-md-6 col-sm-12">
											<select class="form-control select2" id="job_qualification_success_score" name="qualification_success_score">
												<option value="0" {{ old('qualification_success_score') == 0 || $job->qualification_success_score == '0' ? "selected" : "" }}>{{ trans('common.any_job_success') }}</option>
												<option value="90" {{ old('qualification_success_score') == 90 || $job->qualification_success_score == '90' ? "selected" : "" }}>{{ trans('job.90%_job_success_up') }}</option>
												<option value="80" {{ old('qualification_success_score') == 80 || $job->qualification_success_score == '80' ? "selected" : "" }}>{{ trans('job.80%_job_success_up') }}</option>
											</select>
										</div>
									</div>

									<div class="row">
										<div class="col-md-4 col-sm-12">
											<label class="control-label">{{ trans('common.hours_billed_on') }}</label>
										</div>
										<div class="col-md-6 col-sm-12">
											<select class="form-control select2" id="job_qualification_hours" name="qualification_hours">
												<option value="0" {{ old('qualification_hours') == '0' || $job->qualification_hours == '0' ? "selected" : "" }}>{{ trans('common.any_amount') }}</option>
												<option value="1" {{ old('qualification_hours') == '1' || $job->qualification_hours == '1' ? "selected" : "" }}>{{ trans('job.at_least_an_hour') }}</option>
												<option value="100" {{ old('qualification_hours') == '100' || $job->qualification_hours == '100' ? "selected" : "" }}>{{ trans('job.at_least_100_hours') }}</option>
												<option value="500" {{ old('qualification_hours') == '500' || $job->qualification_hours == '500' ? "selected" : "" }}>{{ trans('job.at_least_500_hours') }}</option>
												<option value="1000" {{ old('qualification_hours') == '1000' || $job->qualification_hours == '1000' ? "selected" : "" }}>{{ trans('job.at_least_1000_hours') }}</option>
											</select>
										</div>
									</div>

									<div class="row">
										<div class="col-md-4 col-sm-12">
											<label class="control-label">{{ trans('common.location') }}</label>
										</div>
										<div class="col-md-6 col-sm-12">
											<select class="form-control select2" id="job_qualification_location" name="qualification_location">
												<option value="" {{ old('qualification_location') == '0' || $job->qualification_location == '0' ? "selected" : "" }}>{{ trans('common.any_location') }}</option>
												<option value="Africa" {{ old('qualification_location') == 'Africa' || $job->qualification_location == 'Africa' ? "selected" : "" }}>{{ trans('job.africa') }}</option>
												<option value="Asia" {{ old('qualification_location') == 'Asia' || $job->qualification_location == 'Asia' ? "selected" : "" }}>{{ trans('job.asia') }}</option>
												<option value="Australia" {{ old('qualification_location') == 'Australia' || $job->qualification_location == 'Australia' ? "selected" : "" }}>{{ trans('job.australia') }}</option>
												<option value="Europe" {{ old('qualification_location') == 'Europe' || $job->qualification_location == 'Europe' ? "selected" : "" }}>{{ trans('job.europe') }}</option>
												<option value="Middle East" {{ old('qualification_location') == 'Middle East' || $job->qualification_location == 'Middle East' ? "selected" : "" }}>{{ trans('job.middle_east') }}</option>
												<option value="North America" {{ old('qualification_location') == 'North America' || $job->qualification_location == 'North America' ? "selected" : "" }}>{{ trans('job.north_america') }}</option>
												<option value="South America" {{ old('qualification_location') == 'South America' || $job->qualification_location == 'South America' ? "selected" : "" }}>{{ trans('job.south_america') }}</option>
											</select>
										</div>
									</div>
								</div><!-- .qualifications-section -->
							</div>

							<!-- Cover Letter -->
							<div class="form-group">
								<label class="control-label">
									<i class="icon-envelope-open"></i> {{ trans('common.cover_letter') }}
								</label>
								<br>
								<label class="control-desc">{{ trans('job.cover_letter_description') }}</label>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="cv_required" id="cv_required" value="1" checked="true" {{ old('cv_required') || $job->req_cv == '1' ? "CHECKED" : "" }} />
												{{ trans('job.yes_require_cover_letter') }}
											</label>
										</div>
									</div>
								</div>
							</div>

							<!-- Is Featured -->
							<div class="form-group">
								<label class="control-label"><i class="icon-badge"></i>{{ trans('common.featured') }}</label>
								<label class="control-desc">{{ trans('job.featured_description', ['featured_job_posting_fee' => '$' . Settings::get('FEATURED_JOB_FEE')]) }}</label>
								<div class="row">
									<div class="col-md-6">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="job_featured" id="job_featured" value="1" {{ old('job_featured') || $job->is_featured == '1' ? "checked" : "" }} {{ $job->is_featured == '1' ? 'disabled' : '' }}> {{ trans('job.yes_make_this_job_featured') }}
											</label>
										</div>
									</div>
								</div>
							</div>
						</div><!-- .sub-section -->

						<hr>

						<div class="row form-group action-group">
							<div class="col-sm-10 input-field">
								@if ( $action == 'repost' )
								<button type="submit" class="btn btn-primary" id="btn_repost_job">{{ !empty($job->id)?trans('common.repost_job'):trans('common.post_job') }}</button>
								@else
								<button type="submit" class="btn btn-primary" id="btn_post_job">{{ !empty($job->id) && !$job->isDraft()?trans('common.update_job'):trans('common.post_job') }}</button>
								@endif
								&nbsp;&nbsp;&nbsp;
								@if ( empty($job->id) || $job->isDraft() )
								<button type="submit" formnovalidate class="btn btn-secondary" id="btn_save_draft">{{ trans('common.save_draft') }}</button>
								@else
								<a href="{{ _route('job.overview', ['id' => $project_id]) }}" class="btn btn-link btn-cancel">{{ trans('common.cancel') }}</a>
								@endif
							</div>
						</div>
					</form>

				</div><!-- .box-section -->

			</div><!-- col-md-9 -->

			<div class="col-sm-3 col-md-3">
				<div class="job-post-instruction">
					<div class="title">{{ trans('job.its_free_to_post_a_job') }}</div>
					<ul>
						<li>{{ trans('job.get_proposals_from') }}</li>
						<li>{{ trans('job.review_proposals_freelancer') }}</li>
						<li>{{ trans('job.interview_and_negotiate') }}</li>
						<li>{{ trans('job.send_an_offer_to_hire') }}</li>
						<li>{{ trans('job.pay_the_freelancer_once') }}</li>
					</ul>
				</div>
			</div>

		</div><!-- .row -->

	</div><!-- .job-content-section -->

</div><!-- .page-content-section -->
@endsection