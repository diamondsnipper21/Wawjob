<?php
/**
* All Contracts Page (/contract/all)
* @author Ro Un Nam
* @since Jun 02, 2017
*/

use iJobDesk\Models\Contract;
?>

@extends('layouts/default/index')

@section('content')
<div id="contracts" class="contracts-list">
	<form id="contractsForm" class="form-horizontal" method="post" action="{{ route('contract.all_contracts', ['tab' => $tab]) }}">
		
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />

		<div class="page-content-section no-padding">
			<div class="view-section contracts-content-section">
				<div class="row margin-bottom-20">
					<div class="col-md-6">
						<div class="title-section">
							<i class="icon-layers title-icon"></i>
							<span class="title">{{ trans('common.contracts') }}</span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="search-section">
							<div class="input-group">
								<input type="text" value="{{ $keywords ? $keywords : "" }}" placeholder="{{ trans('common.name_or_title') }}" class="form-control border-right-0" name="keywords" />
								<span class="input-group-addon bg-transparent p-0">
									<button type="submit" class="btn bg-transparent p-0 ml-2 mr-2"><i class="icon-magnifier"></i></button>
								</span>
							</div>
						</div>
					</div>
				</div>

				{{ show_messages() }}
				<div class="tab-section">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="{{ $tab == 'active'?'active':'' }}">
							<a href="{{ route('contract.all_contracts') }}" role="tab">{{ trans('common.active') }}
							</a>
						</li>
						<li role="presentation" class="{{ $tab == 'archived'?'active':'' }}">
							<a href="{{ route('contract.all_contracts', ['tab' => 'archived']) }}" role="tab">{{ trans('common.archived') }}
								@if ( $total_closed_can_leave_feedback )
								&nbsp;<span class="badge badge-error">{{ $total_closed_can_leave_feedback }}</span>
								@endif
							</a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active">
							@include ('pages.contract.section.list_contracts')
						</div><!-- #contracts -->
					</div>
				</div><!-- .tab-section -->
			</div><!-- .view-section -->
		</div><!-- .page-content-section -->
	</form>
</div><!-- .page-content -->
@endsection