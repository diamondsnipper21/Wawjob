@forelse ($attachments as $file)
<div class="attachment">
	<div class="attachment-icon"><i class="fa {{ $file->icon() }}"></i></div>
	<div class="attachment-info">
		<div class="attachment-name">{{ $file->name }}</div>
		<div class="attachment-user">{{ trans('common.by_s', ['name' => $file->user_id == $current_user->id?trans('common.you'):$file->user->fullname()]) }} - {{ format_date('M j, Y', $file->created_at) }}</div>
	</div>
	<a href="{{ file_download_url($file) }}" class="download-link" target="_blank" title="{{ $file->name }}">{{ trans('common.download') }}</a>
	<div class="clearfix"></div>
</div>
@empty
<div class="no-attachments no-data">
	{{ trans('common.no_attachments') }}
</div>
@endforelse