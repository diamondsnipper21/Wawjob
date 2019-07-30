<div class="help-page-detail">
	{!! Breadcrumbs::render('help_detail', $help_page) !!}

	<h2>{{ parse_json_multilang($help_page->title) }}</h2>
	<div class="help-page-content">{!! parse_json_multilang($help_page->content) !!}</div>

	@include('pages.frontend.help.login')
</div>