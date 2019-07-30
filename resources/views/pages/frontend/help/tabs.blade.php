<?php

/**
 * @author KCG
 * @since Mar 8, 2018
 */

use iJobDesk\Models\HelpPage;

?>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	@foreach (HelpPage::pages($type, 0) as $i => $help)
	<li role="presentation" class="{{ $i == 0?'active':'' }}">
		<a href="#help_{{ $type }}_{{ $help->id }}" role="tab" data-toggle="tab">{{ parse_json_multilang($help->title) }}</a>
	</li>
	@endforeach
</ul>