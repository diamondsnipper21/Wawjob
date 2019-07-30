<!-- Edit memo -->
<div class="modal fade" id="EditMemoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ trans('common.edit_memo') }}<span class="form-required"> *</span></h4>
            </div>
            <div class="modal-body">
                <textarea id="newMemo" class="memo form-control maxlength-handler" maxlength="{{ $config['freelancer']['workdiary']['memo'] }}"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" id="updateMemo" class="btn btn-primary">{{ trans('common.change') }}</button>
                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
            </div>
        </div>
    </div>
</div>
