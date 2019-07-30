<?php

use iJobDesk\Models\StaticPage;

$static_pages = StaticPage::all();
$pages = [];
foreach ($static_pages as $p)
	$pages[$p->id] = $p->slug;

?>
{{-- Start Footer --}}
<footer class="page-footer" role="footer">
	<div class="container-fluid">
		{{-- Start Footer Navigation --}}
		<div class="footer-nav">
			<div class="container">
				<div class="row">
					<div class="col-md-3 col-sm-6 col-xs-12 logos">
						<a href="/"><img src="/assets/images/common/logo_footer.png" /></a>
						<p>{{ trans('footer.logo_text') }}</p>
						<div class="row mb-4">
							<div class="col-xs-5"><img src="/assets/images/common/payments/paypal.png"></div>
							<div class="col-xs-6"><img src="/assets/images/common/payments/skrill.png"></div>
						</div>
						<div class="row mb-4">
							<div class="col-xs-5"><img src="/assets/images/common/payments/bank_transfer.png"></div>
							<div class="col-xs-6"><img src="/assets/images/common/payments/wechat.png"></div>
						</div>
						<div class="row mb-4">
							<div class="col-xs-5"><img src="/assets/images/common/payments/payoneer.png"></div>
							<div class="col-xs-6"><img src="/assets/images/common/payments/visa.png"></div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="title">
							<h4>{{ trans('footer.company_info') }}</h4>
							<hr />
						</div>
						<ul>
							<li><a href="{{ route('frontend.static_page', ['slug' => $pages[1]]) }}">{{ trans('footer.about_us') }}</a></li>
							<li><a href="{{ route('frontend.static_page', ['slug' => $pages[3]]) }}">{{ trans('footer.privacy_policy') }}</a></li>
							<li><a href="{{ route('frontend.static_page', ['slug' => $pages[4]]) }}">{{ trans('footer.terms_and_conditions') }}</a></li>
						</ul>

						@if ($current_user && $current_user->isFreelancer())
						<div class="title pt-4">
							<h4>{{ trans('footer.download') }}</h4>
							<hr />
						</div>
						<ul>
							<li><a href="{{ route('frontend.download_tools') }}">{{ trans('footer.desktop_app') }}</a></li>
						</ul>
						@endif
					</div>
					<div class="col-md-3 col-sm-6 col-xs-6">
						<div class="title">
							<h4>{{ trans('footer.help') }}</h4>
							<hr />
						</div>
						<ul>
							<li><a href="{{ route('frontend.how_it_works') }}">{{ trans('footer.how_it_works') }}</a></li>
							<li><a href="{{ route('frontend.help') }}">{{ trans('footer.faqs') }}</a></li>
							<li>
								<a href="{{ $current_user ? route('ticket.list') . '?_action=new' : route('frontend.contact_us') }}">{{ trans('footer.contact_us') }}</a>
							</li>
						</ul>
					</div>
					<div class="col-md-3 col-sm-6 col-xs-6">
						<div class="title">
							<h4>{{ trans('footer.browse') }}</h4>
							<hr />
						</div>
						<ul>
							<li><a href="{{ route('search.user') }}">{{ trans('footer.freelancer_by_skill') }}</a></li>
							<li><a href="{{ route('search.job') }}">{{ trans('footer.find_jobs') }}</a></li>
						</ul>
					</div>
				</div>

				<div class="row">
					<div class="col-md-offset-3 col-md-9">
						<hr style="height: 1px; margin-top: 0; border-top: 1px solid #202020;" />
						{{ trans('footer.address') }} : Tina tn 21-5, Kesklinna linnaosa, Tallinn, Harju maakond, Estonia
					</div>
				</div>
			</div>
		</div>{{-- End Footer Navigation --}}
		<div class="copyright">
			<div class="container">
				<div class="row">
					@if ($current_user && ($current_user->username == 'softhub' || $current_user->username == 'xiaotian1' || $current_user->username == 'xiaotian2'))
					<div class="col-md-12 text-center pb-3">
						<span class="lang en pr-2"><a href="{{ route('user.update_locale', ['lang' => 'en']) }}" class="{{ !$current_user->locale || $current_user->locale == 'en'?'active':'' }}"><img src="/assets/images/common/flags/lang_uk.png">&nbsp;&nbsp;{{ trans('common.language.en') }}</a></span>
						<span class="lang ch pl-2"><a href="{{ route('user.update_locale', ['lang' => 'ch']) }}" class="{{ $current_user->locale == 'ch'?'active':'' }}"><img src="/assets/images/common/flags/lang_cn.png">&nbsp;&nbsp;{{ trans('common.language.ch') }}</a></span>
					</div>
					@endif					
					<div class="col-md-12">
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
