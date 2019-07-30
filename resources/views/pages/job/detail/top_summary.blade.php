<?php
/**
* Job Summary.
* @see job_apply.blade.php
* @see job_detail.blade.php
* @author  - KCG
*/

use iJobDesk\Models\Project;
?>
<div class="row">
    <div class="col-md-4">
        <div class="project-type-info">
        @if ($job->type == Project::TYPE_HOURLY)
            <div class="project-type">
                <div class="row">
                    <div class="col-xs-2">
                        <i class="fa icon-hotel-restaurant-003 u-line-icon-pro"></i>
                    </div>
                    <div class="col-xs-10">
                        <strong>{{ trans('common.hourly_job') }}</strong>
                        <div>
                            {{ trans('common.budget') }}: {{ $job->affordable_rate_string() }}
                        </div>
                        <div>
                            {{ trans('common.workload') }}: {{ $job->workload_string() }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="project-type">
                <div class="row">
                    <div class="col-xs-2">
                        <i class="fa hs-admin-pin-2"></i>
                    </div>
                    <div class="col-xs-10">
                        <strong>{{ trans('common.fixed_price_job') }}</strong>

                        <div>
                            {{ trans('common.budget') }}: {{ $job->price_string(true) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="project-type-info">
            <div class="row">
                <div class="col-xs-2">
                    <i class="fa icon-finance-067 u-line-icon-pro"></i>
                </div>
                <div class="col-xs-10">
                    <strong>
                        @if ( intval($job->experience_level) == Project::EXPERIENCE_LEVEL_EXPERT )
                            {{ trans('job.expert') }}
                        @elseif ( intval($job->experience_level) == Project::EXPERIENCE_LEVEL_INTERMEDIATE )
                            {{ trans('job.intermediate') }}
                        @else
                            {{ trans('job.entry') }}
                        @endif
                            &nbsp;{{ trans('common.level') }}
                    </strong>
                    <div>
                        @if ( intval($job->experience_level) == Project::EXPERIENCE_LEVEL_EXPERT )
                            {{ trans('job.expert_description') }}
                        @elseif ( intval($job->experience_level) == Project::EXPERIENCE_LEVEL_INTERMEDIATE )
                            {{ trans('job.intermediate_description') }}
                        @else
                            {{ trans('job.entry_level_description') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="project-type-info">
            <div class="project-type">
                <div class="row">
                    <div class="col-xs-2">
                        <i class="fa icon-calendar"></i>
                    </div>
                    <div class="col-xs-10">
                    @if ($job->type == Project::TYPE_HOURLY)
                        <strong>{{ trans('common.project_length') }}</strong>
                        <!-- <div> {{ format_date('M j, Y', $job->created_at) }} </div> -->

                        <div>
                            {{ $job->duration_string() }}
                        </div>
                    @else
                        <strong>{{ trans('common.project_length') }}</strong>
                        <div>{{ $job->term_string() }}</div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>