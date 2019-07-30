<?php use Illuminate\Support\Str;
/**
* Show all freelancers.
*
* @author KCG
* @since May 29, 2017
* @version 1.0 Show all freelancers.
* @return Response
*/
?>
@extends('layouts/default/index')

@section('content')

<div id="freelancer_user_page" class="page-content-section freelancer-user-page no-padding">
	<form id="search_form" action="{{ route('search.user') }}">

	    <div class="row">
	        <div class="col-sm-3">
	        	<div class="default-boxshadow bg-white search-user-left">
			        @include ('pages.search.section.box_filter_sidebar')
	        	</div>
	        </div><!-- .col-md-4 -->

	        <div class="col-sm-9">

	        	<div class="default-boxshadow bg-white search-user-right">

			    	<div class="title-section border-0 pb-4">
			    	    <span class="title">{{ trans('page.' . $page . '.title') }}</span>
			    	</div>

			    	<div class="row">
			    		<div class="col-sm-9">
				            <div class="input-group">
				                <input class="form-control" type="text" placeholder="{{ trans('search.search_freelancers') }}" name="q" id="keyword" value="{{ old('q') ? old('q') : '' }}">
				                <span class="input-group-btn">
				                    <button type="submit" class="btn btn-primary btn-search">
				                        <i class="icon icon-magnifier"></i>
				                    </button>
				                </span>
				            </div>
				        </div>
			    	</div>

			        <div class="pt-4 pb-2">
			        	{!! render_pagination_desc('common.showing_of_results', $users) !!}
			        </div>

			        <div id="result" class="pt-4 border-top">
			            @if ( !$users->isEmpty() ) 
			                @include ('pages.search.userResult')  
			            @else
			    		<div class="not-found-result">
			    		    <div class="row">
			    		        <div class="col-md-12 text-center">
			    		            <div class="heading">{{ trans('search.nothing_found_freelancer') }}</div>
			    		        </div>
			    		    </div>
			    		</div> 
			            @endif
			        </div>

			        <div class="text-right">
			            {!! $users->appends(Request::input())->render() !!}
			        </div>
			    </div>

		    </div><!-- .col-md-8 -->

		</div>
	</form>
</div>

@endsection