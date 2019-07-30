<?php

if ( !function_exists('getJobSuccesses') ) {
	function getJobSuccesses($key = '') {
		$options = [
			''   => trans('common.any_job_success'),
			'80' => trans('search.80_up_success'),
			'90' => trans('search.90_up_success'),
		];

		return $key ? ($options[$key] ? $options[$key] : '') : $options;
	}
}

if ( !function_exists('getHourlyRates') ) {
	function getHourlyRates($key = '') {
		$options = [
			'' => trans('common.any_hourly_rate'),
			'1' => trans('search.below_10'),
			'2' => trans('search.between_10_30'),
			'3' => trans('search.between_30_60'),
			'4' => trans('search.above_60')
		];

		return $key ? ($options[$key] ? $options[$key] : '') : $options;
	}
}

if ( !function_exists('getProfileFeedbacks') ) {
	function getProfileFeedbacks($key = '') {
		$options = [
			'4.5' => trans('search.4.5_up_star'),
			'4' => trans('search.4_up_star'),
			'3' => trans('search.3_up_star'),
			'2' => trans('search.2_up_star'),
			'1' => trans('search.1_up_star'),
		];

		return $key ? ($options[$key] ? $options[$key] : '') : $options;
	}
}

if ( !function_exists('getHourlyRates') ) {
	function getHourlyRates($key = '') {
		$options = [
			Project::RATE_BELOW_10 => trans('job.$10_and_below'),
			Project::RATE_10_30 => trans('job.$10_$30'),
			Project::RATE_30_60 => trans('job.$30_$60'),
			Project::RATE_ABOVE_60 => trans('job.$60_and_above')
		];

		return $key ? ($options[$key] ? $options[$key] : '') : $options;
	}
}

if ( !function_exists('getHoursBilled') ) {
	function getHoursBilled($key = '') {
		$options = [
			'1'	=> trans('job.at_least_1_hour_billed'),
			'100' => trans('job.at_least_100_hours_billed'),
			'1000' => trans('job.at_least_1000_hours_billed'),
		];

		return $key ? ($options[$key] ? $options[$key] : '') : $options;
	}
}

if ( !function_exists('getLastActivities') ) {
	function getLastActivities($key = '') {
		$options = [
			'2w' => trans('job.last_active_within_2_weeks'),
			'1m' => trans('job.last_active_within_1_month'),
			'2m' => trans('job.last_active_within_2_months'),
		];

		return $key ? ($options[$key] ? $options[$key] : '') : $options;
	}
}

if ( !function_exists('getEnglishLevels') ) {
	function getEnglishLevels($key = '') {
		$levels = iJobDesk\Models\Category::getEnLevels();

		if ( $key ) {
			return parse_multilang($levels[$key]['name'], App::getLocale());
		}

		return $options;
	}
}

?>