<?php

use iJobDesk\Models\HelpPage;

?>
@extends('layouts/admin/super')

@section('content')
<div id="modal_static_page_container">
	<form action="{{ Request::url() }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<div id="modal_help_page">
			<div class="portlet light">
	            <div class="portlet-title">
	                <div class="caption">
	                    <span class="caption-helper">
	                    	<span class="caption-subject font-green-sharp bold">
	                    		<i class="icon-note"></i>&nbsp;&nbsp;{{ empty($help_page->id)?'Add New Help Page':'Edit #'.$help_page->id.' Help Page' }}
	                    	</span>
	                   	</span>
	                </div>
            		<a href="{{ route('admin.super.settings.help_pages') }}" class="back-list">&lt; Back to list</a>
	            </div>
	            <div class="portlet-body">

	            	{{ show_messages() }}

					<div class="form-group row">
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-3 control-label bold">Type&nbsp;<span class="required">*</span></label>
								<div class="col-sm-9">
									<select name="type" id="type" class="table-group-action-input form-control input-inline input-medium input-sm select2 select-for" data-width="200" data-auto-submit="false">
										<option value="0" {{ $help_page->type === 0 ? 'selected' : '' }}>All</option>
										<option value="1" {{ HelpPage::TYPE_FREELANCER == $help_page->type ? 'selected' : '' }}>Freelancer</option>
										<option value="2" {{ HelpPage::TYPE_BUYER == $help_page->type ? 'selected' : '' }}>Buyer</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-3 control-label bold">Main Category</label>
								<div class="col-sm-9">
									<select name="parent_id" class="form-control select2-category main">
										<option value="0" {{ $help_page->parent_id === 0 ? 'selected' : '' }} data-for="0">Select</option>
										@if ( count($parent_pages) )
											@foreach ( $parent_pages as $parent )
											<option value="{{ $parent->id }}" {{ $help_page->parent_id == $parent->id ? 'selected' : '' }} data-for="{{ $parent->type }}">{{ parse_json_multilang($parent->title, 'en') }} {{ HelpPage::TYPE_FREELANCER == $parent->type?'(Freelancer)':(HelpPage::TYPE_BUYER == $parent->type?'(Buyer)':'All') }}</option>
												@if ( count($parent->child) )
													@foreach ( $parent->child as $child )
													<option value="{{ $child->id }}" {{ $help_page->parent_id == $child->id ? 'selected' : '' }} data-parent="{{ $parent->id }}" data-for="{{ $parent->type }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ parse_json_multilang($child->title, 'en') }}</option>
													@endforeach
												@endif			
											@endforeach
										@endif
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group row">
								<label class="col-sm-3 control-label bold">Position&nbsp;<span class="required">*</span></label>
								<div class="col-sm-9">
									<input type="text" name="order" class="form-control w-30" value="{{ $help_page->order }}" data-rule-required="1" data-rule-number="1" >
								</div>
							</div>
						</div>
					</div>
					<div class="row {{ $help_page->type != 0?'disable':'' }} second-category-row">
						<div class="col-md-6">
							<div class="row form-group">
								<label class="col-sm-3 control-label bold">Second Category</label>
								<div class="col-sm-9">
									<select name="second_parent_id" class="form-control select2-category second" data-width="100%">
										<option value="0" {{ $help_page->second_parent_id === 0 ? 'selected' : '' }} data-for="0">Select</option>
										@if ( count($parent_pages) )
											@foreach ( $parent_pages as $parent )
											<option value="{{ $parent->id }}" {{ $help_page->second_parent_id == $parent->id ? 'selected' : '' }} data-for="{{ $parent->type }}">{{ parse_json_multilang($parent->title, 'en') }} {{ HelpPage::TYPE_FREELANCER == $parent->type?'(Freelancer)':(HelpPage::TYPE_BUYER == $parent->type?'(Buyer)':'All') }}</option>
												@if ( count($parent->child) )
													@foreach ( $parent->child as $child )
													<option value="{{ $child->id }}" {{ $help_page->second_parent_id == $child->id ? 'selected' : '' }} data-parent="{{ $parent->id }}" data-for="{{ $parent->type }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ parse_json_multilang($child->title, 'en') }}</option>
													@endforeach
												@endif			
											@endforeach
										@endif
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row form-group">
								<label class="col-sm-3 control-label bold">Position</label>
								<div class="col-sm-9">
									<input type="text" name="second_order" class="form-control w-30" value="{{ $help_page->second_order }}" data-rule-number="1" >
								</div>
							</div>
						</div>						
					</div>
					<div class="row form-group">
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-3 control-label bold">Possible URL</label>
								<div class="col-sm-9">
									<input type="text" name="slug" class="form-control" value="{{ $help_page->slug }}" >
								</div>
							</div>
						</div>
					</div>

					<div class="tabbable-custom nav-justified">
						<ul class="nav nav-tabs nav-justified">
							@foreach (['en' => 'English', 'ch' => 'China'] as $lang => $label)
							<li class="{{ $lang == 'en'?'active':'' }}">
								<a href="#tab_{{ $lang }}" data-toggle="tab" aria-expanded="true"><img src="/assets/images/common/lang_flags/{{ $lang }}.png">&nbsp;{{ $label }}</a>
							</li>
							@endforeach
						</ul>
						<div class="tab-content">
							@foreach (['en', 'ch'] as $lang)
							<div class="tab-pane {{ $lang == 'en'?'active':'' }}" id="tab_{{ $lang }}">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group row">
											<label class="col-sm-1 control-label bold">Title&nbsp;<span class="required">*</span></label>
											<div class="col-sm-11">
												<input type="text" class="form-control title" name="title[{{ $lang }}]" value="{{ !empty($help_page->id) ? parse_json_multilang($help_page->title, $lang) : '' }}" data-rule-required="true" data-auto-submit="false" />
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12">
										<div class="form-group row">
											<label class="col-sm-1 control-label bold">Content<!-- &nbsp;<span class="required">*</span> --></label>
											<div class="col-sm-11">
												<textarea name="content[{{ $lang }}]" id="content_{{ $lang }}" class="form-control content ckeditor" rows="10" data-rule-required="false" data-auto-submit="false">{{ !empty($help_page->id) ? parse_json_multilang($help_page->content, $lang) : '' }}</textarea>	
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<div class="row">
										<div class="col-md-offset-1 col-md-9">
											<button type="submit" class="btn blue">Save</button>
											<a class="btn default" href="{{ route('admin.super.settings.help_pages') }}">Cancel</a>
										</div>
									</div>
								</div>
							</div>
							@endforeach
						</div>
					</div>
	            </div>
	        </div>
	    </div>
	</form>
</div>
@endsection