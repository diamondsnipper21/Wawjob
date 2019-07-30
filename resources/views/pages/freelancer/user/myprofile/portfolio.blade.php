<div class="col-sm-3 col-xs-6">
	<div class="portfolio item" data-index="{{ $i }}">
		@if ($i == 0)
		<div id="grid-container" class="hide">
			<div class="cbp-item">
				<a href="{{ portfolio_url($portfolio) }}" class="portfolio-image cbp-lightbox" rel="portfolio" data-title="<strong>{{ parse_multilang($portfolio->category->name) }}</strong><br /><span class=''>{!! nl2br($portfolio->description) !!}</span>"><img src="{{ portfolio_thumb_url($portfolio) }}" /></a>
			</div>
		</div>
		@endif
		<div id="grid-container" class="grid-container">
			<div class="cbp-item">
				<a href="{{ portfolio_url($portfolio) }}" class="portfolio-image cbp-lightbox" rel="portfolio" data-title="<strong>{{ parse_multilang($portfolio->category->name) }}</strong><br /><span class=''>{!! nl2br($portfolio->description) !!}</span>"><img src="{{ portfolio_thumb_url($portfolio) }}" /></a>
			</div>
		</div>
		
		<!-- DONT USE THIS SECTION NOW -->
		@if (false)
		<div class="portfolio-title">{{ $portfolio->title }}</div>
		@endif

		<div class="portfolio-category">{{ parse_multilang($portfolio->category->name) }}</div>
		<div class="border-top"></div>

		<!-- DONT USE THIS SECTION NOW -->
		@if (false)
		<div class="portfolio-url"><a href="{{ $portfolio->url }}" target="_blank">{{ $portfolio->url }}</a></div>
		<div class="portfolio-keyword">{{ $portfolio->keyword }}</div>
		<div class="portfolio-desc">{!! render_more_less_desc($portfolio->description, 200) !!}</div>
		@endif

		<div class="action-buttons clearfix">
			<a href="javascript:void(0)" class="edit-item-action"><i class="fa icon-pencil"></i></a>
			<a href="{{ route('user.my_profile.delete') }}" class="trash remove-item-action"><i class="hs-admin-trash"></i></a>
		</div>
	</div>
</div>