<?php
use iJobDesk\Models\Country;
$statusList = Country::getOptions();
?>
@extends('layouts/admin/super')

@section('content')
<div id="countries">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Countries</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($countries) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span><strong>Action</strong>&nbsp;</span>
							<select id="template_action" name="action" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="ENABLE_PAYPAL">Enable PayPal</option>
								<option value="DISABLE_PAYPAL">Disable PayPal</option>
								<option value="ENABLE_PAYONEER">Enable Payoneer</option>
								<option value="DISABLE_PAYONEER">Disable Payoneer</option>
								<option value="ENABLE_SKRILL">Enable Skrill</option>
								<option value="DISABLE_SKRILL">Disable Skrill</option>
								<option value="ENABLE_WECHAT">Enable WeChat</option>
								<option value="DISABLE_WECHAT">Disable WeChat</option>
								<option value="ENABLE_BANK">Enable Bank Transfer</option>
								<option value="DISABLE_BANK">Disable Bank Transfer</option>
								<option value="DELETE">Delete</option>
							</select>
							<button class="btn btn-sm yellow table-group-action-submit button-submit" type="button" disabled data-auto-submit="false"><i class="fa fa-check"></i> Submit</button>
						</div>
			        </div>
			    </div>
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="2%"><input type="checkbox" class="group-checkable" /></th>
								<th class="sorting{{ $sort == 'name'?$sort_dir:'' }}" data-sort="name">Name</th>
								<th class="sorting{{ $sort == 'charcode'?$sort_dir:'' }}" data-sort="charcode" width="9%">Alpha Code</th>
								<th class="sorting{{ $sort == 'country_code'?$sort_dir:'' }}" data-sort="country_code" width="10%">Country Code</th>
								<th class="sorting{{ $sort == 'sub_region'?$sort_dir:'' }}" data-sort="sub_region" width="12%">Region</th>
								<th class="sorting{{ $sort == 'paypal_enabled'?$sort_dir:'' }}" data-sort="paypal_enabled" width="9%">PayPal</th>
								<th class="sorting{{ $sort == 'payoneer_enabled'?$sort_dir:'' }}" data-sort="payoneer_enabled" width="9%">Payoneer</th>
								<th class="sorting{{ $sort == 'skrill_enabled'?$sort_dir:'' }}" data-sort="skrill_enabled" width="9%">Skrill</th>
								<th class="sorting{{ $sort == 'wechat_enabled'?$sort_dir:'' }}" data-sort="wechat_enabled" width="9%">WeChat</th>
								<th class="sorting{{ $sort == 'bank_enabled'?$sort_dir:'' }}" data-sort="creditcard_enabled" width="10%">Credit Card</th>
								<th class="sorting{{ $sort == 'bank_enabled'?$sort_dir:'' }}" data-sort="bank_enabled" width="10%">Bank Transfer</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Name -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[name]" value="{{ old('filter.name') }}" placeholder="" />
								</th>
								<!-- Code -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[charcode]" value="{{ old('filter.charcode') }}" />
								</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[country_code]" value="{{ old('filter.country_code') }}" />
								</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[sub_region]" value="{{ old('filter.sub_region') }}" />
								</th>
								<th>
									<select name="filter[paypal_enabled]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.paypal_enabled')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<select name="filter[payoneer_enabled]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.payoneer_enabled')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<select name="filter[skrill_enabled]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.skrill_enabled')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<select name="filter[wechat_enabled]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.wechat_enabled')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<select name="filter[creditcard_enabled]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.creditcard_enabled')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<select name="filter[bank_enabled]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.bank_enabled')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($countries as $c)
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $c->id }}" data-status-DELETE="true" {{ Country::enableStatusChanged($c) }} /></td>
								<td>{{ $c->name }}</td>
								<td class="text-center">{{ $c->charcode }}</td>
								<td class="text-center">{{ $c->country_code }}</td>
								<td class="text-center">{{ $c->sub_region }}</td>
								<td class="text-center"><span class="label label-{{ strtolower($statusList[$c->paypal_enabled]) }}">{{ $statusList[$c->paypal_enabled] }}</span></td>
								<td class="text-center"><span class="label label-{{ strtolower($statusList[$c->payoneer_enabled]) }}">{{ $statusList[$c->payoneer_enabled] }}</span></td>
								<td class="text-center"><span class="label label-{{ strtolower($statusList[$c->skrill_enabled]) }}">{{ $statusList[$c->skrill_enabled] }}</span></td>
								<td class="text-center"><span class="label label-{{ strtolower($statusList[$c->wechat_enabled]) }}">{{ $statusList[$c->wechat_enabled] }}</span></td>
								<td class="text-center"><span class="label label-{{ strtolower($statusList[$c->creditcard_enabled]) }}">{{ $statusList[$c->creditcard_enabled] }}</span></td>
								<td class="text-center"><span class="label label-{{ strtolower($statusList[$c->bank_enabled]) }}">{{ $statusList[$c->bank_enabled] }}</span></td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="10" align="center">No Countries</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($countries) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $countries->render() !!}</div>
                    </div>
                </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>

	<!-- Modal -->
	<div id="modal_skill_page_container"></div>
</div>

@endsection