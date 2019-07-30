<?php

?>
<!-- Experience Modal -->
<script type="text/x-tmpl" id="tmpl_modal_experience">
    <div class="modal fade form-horizontal" id="modal_experience" tabindex="-1" role="dialog" data-backdrop="static">
        <form action="{{ $modal_form_action }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            <input type="hidden" name="id" value="{%= experience.id %}" />
            <input type="hidden" name="var_name" />
            <input type="hidden" name="collection_var_name" />

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        
                        <h4 class="modal-title">
                            <strong>
                                {% if (!experience.id) { %}
                                    {{ __('profile.add_experience') }}
                                {% } else { %}
                                    {{ __('profile.edit_experience') }}
                                {% } %}
                            </strong>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.title') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control maxlength-handler" id="profile_experience_title" name="profile[experience][title]" value="{%= experience.title %}" maxlength="100" data-rule-required="1" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ __('common.description') }}<span class="required">&nbsp;&nbsp;*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control maxlength-handler" id="profile_experience_description" name="profile[experience][description]" maxlength="500" data-rule-required="1" rows="6">{%= experience.description %}</textarea>
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