<div class="section clearfix">
    <div class="job-category rounded-item pull-left">&nbsp;&nbsp;{{ parse_multilang($job->category->name) }}</div>
    <div class="past-time pull-left">{{ trans('common.posted' )}} {{ ago($job->created_at) }}</div>
</div>
<div class="section clearfix margin-bottom-40">
    @include ('pages.job.detail.top_summary')
</div>

<div class="sub-section pl-3 pr-3">
    <div class="title margin-bottom-20">{{ trans('common.description') }}</div>
    <div class="description break margin-bottom-30">
        <div id="desc_more" class="desc">
            {!! $desc !!}
        </div>
    </div>

    @if (count($job->files) != 0)
    <div class="margin-bottom-10 clearfix">
        <div class="title">{{ trans('common.attachments') }}</div>
        {!! render_files($job->files) !!}
    </div>
    @endif

    <!-- <div class="project-term margin-bottom-10 clearfix">
        <div class="term-label pull-left"><strong>{{ trans('common.project_type') }}:</strong></div>
        <div class="term pull-left">
            {{ $job->term_string() }}
        </div>
    </div> -->

    @if ( count($job->skills) )
    <div class="project-skills margin-bottom-20 clearfix">
        <div class="title margin-bottom-10">
            <strong>{{ trans('common.required_skills') }}</strong>
        </div>
        <div>
            @foreach ( $job->skills as $skill )
            <span class="rounded-item">{{ parse_multilang($skill->name) }}</span>
            @endforeach
        </div>
    </div>
    @endif
</div>