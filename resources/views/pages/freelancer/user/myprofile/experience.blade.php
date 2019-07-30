<div class="experience item clearfix" data-index="{{ $i }}">
	<div class="col-sm-12">
		<strong class="experience-title item-title">{{ $experience->title }}</strong>
		<div class="experience-desc">{!! render_more_less_desc($experience->description, 300) !!}</div>
		
		<div class="action-buttons">
			<a href="javascript:void(0)" class="edit-item-action"><i class="fa icon-pencil"></i></a>
			<a href="{{ route('user.my_profile.delete') }}" class="trash remove-item-action"><i class="hs-admin-trash"></i></a>
		</div>
	</div>
</div>