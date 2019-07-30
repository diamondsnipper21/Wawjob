<div class="send-message-form">
	<form action="{{ route('message.send', ['id' => $id]) }}" method="post" role="form" enctype="multipart/form-data" data-container="{{ !empty($container)?$container:'' }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<input type="hidden" name="_class" value="{{ $class }}" />
		<input type="hidden" name="_type" value="{{ $type }}" />
		<input type="hidden" name="_action" />
		<input type="hidden" name="_limit" value="{{ $limit }}" />

		@if (!empty($form_elements))
			@foreach ($form_elements as $key => $value)
			<input type="hidden" name="{{ $key }}" value="{{ $value }}" />
			@endforeach
		@endif

		@if ($can_send)
		<div class="form-group margin-bottom-15">
			<textarea name="message" data-rule-required="true" class="form-control maxlength-handler" maxlength="5000" rows="5"></textarea>
		</div>
		
		<div class="row">
			<div class="col-md-8">
				<div class="margin-bottom-10 attach-file-description">
					{{ trans('common.attach_file_description', ['max_upload_file_size' => config('filesystems.max_upload_file_size')]) }}
				</div>
				<div>{!! render_file_element($type) !!}</div>
			</div>
			<div class="col-md-4 text-right padding-top-20">
				<button type="button" class="btn {{ $auth_user->isAdmin()?'blue':'btn-primary' }} button-send" disabled>
					{{ trans('common.send') }}
				</button>
			</div>
		</div>
		@endif
	</form>
</div>