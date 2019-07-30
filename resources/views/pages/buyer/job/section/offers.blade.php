
<div class="section-content">
    @if ( count($offers) > 0 )
    <div class="row box-header">
        <div class="col-md-5 col-sm-4 col-xs-12">
            {!! render_pagination_desc('common.showing_of_offers', $offers) !!}
        </div>
        <div class="col-md-5 col-sm-4 col-xs-12 hidden-mobile">
            <div class="row">
                <div class="col-xs-4 text-center">{{ trans('common.terms') }}</div>
                <div class="col-xs-8">{{ trans('common.contractor') }}</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-xs-12"></div>
    </div>

    @foreach ( $offers as $offer )
        @include ('pages.buyer.job.section.offer')
    @endforeach

    <div class="row row-pagination">
        <div class="col-md-6">
            {!! render_pagination_desc('common.showing_of_offers', $offers) !!}
        </div>

        <div class="col-md-6 text-right">
            {!! $offers->appends(['type' => 'offer'])->render() !!}
        </div>
    </div>
    @else
    <div class="not-found-result">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="heading">{{ trans('contract.you_have_no_offers') }}</div>
            </div>
        </div>
    </div>
    @endif
</div>