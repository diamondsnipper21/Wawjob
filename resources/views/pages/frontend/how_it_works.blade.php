@extends('layouts/frontend/index', ['fullwidth' => true])

@section('css')

@endsection

@section('content')
<div class="row">
  	<div class="col-xs-12">
     	<div class="">
	        <div class="content margin-top-10">
                <div class="tabs">
                    <div>
                        <a href="#buyer_content" class="active">{{ trans('how_it_works.buyer.tab') }}</a>
                        <a href="#freelancer_content">{{ trans('how_it_works.freelancer.tab') }}</a>
                    </div>
                </div>
            </div>
            <div id="buyer_content" class="tab-content active">
                @include('pages.frontend.how_it_works.buyer')
            </div>
            <div id="freelancer_content" class="tab-content">
                @include('pages.frontend.how_it_works.freelancer')
            </div>
   		</div>
	</div>
</div>
@endsection