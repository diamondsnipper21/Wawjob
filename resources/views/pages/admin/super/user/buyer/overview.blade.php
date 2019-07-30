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
<div id="user_overview" class="buyer">
	<div class="row">
		<div class="col-md-9">
			<div class="row">
				<div class="col-md-5">
					<div class="user-short-info">
						<img src="{{ avatar_url($user) }}" class="img-circle user-avatar" width="100" />
						<div class="user-name-loc">
							<div class="user-fullname">{{ $user->fullname }} <span class="user-role">({{ $user->role_name }})</span></div>
							<div class="user-location"><i class="fa fa-map-marker"></i> {{ $user->location }}</div>
						</div>
					</div><!-- .user-short-info -->
				</div>
				<div class="col-md-3"></div>
				<div class="col-md-4">
					<div class="last-active">Last active: {{ $user->last_activity?ago($user->last_activity):'-' }}</div>
					<div class="created-at">Member Since: {{ format_date(null, $user->created_at) }}</div>
				</div>
			</div>
			<table class="table">
	  			<thead>
	  				<tr>
	  					<th>Job Posted</th>
	  					<th>Job Awarded</th>
	  					<th>Paid</th>
	  					<th>Reviews</th>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<tr>
	  					<td>{{ $user->jobs }} Jobs</td>
	  					<td>{{ $user->hires }}</td>
	  					<td>${{ number_format($user->total_spent) }}</td>
	  					<td>
		  					<div data-toggle="tooltip" class="stars" title="{{ $user->feedback }}" data-value="{{ $user->feedback / 5 * 100}}"></div>
	                	</td>
	  				</tr>
	  			</tbody>
			</table>
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#tab_detail" data-toggle="tab">Details </a>
				</li>
				<li>
					<a href="#tab_company_details" data-toggle="tab">Company Details </a>
				</li>
				<li>
					<a href="#tab_payment_methods" data-toggle="tab">Payment Methods </a>
				</li>
			</ul>
			<div class="tab-content">
				<!-- Detail Information -->
				<div id="tab_detail" class="tab-pane fade active in">
					<div class="form-horizontal">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">User ID:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->username }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">Timezone:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->timezone_label }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">Name:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->fullname }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">Invoice Address:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->invoice_location }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">Email:</label>
									<div class="col-md-8">
										<p class="form-control-static email"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">Phone:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->phone }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">Address:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->location }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
					</div>
				</div><!-- #tab_detail -->

				<!-- Company Details -->
				<div id="tab_company_details" class="tab-pane fade">
					<div class="form-horizontal">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">{{ trans('common.company_name') }}:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->company->name }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">{{ trans('common.timezone') }}:</label>
									<div class="col-md-8">
										@if ($user->company_contact->timezone)
										<p class="form-control-static">
											{{ $user->company_contact->timezone->label }}
										</p>
										@endif
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">{{ trans('common.website') }}:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->company->website }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">{{ trans('common.address') }}:</label>
									<div class="col-md-8">
										<p class="form-control-static">
											@if ($user->company_contact->address)
												{{ $user->company_contact->address }}<br />
											@endif

											@if ($user->company_contact->state || $user->company_contact->city)
												{{ $user->company_contact->state?$user->company_contact->state . ', ': '' }}{{ $user->company_contact->city ? $user->company_contact->city : '' }}
												<br />
											@endif

											@if ( $user->company_contact->country )
												{{ $user->company_contact->country->name }}
											@endif
										</p>
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">{{ trans('common.tagline') }}:</label>
									<div class="col-md-8">
										<p class="form-control-static">{{ $user->company->tagline }}</p>
									</div>
								</div><!-- .form-group -->
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label col-md-4">{{ trans('common.phone') }}:</label>
									<div class="col-md-8">
										@if ($user->company_contact->phone)
										<p class="form-control-static">
											{{ $user->company_contact->phone }}
										</p>
										@endif
									</div>
								</div><!-- .form-group -->
							</div>
						</div>
					</div>
				</div><!-- #tab_company_details -->

				<!-- Payment Methods -->
				<div id="tab_payment_methods" class="tab-pane fade">
					<table class="table">
					@forelse ($user->paymentGateways as $upg)
						<tr>
							<td class="text-center"><img src="{{ $upg->paymentGateway->logo }}" class="logo-gateway" /></td>
							<td class="text-left">
								<label>{{ $upg->title() }}</label>
								{!! $upg->info() !!}
							</td>
							<td>
								@if ($upg->isPrimary())
	                            <span class="label label-primary">Primary</span>
	                            @endif
							</td>
							<td>
								@if ($upg->isPending())
	                            <span class="label label-warning">Pending</span>
	                            @elseif ($upg->isActive())
	                            <span class="label label-primary">Active</span>
	                            @elseif ($upg->isExpired())
	                            <span class="label label-danger">Expired</span>
	                            @endif
							</td>
							<td>
								{{ format_date('M d, Y h:i A', $upg->created_at) }}
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="4" align="center" class="mt-5 pt-4 border-0"><strong>No Payment Methods</strong></td>
						</tr>
	                @endforelse
	                </table>
				</div><!-- #tab_payment_methods -->
			</div>
		</div>
		<div class="col-md-2 col-md-offset-1">
			<table class="table table-bordered margin-top-20">
				<tr>
					<td>Dispute</td>
					<td>{{ $user->stat->total_jobs_disputed }}</td>
				</tr>
				<tr>
					<td>Suspended</td>
					<td>{{ $user->stat->total_users_suspended }}</td>
				</tr>
			</table>

			<div class="text-right pb-2">
				<strong>Available Balance</strong>
			</div>
			<div class="text-right pb-3">
				${{ formatCurrency($user->myBalance(false)) }}
			</div>

			<div class="text-right pb-2">
				<strong>Pending Balance</strong>
			</div>
			<div class="text-right">
				${{ formatCurrency(TransactionLocal::getUserPendingBalance($user->id)) }}
			</div>
		</div>
	</div>
</div>
@endsection