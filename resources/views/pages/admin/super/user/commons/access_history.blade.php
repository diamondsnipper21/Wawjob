<?php

use iJobDesk\User

?>
@extends('layouts/admin/super/user')

@section('content')
<div id="user_access_histories">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">Access History</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ route('admin.super.user.access_history', ['user_id' => $user->id]) }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			@if (!$histories->isEmpty())
    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($histories) }}</div>
			        </div>
			    </div>
			    @endif
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="5%"></th>
								<th width="25%">Type</th>
								<th width="25%">IP Address</th>
								<th>Date Time</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($histories as $key => $history)
							<tr class="odd gradeX">
								<td align="center">{{ $histories->firstItem() + $key }}</td>
								<td align="center"><span class="label label-{{ strtolower($history->type_string()) }}">{{ $history->type ? 'Log out' : 'Log in' }}</span></td>
								<td align="center">{{ $history->login_ipv4 }} - {{ $history->country() }}</td>
								<td align="center">{{ format_date('Y-m-d H:i:s', $history->logged_at) }}</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="4" align="center">No Histories</td>
			                </tr>
						@endforelse
						</tbody>
			    	</table>
			    </div>
                
    			@if (!$histories->isEmpty())
    			<div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($histories) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $histories->render() !!}</div>
                    </div>
                </div>
			    @endif
	    	</form>
	    </div><!-- .portlet-body -->
	</div>
</div>
@endsection