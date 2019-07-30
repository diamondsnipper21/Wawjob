<div class="education item clearfix" data-index="{{ $i }}">
	<div class="col-sm-12">
		<div class="education-degree item-title">{{ $education->degree }}</div>
		@if ($education->major)
		<div class="education-major">{{ $education->major }}</div>
		@endif
		<div class="education-date"><strong class="education-school">{{ $education->school }}</strong>&nbsp;&nbsp;{{ $education->from }}&nbsp;~&nbsp;{{ $education->to }}</div>
		
		<div class="education-desc">{!! render_more_less_desc($education->description, 300) !!}</div>
		
		<div class="action-buttons">
			<a href="javascript:void(0)" class="edit-item-action"><i class="fa icon-pencil"></i></a>
			<a href="{{ route('user.my_profile.delete') }}" class="trash remove-item-action"><i class="hs-admin-trash"></i></a>
		</div>
	</div>
</div>