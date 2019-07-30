<?php

use iJobDesk\Models\File;
use iJobDesk\Models\Category;

use iJobDesk\Models\UserPortfolio;

?>
<script type="text/javascript">
    var U_P_THUMB_WIDTH   = {{ UserPortfolio::THUMB_WIDTH }};
    var U_P_THUMB_HEIGHT  = {{ UserPortfolio::THUMB_HEIGHT }};
</script>
<!-- Portfolio Modal -->
<script type="text/x-tmpl" id="tmpl_modal_portfolio">
    <div class="modal fade form-horizontal" id="modal_portfolio" tabindex="-1" data-backdrop="static" role="dialog">
        <form action="{{ $modal_form_action }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            <input type="hidden" name="id" value="{%= portfolio.id %}" />
            <input type="hidden" name="var_name" />
            <input type="hidden" name="collection_var_name" />

            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        
                        <h4 class="modal-title">
                            <strong>
                                {% if (!portfolio.id) { %}
                                    {{ __('profile.add_portfolio') }}
                                {% } else { %}
                                    {{ __('profile.edit_portfolio') }}
                                {% } %}
                            </strong>
                        </h4>
                    </div>
                    <div class="modal-body">

                        {{-- DONT USE THIS SECTION NOW --}}
                        @if (false)
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.title') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control maxlength-handler" id="profile_portfolio_title" name="profile[portfolio][title]" value="{%= portfolio.title %}" maxlength="255" data-rule-required="1" />
                            </div>
                        </div>
                        @endif

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.category') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control select2" id="profile_portfolio_cat_id" name="profile[portfolio][cat_id]" data-rule-required="1">
                                    <option value="">{{ __('common.choose_category') }}</option>
                                    @foreach(Category::projectCategories() as $id => $category1)
                                        @if (array_key_exists('children', $category1))
                                        <optgroup label="{{ parse_multilang($category1['name'])}}">
                                            @if ($category1['children'] && is_array($category1['children']))
                                                @foreach($category1['children'] as $id=>$category2)
                                                    <option value="{{ $category2['id'] }}" {%= portfolio.cat_id  == {{ $category2['id'] }}? "selected" : "" %}>{{ parse_multilang($category2['name'])}}</option>
                                                @endforeach
                                            @endif
                                        </optgroup>
                                        @else
                                        <option value="{{ $category1['id'] }}" {%= portfolio.cat_id  == {{ $category1['id'] }}? "selected" : "" %}>{{ parse_multilang($category1['name'], App::getLocale())}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- DONT USE THIS SECTION NOW --}}
                        @if (false)
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.url') }} ({{ __('common.optional') }})</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="profile_portfolio_url" name="profile[portfolio][url]" value="{%= portfolio.url %}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.keyword') }} ({{ __('common.optional') }})</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="profile_portfolio_keyword" name="profile[portfolio][keyword]" value="{%= portfolio.keyword %}" />
                            </div>
                        </div>
                        @endif

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.description') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control maxlength-handler" id="profile_portfolio_description" name="profile[portfolio][description]" maxlength="255" data-rule-required="1" rows="6">{%= portfolio.description %}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-sm-3 control-label">{{ __('common.image') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <div class="file-upload-container">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <span class="btn btn-success green btn-file">
                                            <span class="fileinput-new "><i class="icon-cloud-upload"></i>&nbsp;&nbsp;{{ __('common.upload') }}</span> 
                                            <span class="fileinput-exists">{{ __('common.change') }}</span>
                                            
                                            <input type="file" data-rule-required="1" class="form-control" name="attached_files[]" title="{{ __('profile.portfolio_image_desc') }}"  {!! render_file_validation_options(File::TYPE_USER_PORTFOLIO) !!} />
                                            
                                            <input type="hidden" name="file_ids" value="{%= portfolio.imploded_files %}" />
                                            <input type="hidden" name="file_type" value="{{ File::TYPE_USER_PORTFOLIO }}" />
                                        </span>
                                        <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a>&nbsp;&nbsp;&nbsp;
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-offset-3 col-sm-9">
                                <div class="temp-avatar"></div>
                            </div>
                            <div class="col-sm-offset-3 col-sm-9 portfolio-img">
                                <img src="{%= portfolio.thumb_url %}" height="{{ UserPortfolio::THUMB_HEIGHT }}" class="img-responsive" />
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save">{{ __('common.save') }}</button>
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="x1" class="x1" />
            <input type="hidden" name="y1" class="y1" />
            <input type="hidden" name="width" class="w" />
            <input type="hidden" name="height" class="h" />
        </form>
    </div>
</script>