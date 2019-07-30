<?php
/**
* User Messages Page on Super Admin
*
* @author KCG
* @since July 14, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
?>
<div id="action_histories" role="tabpanel" class="tab-pane page-content-section">
	<div class="portlet light">
	    <div class="portlet-body">
	    	<form class="form-datatable" action="{{ route('admin.super.'.(!empty($user_id)?'user.':'').'contract.action_history', ['id' => $contract->id, 'user_id' => $user_id]) }}" method="post">
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="_action" value="" />

    			{{ show_messages() }}

    			@if (!$histories->isEmpty())
    			<div class="row margin-bottom-10">
			        <div class="col-md-6 margin-top-10">
			            <div role="status" aria-live="polite">{{ render_admin_paginator_desc($histories) }}</div>
			        </div>
			    </div>
			    <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
			        <div class="col-md-6">
			        </div>
			    </div>
			    @endif
			    <div class="table-container">
			    	<table class="table table-striped table-bordered table-hover">
			    		<thead>
							<tr role="row" class="heading">
								<th width="5%"></th>
								<th width="10%">Type</th>
								<th 		  >Action</th>
								<th width="20%" class="sorting{{ $sort == 'doer_fullname'?$sort_dir:'' }}"     				data-sort="doer_fullname">Doer</th>
								<th width="15%" class="sorting{{ $sort == 'action_histories.created_at'?$sort_dir:'' }}" 	data-sort="action_histories.created_at">Created At</th>
							</tr>
							<tr role="row" class="filter">
								<th>&nbsp;</th>
								<!-- Action Type -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[type]" value="{{ old('filter.type') }}" />
								</th>
								<!-- Action -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[description]" value="{{ old('filter.description') }}" />
								</th>
								<!-- Doer -->
								<th>
									<input type="text" class="form-control form-filter input-sm" name="filter[doer_fullname]" value="{{ old('filter.doer_fullname') }}" />
								</th>
								<!-- Created At -->
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
							</tr>
						</thead>
						<tbody>
						@forelse ($histories as $key => $history)
							<tr class="odd gradeX">
								<td align="center">{{ $histories->firstItem() + $key }}</td>
								<td align="center"><span class="label label-{{ strtolower($history->action_string()) }}">{{ $history->action_type }}</span></td>
								<td>{{ $history->reason }}</td>
								<td>
									@if ($auth_user->isSuper() && !$history->doer->isAdmin())
										<a href="{{ route('admin.super.user.overview', ['user_id' => $history->doer_id]) }}">{!! $history->doer->getUserNameWithIcon() !!}</a>
									@else
										{{ $history->doer_fullname }}
									@endif
								</td>
								<td align="center">{{ format_date('Y-m-d H:i:s', $history->created_at) }}</td>
							</tr>
						@empty
			                <tr class="odd gradeX">
			                    <td colspan="5" align="center">No Histories</td>
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