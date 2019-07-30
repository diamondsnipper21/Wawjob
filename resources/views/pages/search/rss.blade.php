<?php
/**
 * RSS Service Page (search/job)
 *
 * @author  - brice
 */
use iJobDesk\Models\Project;
?>
<rss version="2.0"
xmlns:content="http://purl.org/rss/1.0/modules/content/"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
>
<channel>
	<title>{{ config('app.name') }} Recent Jobs</title>
	<atom:link href="{{ route('search.rssjob') }}" rel="self" type="application/rss+xml" />
	<link>{{ route('search.rssjob') }}</link>
	<description>Search Result via {{ config('app.name') }} RSS service</description>
	<lastBuildDate>{{ $last_build_date }}</lastBuildDate>
	<language>EN-US</language>
	<sy:updatePeriod>minutely</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
	@foreach ($jobs as $job)
	<?php 
		$excerpt = strip_tags($job->desc);
		$len = mb_strlen($excerpt);
		if ($len > 200) {
			$excerpt = mb_substr($excerpt, 0, 200, 'UTF-8') . '...';
		}
	?>
	<item>
		<title>{{ $job->subject }}</title>
		<link>{{ _route('job.view', ['id'=>$job->id]) }}</link>
		<pubDate>- Posted {{ ago($job->created_at) }}</pubDate>
		<dc:creator>{{ config('app.name') }} Corporation</dc:creator>
		<guid isPermaLink="false">{{ _route('job.view', ['id'=>$job->id]) }}</guid>
		<description>
			<![CDATA[
				{{ $job->type_string() }}

				@if ( $job->type == Project::TYPE_HOURLY ) 
				- <b>{{ $job->workload_string() }} </b>
				- {{ trans('common.budget') }}: <b> {{ $job->affordable_rate_string() }} </b>
				@elseif ($job->type == Project::TYPE_FIXED )
				- {{ trans('common.budget') }}: <b> {{ $job->price_string(true) }} </b>
				@endif
			]]>
		</description>
		<content:encoded>
			<![CDATA[
				{{ $job->type_string() }}

				@if ( $job->type == Project::TYPE_HOURLY ) 
				- <b>{{ $job->workload_string() }} </b>
				- {{ trans('common.budget') }}: <b> {{ $job->affordable_rate_string() }} </b>
				@elseif ($job->type == Project::TYPE_FIXED )
				- {{ trans('common.budget') }}: <b> {{ $job->price_string(true) }} </b>
				@endif
				<br>
				{{ $job->desc }}
			]]>
	</content:encoded>
</item>
@endforeach
</channel>
</rss>