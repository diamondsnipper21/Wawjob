<div class="form-group row">
    <div class="col-sm-3 col-xs-6 control-label">
        <div class="pre-summary">{{ trans('user.change_security_question.question') }}</div>
    </div>
    <div class="col-sm-9 col-xs-6">
        <div class="w-50">
            <select class="form-control select2" id="question_id" name="question_id" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
                <option value="">{{ trans('common.please_select') }}</option>
                @foreach ($security_questions as $question)
                <option value="{{ $question->id }}"{{ old('question_id') && old('question_id') == $question->id ? ' selected' : '' }}>{{ parse_json_multilang($question->question) }}</option>
                @endforeach
            </select>                
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-3 col-xs-6 control-label">
        <div class="pre-summary">{{ trans('user.change_security_question.answer') }}</div>
    </div>
    <div class="col-sm-9 col-xs-6">
        <div class="w-50">
            <input type="text" class="form-control" id="answer" name="answer" autocomplete="off" data-rule-required="true" value="{{ old('answer') ? old('answer') : '' }}" {{ $current_user->isSuspended() ? 'disabled' : '' }}>
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-3 col-xs-6 control-label">
        <div class="pre-summary">{{ trans('user.change_security_question.important') }}</div> 
    </div>
    <div class="col-sm-9 col-xs-6 mt-2">
        <div class="chk">
            <label class="pl-0"><input type="checkbox" id="term" name="term" data-rule-required="true" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('user.change_security_question.text_terms') }}</label>
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-9 col-sm-offset-3 col-xs-6 col-xs-offset-6">
        <div class="chk">
            <label class="pl-0"><input type="checkbox" id="remember" name="remember" {{ $current_user->isSuspended() ? 'disabled' : '' }}>{{ trans('user.change_security_question.remeber_this_computer') }}</label>
        </div>
    </div>
</div>