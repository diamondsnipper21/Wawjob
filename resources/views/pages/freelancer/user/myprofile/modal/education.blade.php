<?php

?>
<!-- Education Modal -->
<script type="text/x-tmpl" id="tmpl_modal_education">
    <div class="modal fade form-horizontal" id="modal_education" tabindex="-1" role="dialog" data-backdrop="static">
        <form action="{{ $modal_form_action }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            <input type="hidden" name="id" value="{%= education.id %}" />
            <input type="hidden" name="var_name" />
            <input type="hidden" name="collection_var_name" />

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        
                        <h4 class="modal-title">
                            <strong>
                                {% if (!education.id) { %}
                                    {{ __('profile.add_education') }}
                                {% } else { %}
                                    {{ __('profile.edit_education') }}
                                {% } %}
                            </strong>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">{{ __('common.school') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control maxlength-handler" id="profile_education_school" name="profile[education][school]" value="{%= education.school %}" maxlength="100" data-rule-required="1" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">{{ __('common.dates_attended') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_education_from" name="profile[education][from]" data-rule-required="1" data-rule-pairDateCompare="-1" data-pairDateCompare="#profile_education_from,,#profile_education_to," data-msg-pairDateCompare="Must be lesser than 'To'." data-minimum-results-for-search="-1">
                                            <option value="">{{ __('common.from_cap') }}</option>
                                        @for ($year = date('Y'); $year >= 1950; $year--)
                                            <option value="{{ $year }}" {%= (education.from == {{ $year }}?'selected':'') %}>{{ $year }}</option>
                                        @endfor  
                                        </select>
                                        {% if (education.from) { %}
                                            <span class="gray-text-color">{{ __('common.from_cap') }}</span>
                                        {% } %}
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_education_to" name="profile[education][to]" data-rule-required="1" data-rule-pairDateCompare="1" data-pairDateCompare="#profile_education_to,,#profile_education_from," data-msg-pairDateCompare="Must be greater than 'From'." data-minimum-results-for-search="-1">
                                            <option value="">{{ __('common.to_cap') }}</option>
                                        @for ($year = date('Y'); $year >= 1950; $year--)
                                            <option value="{{ $year }}" {%= education.to == {{ $year }}?'selected':'' %}>{{ $year }}</option>
                                        @endfor
                                        </select>
                                        {% if (education.to) { %}
                                            <span class="gray-text-color">{{ __('common.to_cap') }}</span>
                                        {% } %}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">{{ __('common.degree') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control maxlength-handler" id="profile_education_degree" name="profile[education][degree]" value="{%= education.degree %}" maxlength="100" data-rule-required="1" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">{{ __('profile.area_of_study') }} ({{ __('common.optional') }})</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control maxlength-handler" id="profile_education_major" name="profile[education][major]" value="{%= education.major %}" maxlength="150" />
                            </div>
                        </div>
                        <!-- DON'T USER THIS SECTION NOW. -->
                        @if (false)
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">{{ __('common.description') }} ({{ __('common.optional') }})</label>
                            <div class="col-sm-8">
                                <textarea class="form-control maxlength-handler" id="profile_education_desc" name="profile[education][desc]" maxlength="{{ $config['freelancer']['user']['description_length'] }}" rows="6">{%= education.desc %}</textarea>
                            </div>
                        </div>
                        @endif
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