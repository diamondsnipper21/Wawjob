<?php
/**
* Job Categories Listing Page on Super Admin
*
* @author KCG
* @since July 31, 2017
* @version 1.0
*/

use iJobDesk\Models\Category;

?>
@extends('layouts/admin/super')

@section('content')
<div id="job_categories">
	<form action="{{ Request::url() }}" method="post">
		<input type="hidden" name="_action" value="" />
		<input type="hidden" name="_id" value="" /><!-- Used when delete category -->

		<script type="text/javascript">
			var jtree_categories = @json($jtree_categories);
			var ROOT_ID = {{ Category::ROOT_ID }};
		</script>
		<div class="portlet light">
		    <div class="portlet-title">
		        <div class="caption">
		            <i class="fa fa-cogs font-green-sharp"></i>
		            <span class="caption-subject font-green-sharp bold">Job Categories</span>
		        </div>
		    </div><!-- .portlet-title -->
		    <div class="portlet-body">

		    	{{ show_messages() }}
		    	
		    	<div class="tree-container">
		    		<div class="toolbar">
		    			<button                                                                      class="delete-link btn btn-sm red pull-right action-link" type="button"><i class="fa fa-trash-o"></i>&nbsp;Delete</button>
		    			<button data-url="{{ route('admin.super.settings.job_category.edit') }}"     class="edit-link btn btn-sm blue pull-right action-link" disabled type="button"><i class="fa fa-edit"></i>&nbsp;Edit</button>
		    			<button data-url="{{ route('admin.super.settings.job_category.re_order') }}" class="order-link btn btn-sm btn-info pull-right action-link" type="button"><i class="fa fa-reorder"></i>&nbsp;Save Order</button>
		    			<button data-url="{{ route('admin.super.settings.job_category.edit') }}"     class="add-link btn btn-sm green pull-right action-link" type="button"><i class="fa fa-plus"></i>&nbsp;Add</button>
		    		</div>
		    		<div class="tree"></div>
		    	</div>
		    </div><!-- .portlet-body -->
		</div>
	</form>

	<!-- Modal -->
	<div id="modal_job_category" class="modal fade modal-scroll" tabindex="-1" data-width="760" aria-hidden="true"></div>
</div>

@endsection