<?php
/**
* Retrieve Ticket list 
*
* @author  - so gwang
*/

use iJobDesk\Models\Ticket;
?>


@extends('layouts/default/index')

@section('content')

	<div class="title-section mb-4">
		<div class="row">
			<div class="col-sm-6">
				<i class="icon-communication-007 u-line-icon-pro title-icon"></i>
				<span class="title">{{ trans('page.' . $page . '.title') }}</span>
			</div>
			<div class="col-sm-6 create-btn">      
				<a class="btn btn-primary pull-right" data-toggle="modal" data-target="#createModal" >
					{{ trans('ticket.Create') }}
					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
				</a>
			</div>
		</div>  
	</div>

	{{ show_messages() }}

	<div id="tickets_page" class="page-content-section ticket-page no-padding">
		<form id="ticketListForm" class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('ticket.list') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="postType" value="" />
			<input type="hidden" name="postTicketId" value="" />
			<input type="hidden" name="tab" value="{{ $tab }}" />

			<div class="row mb-4">
				<div class="col-md-6">
					<div class="search-section">
						<div class="input-group">
							<input id="search_title" name="search_title" class="form-control border-right-0" type="text" placeholder="{{ trans('common.search') }}" value="{{ $search_title }}"/>
							<span class="input-group-addon bg-transparent p-0">
								<button type="submit" class="btn bg-transparent p-0 ml-2 mr-2"><i class="icon-magnifier"></i></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-6">      
					<div class="row">
						<div class="col-sm-offset-4 col-sm-4 text-right">
							<label class="mt-2">{{ trans('common.filter_by') }}</label>
						</div>
						<div class="col-sm-4">
							<select class="form-control select2" name="sort" id="sortSel">
								<option value=""> - {{ trans('ticket.modal.select') }} - </option>
								@foreach ($optionTypeArry as $key=>$optionType)
								<option value="{{$optionType}}" {{ ($sort == $optionType) ? 'selected' : '' }}>{{ trans('common.' . $key) }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>

			<ul class="nav nav-tabs">
				<li role="presentation" class="{{$tab == 'opening' ? 'active' : ''}}" >
					<a href="{{ route('ticket.list', ['tab' => 'opening']) }}">
						{{ trans('ticket.Open') }}
					</a>
				</li>
				<li role="presentation" class="{{$tab == 'closed' ? 'active' : ''}}">
					<a href="{{ route('ticket.list', ['tab' => 'closed']) }}">
						{{ trans('ticket.archived') }}
					</a>
				</li>
			</ul>
			<div class="tab-content">
				@if (count($tickets))

					<div class="row margin-bottom-5">
						<div class="col-sm-5">
							{!! render_pagination_desc('common.showing_of_tickets', $tickets) !!}
						</div>
						<div class="col-sm-2 text-center">
							<b>{{ trans('ticket.modal.type') }}</b>
						</div>
						<div class="col-sm-1 text-center">
							<b>{{ trans('ticket.status') }}</b>
						</div>
						<div class="col-sm-1 text-center">
							<b>{{ trans('ticket.new') }}</b>
						</div>
						<div class="col-sm-1 text-center">
							<b>{{ trans('ticket.messages') }}</b>
						</div>
					</div>
					<div class="list-group mb-0">
						@foreach ($tickets as $id => $ticket)
							@include('pages.ticket.ticket')
						@endforeach
					</div>
					<div class="list-group-label pull-left pt-4">
						{!! render_pagination_desc('common.showing_of_tickets', $tickets) !!}
					</div>
					<div class="pull-right">
						{!! $tickets->render() !!}
					</div>
					<div class="clearfix"></div>
				@else
					<div class="not-found-result">
		                <div class="text-center">
		                    <div class="heading">
		                    	@if($tab == 'opening')
									{{ trans('ticket.you_have_no_opening_tickets') }}
								@else
									{{ trans('ticket.you_have_no_archived_tickets') }}
								@endif
		                    </div>
		                </div>
			        </div>
				@endif
			</div>
		</form>
	</div>
	@include('pages.ticket.modal.create')

@endsection