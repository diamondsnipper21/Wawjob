<?php
/**
* Buyer Overview Page on Super Admin
*
* @author KCG
* @since July 7, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\TransactionLocal;
?>
@extends('layouts/admin/super/user')
@section('content')
<div id="user_affiliate" class="user-affiliate">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Affiliate</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<ul class="nav nav-tabs">
				<li class="{{ $actTab == 'payment' ? 'active': '' }}">
					<a href="#tab_affiliate_payment" data-toggle="tab">Overview</a>
				</li>
				<li class="{{ $actTab == 'referrals' ? 'active': '' }}">
					<a href="#tab_affiliate_referrals" data-toggle="tab">Referrals</a>
				</li>
				<li class="{{ $actTab == 'history' ? 'active': '' }}">
					<a href="#tab_affiliate_payment_history" data-toggle="tab">Affiliate Payment History</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="tab_affiliate_payment" class="tab-pane fade {{ $actTab == 'payment' ? 'active': '' }} in">
					<form method="post" class="form-horizontal form-pay" action="{{ route('admin.super.user.affiliate_pay', [$userId]) }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="form-body">
							<div class="form-group">
								<div class="col-md-6">
									<div class="form-group">
										<div class="col-md-6">
											<div class="title">Affiliate Details</div>
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-5">
											<div class="border mt-4 mr-4">
												<div class="p-4">
													<div class="border-bottom pb-3 mb-3">
														<div class="row">
															<div class="col-md-2">{{ $paymentData['total_sent'] }}</div>
															<div class="col-md-10">Invitations sent</div>
														</div>
													</div>

													<div class="border-bottom pb-3 mb-3">
														<div class="pl-3">
															<div class="mb-3">Primary Affiliate</div>
															<div class="row">
																<div class="col-md-2">{{ $paymentData['total_accepted_buyers'] }}</div>
																<div class="col-md-10">Buyers</div>
															</div>

															<div class="row">
																<div class="col-md-2">{{ $paymentData['total_accepted_freelancers'] }}</div>
																<div class="col-md-10">Freelancers</div>
															</div>
														</div>
													</div>

													<div>
														<div class="mb-3">Secondary Affiliate</div>
														<div class="pl-3">
															<div class="row">
																<div class="col-md-2">{{ $paymentData['total_secondary_accepted_buyers'] }}</div>
																<div class="col-md-10">Buyers</div>
															</div>

															<div class="row">
																<div class="col-md-2">{{ $paymentData['total_secondary_accepted_freelancers'] }}</div>
																<div class="col-md-10">Freelancers</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="col-md-5">
											<div class="form-group">
												<div class="col-md-8">
													<div class="sub-title">Earning</div>
												</div>
											</div>

											<div class="form-group">
												<label class="control-label col-md-6">Pending</label>
												<div class="col-md-6">
													<p class="form-control-static">{{ $paymentData['pending_amount'] >= 0 ? '$' . formatCurrency($paymentData['pending_amount']) : '($' . formatCurrency(abs($paymentData['pending_amount'])) . ')' }}</p>
												</div>
											</div>

											<div class="form-group">
												<label class="control-label col-md-6">Last Payment</label>
												<div class="col-md-6">
													<p class="form-control-static">{{ $paymentData['last_payment_amount'] }}</p>
												</div>
											</div>

											<div class="form-group">
												<label class="control-label col-md-6">Lifetime</label>
												<div class="col-md-6">
													<p class="form-control-static">{{ $paymentData['lifetime_amount'] >= 0 ? '$' . formatCurrency($paymentData['lifetime_amount']) : '($' . formatCurrency(abs($paymentData['lifetime_amount'])) . ')' }}</p>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<div class="col-md-6">
											<div class="title">Affiliate Commission</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Affiliate Users</label>
										<div class="col-md-6">
											<p class="form-control-static">{{ $paymentData['all_user_count'] }}</p>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Amount</label>
										<div class="col-md-6">
											<p class="form-control-static">{{ $paymentData['pending_amount'] >= 0 ? '$' . formatCurrency($paymentData['pending_amount']) : '($' . formatCurrency(abs($paymentData['pending_amount'])) . ')' }}</p>
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-6 divide"></div>
									</div>						
									<div class="form-group">
										<label class="control-label col-md-3"><input type="checkbox" name="check_pay"
										{{ $user->isSuspended() || $user->isFinancialSuspended() ? 'disabled': '' }}/></label>
										<div class="col-md-6">
											<p class="form-control-static">Yes, I'm going to pay now.</p>	
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-3 toolbar text-right">
											<button class="btn btn-sm blue button-pay" type="submit" disabled><i class="fa fa-check"></i>Pay Now</button>
										</div>
									</div>
								</div>
							</div>
						</div>			
					</form>
				</div><!-- .tab-pane -->

				<div id="tab_affiliate_referrals" class="tab-pane fade {{ $actTab == 'referrals' ? 'active': '' }} in">
					<div class="table-container">
		    			<table class="table table-striped table-bordered table-hover">
				  			<thead>
				  				<tr class="heading">
				  					<th>Referral User</th>
				  					<th width="18%">User Type</th>
				  					<th width="18%">Type</th>
				  					<th width="18%">Ref User Earning</th>
				  					<th width="18%">Registered</th>
				  				</tr>
				  			</thead>
				  			<tbody>
			  				@forelse($affiliated_users as $u)
				  				<tr>
				  					<td class="text-center"><a href="{{ route('admin.super.user.overview', ['user_id' => $u['user']->id]) }}">{{ $u['user']->fullname() }}</a></td>
				  					<td class="text-center">{{ $u['user']->isFreelancer() ? 'Freelancer' : 'Buyer' }}</td>
				  					<td class="text-center">{{ $u['type'] }}</td>
				  					<td class="text-center">${{ formatCurrency($u['total']) }}</td>
				  					<td class="text-center">{{ format_date('M d, Y', $u['user']->created_at) }}</td>
				  				</tr>
				  			@empty
				  				<tr>
				  					<td colspan="4" class="text-center">No Referrals</td>
				  				</tr>
				  			@endforelse
				  			</tbody>
				  		</table>
				  	</div>
				</div><!-- .tab-pane -->
				
				<div id="tab_affiliate_payment_history" class="tab-pane fade {{ $actTab == 'history' ? 'active': '' }} in">
					@if ( $historyTab == 'master' )
					<form class="form-datatable" action="{{ Request::url() }}" method="post">
	    				<input type="hidden" name="_token" value="{{ csrf_token() }}" />

						<div class="table-container">
			    			<table class="table table-striped table-bordered table-hover">
					  			<thead>
					  				<tr class="heading">
					  					<th width="6%" class="sorting{{ $sort == 'id' ? $sort_dir : '' }}" data-sort="id">#ID</th>
					  					<th width="15%" class="sorting{{ $sort == 'created_at' ? $sort_dir : '' }}" data-sort="created_at">Date</th>
					  					<th width="20%" class="sorting{{ $sort == 'username' ? $sort_dir : '' }}" data-sort="username">User</th>
					  					<th>Description</th>
					  					<th width="12%" class="sorting{{ $sort == 'amount' ? $sort_dir : '' }}" data-sort="amount">Amount</th>
					  					<th width="12%" class="sorting{{ $sort == 'status' ? $sort_dir : '' }}" data-sort="status">Status</th>
					  				</tr>
									<tr role="row" class="filter">
										<th>
											<input type="text" class="form-control form-filter input-sm" name="filter[id]" value="{{ old('filter.id') }}" placeholder="#ID" />
										</th>
										<th>
											<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
												<input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][from]" placeholder="From" value="{{ old('filter.created_at.from') }}" data-value="{{ old('filter.created_at.from') }}" />
												<span class="input-group-btn">
													<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
												</span>
											</div>
											<div class="input-group date datepicker" data-date-format="yyyy-mm-dd">
												<input type="text" class="form-control form-filter input-sm" readonly name="filter[created_at][to]" placeholder="To" value="{{ old('filter.created_at.to') }}" data-value="{{ old('filter.created_at.to') }}" />
												<span class="input-group-btn">
													<button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
												</span>
											</div>
										</th>
										<th>
											<input type="text" class="form-control form-filter input-sm" name="filter[username]" value="{{ old('filter.username') }}" placeholder="#ID or User Name" />
										</th>
										<th></th>
										<th>
											<input type="text" class="form-control form-filter input-sm" name="filter[amount]" value="{{ old('filter.amount') }}" placeholder="Amount" />
										</th>
										<th>
											<select name="filter[status]" class="form-control form-filter input-sm select2" data-with-color="1">
												<option value="">Select...</option>
												<option value="{{ TransactionLocal::STATUS_AVAILABLE }}" {{ TransactionLocal::STATUS_AVAILABLE == old('filter.status') ? 'selected' : '' }}>Pending</option>
												<option value="{{ TransactionLocal::STATUS_DONE }}" {{ TransactionLocal::STATUS_DONE == old('filter.status') ? 'selected' : '' }}>Paid</option>
											</select>
										</th>
									</tr>
					  			</thead>
					  			<tbody>
					  			@forelse($transactions as $t)
					  				<tr>
					  					<td class="text-center">{{ $t->id }}</td>
					  					<td class="text-center">{{ $t->done_at ? format_date('M d, Y', $t->done_at) : ' - ' }}</td>
					  					<td class="text-center">
										    <a href="{{ route('admin.super.user.overview', ['user_id' => $t->ref_user->id]) }}">{{ $t->ref_user->fullname() }}</a>
					  					</td>
					  					<td>{!! $t->affiliate_description_string() !!}</td>
					  					<td class="text-center">{{ $t->amount > 0 ? '$' . formatCurrency($t->amount) : '($' . formatCurrency(abs($t->amount)) . ')' }}</td>
					  					<td class="text-center">{{ $t->isDone() ? 'Paid' : 'Pending' }}</td>
					  				</tr>
					  			@empty
					  				<tr>
					  					<td colspan="5" class="text-center">No Payment History</td>
					  				</tr>
					  			@endforelse
					  			</tbody>
							</table>
						</div>
					</form>
					@endif
				</div><!-- .tab-pane -->
			</div>
	    </div>
	</div>
</div>
@endsection