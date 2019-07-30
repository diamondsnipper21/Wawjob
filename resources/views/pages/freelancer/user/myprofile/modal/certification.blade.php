<?php

?>
<!-- Certification Modal -->
<script type="text/x-tmpl" id="tmpl_modal_certification">
    <div class="modal fade form-horizontal" id="modal_certification" tabindex="-1" role="dialog" data-backdrop="static">
        <form action="{{ $modal_form_action }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            <input type="hidden" name="id" value="{%= certification.id %}" />
            <input type="hidden" name="var_name" />
            <input type="hidden" name="collection_var_name" />

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        
                        <h4 class="modal-title">
                            <strong>
                                {% if (!certification.id) { %}
                                    {{ __('profile.add_certification') }}
                                {% } else { %}
                                    {{ __('profile.edit_certification') }}
                                {% } %}
                            </strong>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.title') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control maxlength-handler" id="profile_certification_title" name="profile[certification][title]" value="{%= certification.title %}" maxlength="200" data-rule-required="1" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.date') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_certification_month" name="profile[certification][month]" data-rule-required="1">
                                            <option value="">- {{ __('common.month') }} -</option>
                                         @for ($month = 0; $month < 12; $month++)
                                            <option value="{{ $month + 1 }}" {%= ({{ $month + 1 }} == certification.month?'selected':'') %}>{{ strftime('%B', strtotime('1990-' . ($month + 1) . '-24')) }}</option>
                                         @endfor   
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_certification_year" name="profile[certification][year]" data-rule-required="1" data-minimum-results-for-search="-1">
                                            <option value="">- {{ __('common.year') }} -</option>
                                         @for ($year = date('Y'); $year >= 1950 ; $year--)
                                            <option value="{{ $year }}" {%= ({{ $year }} == certification.year?'selected':'') %}>{{ $year }}</option>
                                         @endfor   
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.url') }} ({{ __('common.optional') }})</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="profile_certification_url" name="profile[certification][url]" value="{%= certification.url %}" data-rule-url="true" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.description') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control maxlength-handler" id="profile_certification_description" name="profile[certification][description]" maxlength="255" data-rule-required="1" rows="6">{%= certification.description %}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</script>