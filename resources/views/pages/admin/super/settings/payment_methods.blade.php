<?php
/**
* Payment Methods on Super Admin
*/

use iJobDesk\Models\PaymentGateway;
use iJobDesk\Models\Settings;

$statusList = PaymentGateway::getOptions();
?>
@extends('layouts/admin/super')

@section('content')

<div id="payment_methods">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Payment Methods</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

			    <div class="row margin-bottom-10">
			        <div class="col-md-offset-6 col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<span></span>
							<select name="template_action" id="template_action" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-auto-submit="false">
								<option value="">Select...</option>
								<option value="0">Disable</option>
								<option value="1">Enable</option>
								<option value="2">Disable Withdrawal</option>
								<option value="3">Enable Withdrawal</option>
								<option value="4">Disable Deposit</option>
								<option value="5">Enable Deposit</option>
							</select>
							<button class="btn btn-sm yellow table-group-action-submit button-submit" type="button" disabled><i class="fa fa-check"></i> Submit</button>
						</div>
			        </div>
			    </div>
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="2%"><input type="checkbox" class="group-checkable" /></th>
								<th width="15%" class="sorting{{ $sort == 'name'?$sort_dir:'' }}" data-sort="name">Name</th>
								<th>Information</th>
								<th width="10%" class="sorting{{ $sort == 'status'?$sort_dir:'' }}" data-sort="status">Status</th>
								<th width="10%" class="sorting{{ $sort == 'enable_withdraw'?$sort_dir:'' }}" data-sort="enable_withdraw">Withdrawal</th>
								<th width="10%" class="sorting{{ $sort == 'enable_deposit'?$sort_dir:'' }}" data-sort="enable_deposit">Deposit</th>
								<th width="8%">Action</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Name -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[slug]" value="{{ old('filter.name') }}" placeholder="" />
								</th>
								<th></th>
								<!-- Status -->
								<th>
									<select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.status')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<select name="filter[enable_withdraw]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.enable_withdraw')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>
									<select name="filter[enable_deposit]" class="form-control form-filter input-sm select2" data-with-color="1">
										<option value="">Select...</option>
										@foreach ($statusList as $status => $label)
										<option value="{{ $status }}" {{ "$status" == old('filter.enable_deposit')?'selected':'' }}>{{ $label }}</option>
										@endforeach
									</select>
								</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($payment_methods as $i => $payment_method)
							<tr class="odd gradeX" data-index="{{ $i }}">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $payment_method->id }}" {{ PaymentGateway::enableStatusChanged($payment_method) }} {{ PaymentGateway::enableWithdrawalStatusChanged($payment_method) }} /></td>
								<td>{{ parse_json_multilang($payment_method->name) }}</td>
								<td>
								@if ( $payment_method->isPaypal() )
									{{ Settings::get('PAYPAL_EMAIL') }}
								@elseif ( $payment_method->isSkrill() )
									{{ Settings::get('SKRILL_MERCHANT_EMAIL') }}
								@elseif ( $payment_method->isPayoneer() )
									{{ Settings::get('PAYONEER_EMAIL') }}
								@elseif ( $payment_method->isWeixin() )
									{{ Settings::get('WEIXIN_PHONE_NUMBER') }}
								@elseif ( $payment_method->isCreditCard() )
								@elseif ( $payment_method->isWireTransfer() )
									{{ Settings::get('BANK_REFERENCE') }}
								@endif
								</td>
								<td align="center"><span class="label label-{{ strtolower($statusList[$payment_method->is_active]) }}">{{ $statusList[$payment_method->is_active] }}</span></td>
								<td align="center"><span class="label label-{{ strtolower($statusList[$payment_method->enable_withdraw]) }}">{{ $statusList[$payment_method->enable_withdraw] }}</span></td>
								<td align="center"><span class="label label-{{ strtolower($statusList[$payment_method->enable_deposit]) }}">{{ $statusList[$payment_method->enable_deposit] }}</span></td>
								<td align="center"><a href="#modalPaymentMethod_{{ $payment_method->type }}" class="btn btn-sm blue edit-link edit" data-toggle="modal" data-backdrop="static"><span class="md-click-circle"></span><i class="fa fa-edit"></i> Edit</a></td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="7" align="center">No Payment Methods</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>

@include ('pages.admin.super.settings.payment_method.modal')

@endsection