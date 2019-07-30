<?php

/**
 * @author KCG
 * @since Mar 8, 2018
 */

use iJobDesk\Models\HelpPage;

?>
<div class="search-results">
	<h2>{{ trans('home.help.search_results') }}</h2>

@if (!$pages->isEmpty())
	<div class="result-top-section">
		<span>{{ trans('home.help.search_result_for', ['q' => $q]) }}</span>
		{!! render_pagination_desc('common.showing', $pages) !!}
	</div>

	@foreach ($pages as $i => $help1)
		<?php $help = HelpPage::find($help1->id);  ?>
		<div class="help-item {{ $help->hasChildren()?'has-children':'' }}">
			<a href="{{ $help->url() }}">{!! str_replace($q, '<strong>'.$q.'</strong>', strip_tags(parse_json_multilang($help->title), '<strong>')) !!}</a>
		</div>
	@endforeach
	<div class="pagination-container">{!! $pages->render() !!}</div>
@else
	<div class="not-found-result text-center">
	    <div class="heading">{{ trans('common.no_articles') }}</div>
	</div>
@endif
	@include('pages.frontend.help.login')
</div>