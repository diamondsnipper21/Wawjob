<?php
/**
* Faqs listing Page on Super Admin
*
* @author KCG
* @since July 7, 2017
* @version 1.0
*/

use iJobDesk\Models\Faq;

?>
@extends('layouts/admin/super')

@section('additional-js')
@endsection

@section('content')

<div id="faq_list">
    <form action="{{ route('admin.super.settings.faqs') }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="_action" value="" />

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-green-sharp hide"></i>
                    <span class="caption-helper"><span class="caption-subject font-green-sharp bold"><i class="icon-user"></i>&nbsp;&nbsp;Faqs</span></span>
                </div>
                <div class="tools">
                    <button class="btn green edit-modal-link" data-url="{{ route('admin.super.settings.faq.edit') }}">Add New <i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="portlet-body">
                {{ show_messages() }}
                <div class="row margin-bottom-10">
                    <div class="col-md-12 margin-top-10">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($faqs) }}</div>
                    </div>
                </div>
                <div class="row margin-bottom-10">
                    <div class="col-md-6 margin-top-10">
                        <a href="#" class="clear-filter">Clear filters</a>
                    </div>
                    <div class="col-md-6">
                        <div class="toolbar toolbar-table pull-right">
                            <span><strong>Action</strong>&nbsp;</span>
                            <select name="action" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-action" data-auto-submit="false">
                                <option value="">Select...</option>
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
                                <th width="15%" class="sorting{{ $sort == 'title'?$sort_dir:'' }}" data-sort="title">Name</th>
                                <th             class="sorting{{ $sort == 'content'?$sort_dir:'' }}" data-sort="content">Desc</th>
                                <th width="10%" class="sorting{{ $sort == 'type'?$sort_dir:'' }}" data-sort="type">Type</th>
                                <th width="8%" class="sorting{{ $sort == 'visible'?$sort_dir:'' }}" data-sort="visible">Visible</th>
                                <th width="10%" class="sorting{{ $sort == 'cat_id'?$sort_dir:'' }}" data-sort="cat_id">Category</th>
                                <th width="5%" class="sorting{{ $sort == 'order'?$sort_dir:'' }}" data-sort="order">Order</th>
                                <th width="10%" >Action</th>
                            </tr>
                            <tr role="row" class="filter">
                                <th>&nbsp;</th>
                                <!-- Name -->
                                <th>
                                    <input type="text" class="form-control form-filter input-sm" name="filter[title]" value="{{ old('filter.title') }}" placeholder="" />
                                </th>
                                <!-- Desc -->
                                <th>
                                    <input type="text" class="form-control form-filter input-sm" name="filter[content]" value="{{ old('filter.content') }}" placeholder="" />
                                </th>
                                <!-- Type -->
                                <th>
                                    <select name="filter[type]" class="form-control form-filter input-sm select2">
                                        <option value="">Select...</option>
                                        <option value="0" {{ "0" == old('filter.type')?'selected':'' }}>Buyer</option>
                                        <option value="2" {{ "2" == old('filter.type')?'selected':'' }}>Freelancer</option>
                                        <option value="1" {{ "1" == old('filter.type')?'selected':'' }}>All</option>
                                    </select>
                                </th>
                                <!-- Visible -->
                                <th>
                                    <select name="filter[visible]" class="form-control form-filter input-sm select2">
                                        <option value="">Select...</option>
                                        <option value="0" {{ "0" == old('filter.visible')?'selected':'' }}>Hidden</option>
                                        <option value="1" {{ "1" == old('filter.visible')?'selected':'' }}>Show</option>
                                    </select>
                                </th>
                                <!-- Category -->
                                <th>
                                    <select name="filter[cat_id]" class="form-control form-filter input-sm select2">
                                        <option value="">Select...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $category->id == old('filter.cat_id') ? 'selected' : '' }}>{{ parse_multilang($category->name, "EN") }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <!-- Order -->
                                <th>
                                    <input type="text" class="form-control form-filter input-sm" name="filter[order]" value="{{ old('filter.order') }}" placeholder="" />
                                </th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($faqs as $faq)
                            <tr class="odd gradeX">
                                <td><input type="checkbox" class="checkboxes" name="ids[]" value="{{ $faq->id }}" data-status-DELETE="1" /></td>
                                <td>{{ parse_multilang($faq->title) }}</td>
                                <td>{{ parse_multilang($faq->content) }}</td>
                                <td>
                                    @if($faq->type == 0)
                                        Buyer
                                    @elseif($faq->type == 1)
                                        All
                                    @else
                                        Freelancer
                                    @endif
                                </td>
                                <td>
                                    @if($faq->visible == 0)
                                        Hidden
                                    @else
                                        Show
                                    @endif
                                </td>
                                <td>{{ parse_multilang($faq->category->name, "EN") }}</td>
                                <td>{{ $faq->order }}</td>
                                <td><a href="#" data-url="{{ route('admin.super.settings.faq.edit', ['id' => $faq->id]) }}" class="btn btn-sm blue edit-modal-link"><span class="md-click-circle"></span><i class="fa fa-edit"></i> Edit</a></td>
                            </tr>
                        @empty
                            <tr class="odd gradeX">
                                <td colspan="8" align="center">No Faqs</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- .table-container -->

                <div class="row margin-top-10">
                    <div class="col-md-6">
                        <div role="status" aria-live="polite">{{ render_admin_paginator_desc($faqs) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="datatable-paginate pull-right">{!! $faqs->render() !!}</div>
                    </div>
                </div>
            </div><!-- .portlet-body -->
        </div><!-- .portlet -->
    </form>
</div><!-- #faq_list -->

<!-- Modal -->
    <div id="modal_faq" class="modal fade modal-scroll" tabindex="-1" data-width="760" aria-hidden="true"></div>
@endsection