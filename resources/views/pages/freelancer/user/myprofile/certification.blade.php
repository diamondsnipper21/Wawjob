<div class="certification item clearfix" data-index="{{ $i }}">
	<div class="col-sm-12">
		<strong class="cert-title item-title">
			@if (empty(trim($certification->url)))
				{{ $certification->title }}
			@else
				<a href="{{ $certification->url }}" target="_blank">{{ $certification->title }}</a>
			@endif
		</strong>
		<div class="cert-date">{{ format_date('M', "1990-{$certification->month}-23") }}&nbsp;{{ format_date('Y', "{$certification->year}-05-20") }}</div>
		<div class="cert-desc">{!! render_more_less_desc($certification->description, 300) !!}</div>

		<div class="action-buttons">
			<a href="javascript:void(0)" class="edit-item-action"><i class="fa icon-pencil"></i></a>
			<a href="{{ route('user.my_profile.delete') }}" class="trash remove-item-action"><i class="hs-admin-trash"></i></a>
		</div>
	</div>
</div>