<?php
/**
* Freelancer Contracts Page on Super Admin
*
* @author KCG
* @since July 14, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\UserAffiliate;

?>
@extends('layouts/admin/super'.(!empty($user)?'/user':''))

@section('content')
<div id="user_affliates" class="freelancer">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Commission History</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			
    			{{ show_messages() }}

    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{!! render_admin_paginator_desc($affiliates) !!}</div>
			        </div>
			    </div> 
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        	<div class="toolbar toolbar-table pull-right">
							<button class="btn btn-sm blue table-group-action-submit button-submit" disabled type="button" ><i class="fa fa-check"></i>Pay Now</button>
						</div>
			        </div>
			    </div>
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="2%"><input type="checkbox" class="group-checkable" /></th>
								<th width="5%" class="sorting{{ $sort == 'user_id' ? $sort_dir : '' }}" data-sort="user_id">ID #</th>
								<th width="17%" class="sorting{{ $sort == 'full_name'?$sort_dir : '' }}" data-sort="full_name">Name</th>
								<th width="10%" class="sorting{{ $sort == 'invited_buyer_count' ? $sort_dir : '' }}" data-sort="invited_buyer_count">Invited Buyers</th>
								<th width="10%" class="sorting{{ $sort == 'invited_freelancer_count' ? $sort_dir : '' }}" data-sort="invited_freelancer_count">Invited Freelancers</th>
								<th width="8%" class="sorting{{ $sort == 'pending_amount' ? $sort_dir : '' }}" data-sort="pending_amount">Pending</th>
								<th width="8%" class="sorting{{ $sort == 'paid_amount' ? $sort_dir : '' }}" data-sort="paid_amount">Paid</th>
								<th width="12%" class="sorting{{ $sort == 'last_payment'?$sort_dir:'' }}" data-sort="last_payment">Last Payment</th>
								<th width="5%">Action</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[user_id]" value="{{ old('filter.user_id') }}" placeholder="ID #" />
								</th>
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[full_name]" value="{{ old('filter.full_name') }}" placeholder="#ID or Name" />
								</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>
									<div class="input-group date datepicker" data-date-format="yyyy/mm/dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[last_payment][from]" placeholder="From" value="{{ old('filter.last_payment.from') }}" data-value="{{ old('filter.last_payment.from') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
									<div class="input-group date datepicker" data-date-format="yyyy/mm/dd">
										<input type="text" class="form-control form-filter input-sm" readonly name="filter[last_payment][to]" placeholder="To" value="{{ old('filter.last_payment.to') }}" data-value="{{ old('filter.last_payment.to') }}" />
										<span class="input-group-btn">
											<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
										</span>
									</div>
								</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						@forelse ($affiliates as $affiliate)
							<tr class="odd gradeX">
			                    <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $affiliate->user_id }}" {{ abs($affiliate->pending_amount) == 0 ? 'disabled' : '' }} /></td>
			                    <td align="center">{{ $affiliate->user_id }}</td>
								<td>{!! User::find($affiliate->user_id)->fullname(true) !!}</td>
								<td>{{ $affiliate->invited_buyer_count }}</td>
								<td>{{ $affiliate->invited_freelancer_count }}</td>
								<td>
								@if ( $affiliate->pending_amount >= 0 )
									${{ formatCurrency(abs($affiliate->pending_amount)) }}
								@else
									(${{ formatCurrency(abs($affiliate->pending_amount)) }}) (Refund)
								@endif
								</td>
								<td>
								@if ( $affiliate->pending_amount >= 0 )
									${{ formatCurrency($affiliate->paid_amount) }}
								@else
									(${{ formatCurrency(abs($affiliate->paid_amount)) }})
								@endif
								</td>
								<td>{{ format_date('Y-m-d H:i', $affiliate->last_payment) }}</td>
								<td align="center"><a href="{{ route('admin.super.user.affiliate', [$affiliate->user_id]) }}">View</a></td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="9" align="center">No Affiliate</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
			    <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($affiliates) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $affiliates->render() !!}</div>
                    </div>
                </div>
	    	</form>	    	
	    </div><!-- .portlet-body -->
	</div>
</div>
@endsection