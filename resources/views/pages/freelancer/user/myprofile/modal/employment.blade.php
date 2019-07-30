<?php

?>
<!-- Employment Modal -->
<script type="text/x-tmpl" id="tmpl_modal_employment">
    <div class="modal fade form-horizontal" id="modal_employment" tabindex="-1" role="dialog" data-backdrop="static">
        <form action="{{ $modal_form_action }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            <input type="hidden" name="id" value="{%= employment.id %}" />
            <input type="hidden" name="var_name" />
            <input type="hidden" name="collection_var_name" />

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        
                        <h4 class="modal-title">
                            <strong>
                                {% if (!employment.id) { %}
                                    {{ __('profile.add_employment') }}
                                {% } else { %}
                                    {{ __('profile.edit_employment') }}
                                {% } %}
                            </strong>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.company') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control maxlength-handler" id="profile_employment_company" name="profile[employment][company]" value="{%= employment.company %}" maxlength="100" data-rule-required="1" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.from_cap') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_employment_from_month" name="profile[employment][from_month]" data-rule-required="1" data-rule-pairDateCompare="-1" data-pairDateCompare="#profile_employment_from_year,#profile_employment_from_month,#profile_employment_to_year,#profile_employment_to_month" data-msg-pairDateCompare="This is lesser than 'To' date.">
                                            <option value="">- {{ __('common.month') }} -</option>
                                        @for ($month = 0; $month < 12; $month++)
                                            <option value="{{ $month + 1 }}" {%= ({{ $month + 1 }} == employment.from_month?'selected':'') %}>{{ strftime('%B', strtotime('1990-' . ($month + 1) . '-24')) }}</option>
                                        @endfor   
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_employment_from_year" name="profile[employment][from_year]" data-rule-required="1" data-rule-pairDateCompare="-1" data-pairDateCompare="#profile_employment_from_year,#profile_employment_from_month,#profile_employment_to_year,#profile_employment_to_month" data-msg-pairDateCompare="This is lesser than 'To' date." data-minimum-results-for-search="-1">
                                            <option value="">- {{ __('common.year') }} -</option>
                                        @for ($year = date('Y'); $year >= 1950; $year--)
                                            <option value="{{ $year }}" {%= ({{ $year }} == employment.from_year?'selected':'') %}>{{ $year }}</option>
                                        @endfor      
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.to_cap') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_employment_to_month" name="profile[employment][to_month]" data-rule-required="1" data-rule-pairDateCompare="1" data-pairDateCompare="#profile_employment_to_year,#profile_employment_to_month,#profile_employment_from_year,#profile_employment_from_month" data-msg-pairDateCompare="This is greater than 'From' date" {%= employment.to_present==1?'disabled':'' %}>
                                            <option value="">- {{ __('common.month') }} -</option>
                                        @for ($month = 0; $month < 12; $month++)
                                            <option value="{{ $month + 1 }}" {%= ({{ $month + 1 }} == employment.to_month?'selected':'') %}>{{ strftime('%B', strtotime('1990-' . ($month + 1) . '-24')) }}</option>
                                        @endfor   
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="profile_employment_to_year" name="profile[employment][to_year]" data-rule-required="1" data-rule-pairDateCompare="1" data-pairDateCompare="#profile_employment_to_year,#profile_employment_to_month,#profile_employment_from_year,#profile_employment_from_month" data-msg-pairDateCompare="This is greater than 'From' date" data-minimum-results-for-search="-1" {%= employment.to_present==1?'disabled':'' %}>
                                            <option value="">- {{ __('common.year') }} -</option>
                                         @for ($year = date('Y'); $year >= 1950; $year--)
                                            <option value="{{ $year }}" {%= ({{ $year }} == employment.to_year?'selected':'') %}>{{ $year }}</option>
                                         @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-12">
                                        <div class="chk">
                                            <label class="pl-0"><input type="checkbox" id="profile_employment_to_present" name="profile[employment][to_present]" {%= employment.to_present==1?'checked':'' %} value="1">{{ __('common.present') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.position') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control maxlength-handler" id="profile_employment_position" name="profile[employment][position]" value="{%= employment.position %}" maxlength="50" data-rule-required="1" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.description') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control maxlength-handler" id="profile_employment_desc" name="profile[employment][desc]" maxlength="1000" data-rule-required="1" rows="6">{%= employment.desc %}</textarea>
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