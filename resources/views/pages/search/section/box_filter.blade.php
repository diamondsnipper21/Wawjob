<?php
/**
  * @author Ro Un Nam
 */
use iJobDesk\Models\Project;
use iJobDesk\Models\Category;
use iJobDesk\Models\Country;
use iJobDesk\Models\Language;
?>
<div class="search-section">
    <div class="row">
        <div class="col-sm-6 col-xs-8">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="{{ trans('common.search') }}..." name="q" value="{{ old('q') ? old('q') : '' }}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-search">
                        <i class="icon icon-magnifier"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-sm-6 col-xs-4">
            <button type="button" class="btn btn-normal btn-filters"><i class="fa fa-filter"></i> {{ trans('common.filters') }}</button>
        </div>
    </div>
</div><!-- .search-section -->

<?php
    $c_labels = '';
    $l_labels = '';
?>

@if ( $filtered )
    <div class="filters">
        @if ( $params['c'] )
        <?php
            $c = Category::find($params['c']);
            $url_params = $params;
            unset($url_params['c']);
        ?>
            @if ( $c )
            <label class="item">
                {{ trans('common.category') }}: {{ parse_multilang($c->name) }}
                <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
            </label>
            @endif
        @endif

        @if ( $params['js'] )
        <?php
            $url_params = $params;
            unset($url_params['js']);
        ?>
        <label class="item">
            {{ trans('common.job_success') }}: {{ getJobSuccesses($params['js']) }}
            <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
        </label>
        @endif

        @if ( $params['f'] )
        <?php
            $url_params = $params;
            unset($url_params['f']);
        ?>
        <label class="item">
            {{ trans('common.feedback') }}: {{ getProfileFeedbacks($params['f']) }}
            <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
        </label>
        @endif

        @if ( $params['hr'] )
        <?php
            $url_params = $params;
            unset($url_params['hr']);
        ?>
        <label class="item">
            {{ trans('common.hourly_rate') }}: {{ getHourlyRates($params['hr']) }}
            <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
        </label>
        @endif

        @if ( $params['hb'] )
        <?php
            $url_params = $params;
            unset($url_params['hb']);
        ?>
        <label class="item">
            {{ trans('common.hours_billed') }}: {{ getHoursBilled($params['hb']) }}
            <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
        </label>
        @endif

        @if ( $params['a'] )
        <?php
            $url_params = $params;
            unset($url_params['a']);
        ?>
        <label class="item">
            {{ trans('job.last_activity') }}: {{ getLastActivities($params['a']) }}
            <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
        </label>
        @endif

        @if ( $params['el'] )
        <?php
            $url_params = $params;
            unset($url_params['el']);
        ?>
        <label class="item">
            {{ trans('job.english_level') }}: {{ getEnglishLevels($params['el']) }}
            <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
        </label>
        @endif

        {{-- Country  --}}
        @if ( $params['l'] )
            @foreach (explode(',', $params['l']) as $i)
                <?php
                    $c = Country::getCountryByCode($i);
                    if ( $c ) {
                        if ( $c_labels ) {
                            $c_labels .= ', ';
                        }
                        $c_labels .= $c->name;
                    }
                ?>
            @endforeach
            
            @if ( $c_labels )
            <label class="item">{{ trans('common.location') }}: {{ $c_labels }}           
                <?php
                    $url_params = $params;
                    unset($url_params['l']);
                ?>
                <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>            
            </label>
            @endif
        @endif

        @if ( $params['t'] )
        <?php
            $url_params = $params;
            unset($url_params['t']);
        ?>
        <label class="item">{{ trans('common.title') }}: {{ $params['t'] }}
            <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
        </label>
        @endif

        {{-- Language  --}}
        <?php
            $l_labels = '';
        ?>
        @if ( $params['ln'] )
            @foreach (explode(',', $params['ln']) as $i)
                <?php
                    $l = Language::find($i);
                    if ( $l ) {
                        if ( $l_labels ) {
                            $l_labels .= ', ';
                        }
                        $l_labels .= $l->name;
                    }
                ?>
            @endforeach
            @if ( $l_labels )
            <?php
                $url_params = $params;
                unset($url_params['ln']);
            ?>
            <label class="item">{{ trans('profile.languages') }}: {{ $l_labels }}
                <a href="{{ $page_route }}?{{ makeUrlParams($url_params) }}"><i class="fa fa-times"></i></a>
            </label>
            @endif
        @endif

        <a href="{{ $page_route }}" class="btn btn-link">{{ trans('common.clear_filters') }}</a>
    </div>
@endif

<div class="box-filters default-boxshadow pb-4 mb-4">
    <div class="row mb-4">
        <!-- Category Options -->
        <div class="col-sm-3">
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
        </div><!-- .col-sm-3 -->

        <div class="col-sm-3">
            <label class="control-label">{{ trans('common.title') }}</label>
            <input type="text" class="form-control" name="t" id="title" placeholder="{{ trans('job.find_freelancers_by_title') }}" value="{{ old('t') ? old('t') : '' }}">
        </div><!-- .col-sm-3 -->
    </div>

    <div class="row mb-4">
        <div class="col-sm-3">
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
        </div><!-- .col-sm-3 -->

        <div class="col-sm-3">
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
        </div><!-- .col-sm-3 -->

        <div class="col-sm-3">
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
        </div><!-- .col-sm-3 -->

        <div class="col-sm-3">
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
        </div><!-- .col-sm-3 -->
    </div>

    <div class="row mb-4">
        <div class="col-sm-3">
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
        </div><!-- .col-sm-3 -->

        <div class="col-sm-3">
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
        </div><!-- .col-sm-3 -->

        <div class="col-sm-3 col-md-offset-3">
            <label class="control-label">{{ trans('job.other_languages') }}</label>
            <select class="form-control select2-ajax" id="languages" name="ln" data-placeholder="{{ trans('job.find_different_language') }}" data-url="{{ route('job.search_languages.ajax') }}" data-maximum-selection-length="5">
                <option value="{{ old('ln') }}">{{ $l_labels }}</option>
            </select>
        </div><!-- .col-sm-3 -->
    </div><!-- .row -->

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-apply">{{ trans('common.apply_filters') }}</button>
            <button type="button" class="btn btn-normal btn-cancel">{{ trans('common.close') }}</button>
        </div>
    </div>
</div>