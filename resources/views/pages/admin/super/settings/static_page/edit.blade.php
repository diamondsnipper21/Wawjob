<?php
/**
 *
 * @author KCG
 * @since Jan 20, 2019
 * @version 1.0
*/

use iJobDesk\Models\StaticPage;

?>
@extends('layouts/admin/super')

@section('content')
<div id="modal_static_page_container">
	<form action="{{ Request::url() }}" method="post" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
		<div id="modal_static_page">
			<div class="portlet light">
	            <div class="portlet-title">
	                <div class="caption">
	                    <span class="caption-helper">
	                    	<span class="caption-subject font-green-sharp bold">
	                    		<i class="icon-note"></i>&nbsp;&nbsp;{{ empty($static_page->id)?'Add New Static Page':'Edit #'.$static_page->id.' Static Page' }}
	                    	</span>
	                   	</span>
	                </div>
            		<a href="{{ route('admin.super.settings.static_pages') }}" class="back-list">&lt; Back to list</a>
	            </div>
	            <div class="portlet-body">

	            	{{ show_messages() }}

					<div class="row">
						<div class="col-md-12">
							<div class="form-group row">
								<label class="col-sm-1 control-label bold">Slug&nbsp;<span class="required">*</span></label>
								<div class="col-sm-11">
									<input type="text" class="form-control slug" name="slug" value="{{ !empty($static_page->id) ? $static_page->slug: '' }}" data-rule-required="true" data-auto-submit="false" />
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
												<input type="text" class="form-control title" name="title[{{ $lang }}]" value="{{ !empty($static_page->id) ? parse_json_multilang($static_page->title, $lang) : '' }}" data-rule-required="true" data-auto-submit="false" />						
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group row">
											<label class="col-sm-2 control-label bold">Keyword&nbsp;<span class="required">*</span></label>
											<div class="col-sm-10">
												<input type="text" class="form-control keyword" name="keyword[{{ $lang }}]" value="{{ !empty($static_page->id) ? parse_json_multilang($static_page->keyword, $lang) : '' }}" data-rule-required="true" data-auto-submit="false" />						
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group row">
											<label class="col-sm-2 control-label bold">Description&nbsp;<span class="required">*</span></label>
											<div class="col-sm-10">
												<input type="text" class="form-control desc" name="desc[{{ $lang }}]" value="{{ !empty($static_page->id) ? parse_json_multilang($static_page->desc, $lang) : '' }}" data-rule-required="true" data-auto-submit="false" />						
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group row">
											<label class="col-sm-1 control-label bold">Content&nbsp;<span class="required">*</span></label>
											<div class="col-sm-11">
												<textarea name="content[{{ $lang }}]" id="content_{{ $lang }}" class="form-control content ckeditor" rows="5" data-rule-required="true" data-auto-submit="false">{{ !empty($static_page->id) ? parse_json_multilang($static_page->content, $lang) : '' }}</textarea>	
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group row">
											<div class="col-sm-11 col-sm-offset-1">
												<label>
													<input type="checkbox" name="notify_users" value="1" data-status-0="true" data-status-2="true"> Notify all users by email
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<div class="row">
										<div class="col-md-offset-1 col-md-9">
											<button type="submit" class="btn blue">Save</button>
											<a class="btn default" href="{{ route('admin.super.settings.static_pages') }}">Cancel</a>
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