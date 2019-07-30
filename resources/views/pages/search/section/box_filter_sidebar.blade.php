<?php
/**
  * @author Ro Un Nam
 */
use iJobDesk\Models\Project;
use iJobDesk\Models\Category;
use iJobDesk\Models\Country;
use iJobDesk\Models\Language;
?>

<div class="box-filters bg-white show">
    <div class="title mb-4">
        <span class="pull-left">{{ trans('common.filter_by') }}</span>
        @if ( $filtered )
        <a href="{{ route('search.user') }}" class="mt-1 pull-right">{{ trans('common.clear_filters') }}</a>
        @endif
        <div class="clearfix"></div>
    </div>
    
    <!-- Category Options -->
    <div class="mb-4">
    	<label class="control-label">{{ trans('common.categories') }}</label>
        <select class="form-control select2-project-category" id="category" name="c">
            <option value="">{{ trans('common.any_category') }}</option>
            @foreach(Category::projectCategories() as $id => $category1)
            <option value="{{ $category1['id'] }}" {{ old('c') == $category1['id'] ? 'selected' : '' }} >{{ parse_multilang($category1['name']) }}</option>
                @if ( isset($category1['children']) && is_array($category1['children']) )
                    @foreach($category1['children'] as $id=>$category2)
                    <option value="{{ $category2['id'] }}" {{ old('c') == $category2['id'] ? 'selected' : ''  }} data-parent="{{ $category1['id'] }}" data-caption-without-parent="1">&nbsp;&nbsp;&nbsp;&nbsp;{{ parse_multilang($category2['name']) }}</option>
                    @endforeach
                @endif
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="control-label">{{ trans('common.title') }}</label>
        <input type="text" class="form-control" name="t" id="title" placeholder="{{ trans('job.find_freelancers_by_title') }}" value="{{ old('t') ? old('t') : '' }}">
    </div>

    <div class="mb-4">
		<label class="control-label">{{ trans('common.hourly_rate') }}</label>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="hr" value="" {{ old('hr') == "" ? "checked" : "" }}>
		        {{ trans('common.any_hourly_rate') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="hr" value="{{ Project::RATE_BELOW_10 }}" {{ old('hr') == (string)Project::RATE_BELOW_10 ? "checked" : "" }}>
		        {{ trans('job.$10_and_below') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="hr" value="{{ Project::RATE_10_30 }}" {{ old('hr') == (string)Project::RATE_10_30 ? "checked" : "" }}>
		        {{ trans('job.$10_$30') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="hr" value="{{ Project::RATE_30_60 }}" {{ old('hr') == (string)Project::RATE_30_60 ? "checked" : "" }}>
		        {{ trans('job.$30_$60') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="hr" value="{{ Project::RATE_ABOVE_60 }}" {{ old('hr') == (string)Project::RATE_ABOVE_60 ? "checked" : "" }}>
		        {{ trans('job.$60_and_above') }}
		    </label>
		</div>
    </div>

    <div class="mb-4">
		<label class="control-label">{{ trans('common.job_success') }}</label>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="js" value="" {{ old('js') == "" ? "checked" : "" }}>
		        {{ trans('common.any_job_success') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="js" value="90" {{ old('js') == 90 ? "checked" : "" }}>
		        {{ trans('job.90%_job_success_up') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="js" value="80" {{ old('js') == 80 ? "checked" : "" }}>
		        {{ trans('job.80%_job_success_up') }}
		    </label>
		</div>
    </div>

    <div class="mb-4">
    	<label class="control-label">{{ trans('common.ratings') }}</label>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="f" value="" {{ old('f') == "" ? "checked" : "" }}>
		        {{ trans('common.any_rating') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="f" value="4.5" {{ old('f') == 4.5 ? "checked" : "" }}>
		        {{ trans('search.4.5_up_star') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="f" value="4" {{ old('f') == 4 ? "checked" : "" }}>
		        {{ trans('search.4_up_star') }}
		    </label>
		</div>
		<div class="radiobox">
		    <label>
		        <input type="radio" name="f" value="3" {{ old('f') == 3 ? "checked" : "" }}>
		        {{ trans('search.3_up_star') }}
		    </label>
		</div>
    </div>

    <div class="mb-4">
        <label class="control-label">{{ trans('job.english_level') }}</label>
        <div class="radiobox">
            <label>
                <input type="radio" name="el" value="" {{ old('el') == "0" || old('el') == "" ? "checked" : "" }}>
                {{ trans('job.any_english_level') }}
            </label>
        </div>

        @foreach ( Category::getEnLevels() as $k => $v )
        <div class="radiobox">
            <label>
                <input type="radio" name="el" value="{{ $k }}" {{ old('el') == $k ? "checked" : "" }}>
                {{ parse_multilang($v['name'], App::getLocale()) }}
            </label>
        </div>
        @endforeach
    </div>

    <div class="mb-4">
        <label class="control-label">{{ trans('job.other_languages') }}</label>
        <select class="form-control select2-ajax" id="languages" name="ln" data-placeholder="{{ trans('job.find_different_language') }}" data-url="{{ route('job.search_languages.ajax') }}" data-maximum-selection-length="5" data-allow-clear="true">
            <option value="{{ old('ln') }}" selected="selected"></option>
        </select>
    </div>

    <div class="mb-4">
        <label class="control-label">{{ trans('common.hours_billed') }}</label>
        <div class="radiobox">
            <label>
                <input type="radio" name="hb" value="" {{ old('hb') == "" ? "checked" : "" }}>
                {{ trans('common.any_hours') }}
            </label>
        </div>
        <div class="radiobox">
            <label>
                <input type="radio" name="hb" value="1" {{ old('hb') == "1" ? "checked" : "" }}>
                {{ trans('job.at_least_1_hour_billed') }}
            </label>
        </div>
        <div class="radiobox">
            <label>
                <input type="radio" name="hb" value="100" {{ old('hb') == "100" ? "checked" : "" }}>
                {{ trans('job.at_least_100_hours_billed') }}
            </label>
        </div>
        <div class="radiobox">
            <label>
                <input type="radio" name="hb" value="1000" {{ old('hb') == "1000" ? "checked" : "" }}>
                {{ trans('job.at_least_1000_hours_billed') }}
            </label>
        </div>
    </div>

    <div class="mb-4">
        <label class="control-label">{{ trans('job.last_activity') }}</label>
        <div class="radiobox">
            <label>
                <input type="radio" name="a" value="" {{ old('a') == '' ? 'checked' : '' }}>
                {{ trans('common.any_time') }}
            </label>
        </div>
        <div class="radiobox">
            <label>
                <input type="radio" name="a" value="2w" {{ old('a') == '2w' ? 'checked' : '' }}>
                {{ trans('job.last_active_within_2_weeks') }}
            </label>
        </div>
        <div class="radiobox">
            <label>
                <input type="radio" name="a" value="1m" {{ old('a') == '1m' ? 'checked' : '' }}>
                {{ trans('job.last_active_within_1_month') }}
            </label>
        </div>
        <div class="radiobox">
            <label>
                <input type="radio" name="a" value="2m" {{ old('a') == '2m' ? 'checked' : '' }}>
                {{ trans('job.last_active_within_2_months') }}
            </label>
        </div>
    </div>
</div>