<div class="section-content box-section">

@if ( count($open_contracts) > 0 )
    <div class="row box-header title">
        <div class="col-xs-5 col-xs-12">
            {!! render_pagination_desc('common.showing_of_contracts', $open_contracts) !!}
        </div>
        <div class="col-xs-5 col-xs-12">
            <div class="row">
                <div class="col-sm-5 text-center">{{ trans('common.terms') }}</div>
                <div class="col-sm-7">{{ trans('common.contractor') }}</div>
            </div>
        </div>
        <div class="col-xs-2"></div>
    </div>

    @foreach ( $open_contracts as $contract )
        @include ('pages.buyer.job.section.contract')
    @endforeach

    <div class="row row-pagination box-pagination">
        <div class="col-md-6">
            {!! render_pagination_desc('common.showing_of_contracts', $open_contracts) !!}
        </div>

        <div class="col-md-6 text-right">
            {!! $open_contracts->appends(['type' => 'contract'])->render() !!}
        </div>
    </div>
@else
    <div class="not-found-result">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="heading">{{ trans('contract.you_have_no_open_contracts') }}</div>
            </div>
        </div>
    </div>
@endif
</div>