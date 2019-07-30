<div class="employment item clearfix" data-index="{{ $i }}">
	<div class="col-sm-12">
		<strong class="employment-company item-title">{{ $employment->company }}</strong>
		<div class="employment-date">
			@if ($employment->position)
				<strong>{{ $employment->position }}</strong>&nbsp;
			@endif
			{{ format_date('M', "1990-{$employment->from_month}-23") }}&nbsp;{{ format_date('Y', "{$employment->from_year}-05-20") }}
			&nbsp;-&nbsp;
			@if (!$employment->to_present)
				{{ format_date('M', "1990-{$employment->to_month}-23") }}&nbsp;{{ format_date('Y', "{$employment->to_year}-05-20") }}
			@else
				{{ trans('common.present') }}
			@endif
		</div>
		<div class="employment-desc">{!! render_more_less_desc($employment->desc, 300) !!}</div>
		
		<div class="action-buttons">
			<a href="javascript:void(0)" class="edit-item-action"><i class="fa icon-pencil"></i></a>
			<a href="{{ route('user.my_profile.delete') }}" class="trash remove-item-action"><i class="hs-admin-trash"></i></a>
		</div>
	</div>
</div>