<div class="col-sm-12">
    <div class="client-name-big" data-contract="{{ $contract->id }}">
        {{ $contract->buyer->fullname() }}
    </div>
    <div class="subject-big">
        {{ $contract->title }}
    </div>
	<div class="content">
		@if ( mb_strlen($ticket_content) > 180 )
			{!! mb_substr($ticket_content, 0, 180) !!}
			<span class="more-link">
				... {{ trans('common.more') }}
			</span>
			<span class="more-text">
				{!! mb_substr($ticket_content, 180) !!}
			</span>
			<span class="less-link">
				... {{ trans('common.less') }}
			</span>
		@else
			{!! $ticket_content !!}
		@endif
	</div>
</div>