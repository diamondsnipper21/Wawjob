<?php

use iJobDesk\Models\StaticPage;

$static_pages = StaticPage::all();
$pages = [];
foreach ($static_pages as $p)
	$pages[$p->id] = $p->slug;

?>
<footer class="page-footer" role="footer">
	<div class="container-fluid">
		<div class="copyright row">
			<div class="container">
				<div class="row">
					<div class="col-md-12 text-center">
						<div class="text-center">{!! trans('page.footer.copyright', ['year' => date('Y')]) !!}</div>

						<div class="text-center pt-2 fs-12">
							<a href="{{ route('frontend.static_page', ['slug' => $pages[4]]) }}">{{ trans('footer.terms') }}</a>&nbsp;&nbsp;|&nbsp;
							<a href="{{ route('frontend.static_page', ['slug' => $pages[3]]) }}">{{ trans('footer.privacy') }}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>{{-- End Footer --}}