<?php

/**
 * @author KCG
 * @since Mar 8, 2018
 */

use iJobDesk\Models\HelpPage;

?>
<div class="tab-section">
	@include('pages.frontend.help.tabs', ['type' => $type])

	<!-- Tab panes -->
	<div class="tab-content">
		@foreach (HelpPage::pages($type, 0) as $i => $help)
		<div id="help_{{ $type }}_{{ $help->id }}" role="tabpanel" class="tab-pane fade {{ $i == 0?'active in':'' }}">
			<!-- First Level -->
			@foreach (HelpPage::pages($type, $help->id) as $help_level_one)
			<div class="help-item level-one {{ $help_level_one->hasChildren()?'has-children opening':'' }}">
				<a href="{{ $help_level_one->hasChildren()?'javascript:void(0)':$help_level_one->url() }}" target="{{ $help_level_one->isOutUrl()?'_blank':'' }}">{{ parse_json_multilang($help_level_one->title) }}</a>

				<!-- Second Level -->
				@if ($help_level_one->hasChildren())
				<div class="sub-help-items">
					@foreach (HelpPage::pages($type, $help_level_one->id) as $help_level_two)
					<div class="help-item level-two {{ $help_level_two->hasChildren()?'has-children':'' }}">
						<a href="{{ $help_level_two->url() }}" target="{{ $help_level_two->isOutUrl()?'_blank':'' }}">{{ parse_json_multilang($help_level_two->title) }}</a>
					</div>
					@endforeach
				</div>
				<span><i class="icon-arrow-down"></i></span>
				@endif
			</div>
			@endforeach
		</div>
		@endforeach
	</div>
</div><!-- .tab-section -->