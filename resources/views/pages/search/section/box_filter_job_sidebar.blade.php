<?php

use iJobDesk\Models\Project;

?>
<div class="box-filters bg-white show">
	<div class="title mb-4">
	    <span class="pull-left">{{ trans('common.filter_by') }}</span>
	    @if ( $filtered )
        <a href="{{ route('search.job') }}" class="mt-1 pull-right">{{ trans('common.clear_filters') }}</a>
        @endif
	    <div class="clearfix"></div>
	</div>

	<!-- Category -->
	<div class="mb-4">
	    <label class="control-label">{{ trans('common.categories') }}</label>
	    <div class="box-content">
	        <div>
	            <select class="form-control select2" name="c" id="main_category">
	                <option value="" {{ old('c') == '' ? 'selected' : '' }}>{{ trans('common.any_category') }}</option>
	                @foreach ($categoryTreeList as $category)
	                <option value="{{ $category['id'] }}" {{ $category['id'] == old('c') ? 'selected' : '' }}>{{ parse_multilang($category['name'], App::getLocale()) }}</option>
	                @endforeach
	            </select> 
	        </div>

	        <div id="sub_category_list" class="mt-4">
	            <div class="chk" @if ($main_category_id == '') style="display:none;" @endif>
	                <label><input type="checkbox" name="ac" id="all_check" {{ (!empty(old('ac')) || old('cs') == '') ? 'checked' : '' }} value="1">
	                <span>{{ trans('search.all_subcategories') }}</span></label>
	            </div>
	            @foreach ($categoryTreeList as $cix => $category)
	                @if ( isset($category['children']) && $category['children'] )
	                <div class="sub-category checkbox-list" id="sub_category_{{ $category['id'] }}" data-id="{{ $category['id'] }}" 
	                    @if ( !empty(old('c')) )
	                        @if ( old('c') != $cix ) style="display:none;" @endif
	                    @else
	                        @if ($cix > 0) style="display:none;" @endif
	                    @endif
	                    >
	                    @foreach ($category['children'] as $subCategory)
	                    <div class="chk">
	                        <label>
	                            <input type="checkbox" name="cs[]" value="{{ $subCategory['id'] }}" 
	                            @if ( (old('cs') != '' && in_array($subCategory['id'], explode(',', old('cs'))) ) || old('cs') == '' ) checked @endif/>
	                            <span>{{ parse_multilang($subCategory['name'], App::getLocale()) }} ({{ $subCategory['cnt_projects'] }})</span>
	                        </label>
	                    </div>
	                    @endforeach
	                </div>
	                @endif
	            @endforeach
	        </div>              
	    </div>  
	</div>  

	<!-- Type -->
	<div class="mb-4">
	    <label class="control-label">{{ trans('common.job_type') }}</label>
	    <div class="box-content">
	        <div class="checkbox-list">
	            @foreach($jobTypes as $key => $jobType)
	            <div class="chk">
	                <label>
	                    <input type="checkbox" name="t[]" value="{{ $key }}" 
	                    {{ $key === 0 ? 'id=type_fixed' : ''}}
	                    {{ $key === 1 ? 'id=type_hourly' : ''}}
	                    @if ( (old('t') != '' && in_array($key, explode(',', old('t')))) || old('t') == '' || $defaultSearched ) checked @endif>
	                    <span>{{ $jobType['title'] }} ({{ $jobType['count'] }})</span>
	                </label>
	            </div>
	            @endforeach
	        </div>
	    </div>  
	</div>

	@if ($job_prices)
	<!-- Price -->
	<div class="mb-4">
	    <label class="control-label">{{ trans('common.budget') }}</label>
	    <div class="box-content">
	        <div class="checkbox-list">
	            @foreach($job_prices as $key => $job_price)
	            <div class="chk">
	                <label>
	                    <input type="checkbox" name="p[]" value="{{ $key }}" 
	                    @if ( (old('p') != '' && in_array($key, explode(',', old('p')))) || old('p') == '' || $defaultSearched ) checked @endif>
	                    <span>{{ $job_price['title'] }} ({{ $job_price['count'] }})</span>
	                </label>
	            </div>
	            @endforeach
	        </div>
	    </div>  
	</div>
	@endif

	<!-- Budget -->
	<div class="mb-4 {{ old('t') != '' && !in_array(Project::TYPE_HOURLY, explode(',', old('t')))?'':'hide' }} hide" id="budget_box">
	    <label class="control-label">{{ trans('common.budget') }} ({{ trans('common.fixed_price') }})</label>
	    <div class="box-content">
	        <div id="budget"></div>
	        <div class="budget-value-section">
	            <span id="budget-value-var">
	                ${{ number_format(old('min') != '' ? old('min') : '0') }} 
	                - 
	                ${{ number_format(old('max') != '' ? old('max') : '50000') }} 
	            </span>
	            <input type="hidden" id="bgt_amt_min" name="min" value="{{ old('min') }}"/>
	            <input type="hidden" id="bgt_amt_max" name="max" value="{{ old('max') }}" />
	        </div>
	    </div>  
	</div>

	<!-- Experience -->
	<div class="mb-4">
	    <label class="control-label">{{ trans('search.experience_level') }}</label>
	    <div class="box-content">
	        <div class="checkbox-list">
	            @foreach($jobExperienceLevels as $key => $jobExperienceLevel)
	            <div class="chk">
	                <label>
	                    <input type="checkbox" name="el[]" value="{{ $key }}" 
	                    @if ( (old('el') != '' && in_array($key, explode(',', old('el')))) || old('el') == '' || $defaultSearched ) checked @endif>
	                    <span>{{ $jobExperienceLevel['title'] }} ({{ $jobExperienceLevel['count'] }})</span>
	                </label>
	            </div>
	            @endforeach
	        </div>
	    </div>  
	</div>

	<!-- Duration -->
	<div class="mb-4">
	    <label class="control-label">{{ trans('common.duration') }}</label>
	    <div class="box-content">
	        <div class="checkbox-list" id="qry_dur_grp">
	            @foreach($jobDurations as $key => $jobDuration)
	            <div class="chk">
	                <label>
	                    <input type="checkbox" name="d[]" value="{{ $key }}" 
	                    @if ( (old('d') != '' && in_array($key, explode(',', old('d')))) || old('d') == '' || $defaultSearched ) checked @endif>
	                    <span>{{ $jobDuration['title'] }} ({{ $jobDuration['count'] }})</span>
	                </label>
	            </div>
	            @endforeach
	        </div>
	    </div>  
	</div>

	<!-- Workload -->
	<div class="mb-4 {{ old('t') != '' && !in_array(Project::TYPE_FIXED, explode(',', old('t')))?'':'hide' }}" id="workload_box">
	    <label class="control-label">{{ trans('common.workload') }}</label>
	    <div class="box-content">
	        <div class="checkbox-list" id="qry_workload_grp">
	            @foreach ($jobWorkloads as $key => $jobWorkload)
	            <div class="chk">
	                <label>
	                    <input type="checkbox" name="wl[]" value="{{ $key }}" 
	                    @if ( (old('wl') != '' && in_array($key, explode(',', old('wl')))) || old('wl') == '' || $defaultSearched ) checked @endif>
	                    <span>{{ $jobWorkload['title'] }} ({{ $jobWorkload['count'] }})</span>
	                </label>
	            </div>
	            @endforeach
	        </div>
	    </div>  
	</div>

	<!-- Job State -->
	<div class="mb-4">
	    <label class="control-label">{{ trans('search.job_state') }}</label>
	    <div class="box-content">
	        <div class="radiobox-list" id="qry_job_state">
	            <div class="radiobox">
		            <label>
		                <input type="radio" name="st" value="2" {{ old('st') == '2' ? 'checked' : '' }}>
		                {{ trans('search.all_open_jobs') }}
		            </label>
		        </div>
		        <div class="radiobox">
		            <label>
		                <input type="radio" name="st" value="1" {{ old('st') == '1' || $defaultSearched ? 'checked' : '' }}>
		                {{ trans('search.all_open_and_jobs') }}
		            </label>
		        </div>
	        </div>
	    </div>  
	</div>
</div>