<div id="ended_contracts" class="multi-freelancer-needed">
    @foreach ( $end_contracts as $contract )
        @include('pages.job.detail.feedback')
    @endforeach

    @if ($ended_contract_more)
    <a href="{{ route('job.detail.feedbacks', ['user_id' => $contract->buyer_id, 'page' => 2]) }}" class="load-more-messages">{{ trans('ticket.load_more') }}</a>
    @endif

    <div class="loading">{!! render_block_ui_default_html() !!}</div>
</div> 