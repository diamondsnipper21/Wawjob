<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editModalLabel">Edit Comment</h4>
            </div>
            <div class="modal-body">
                <form id="editForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="commentId" id="commentId">
                    <input type="hidden" name="type" value="edit">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label"><b>Comment</b></label>
                        <div class="col-sm-10">
                            <textarea name="comment" class="form-control" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-group" style="display:none;">
                        <label for="" class="col-sm-2 control-label"><b>Attachment</b></label>
                        <div class="col-sm-10">
                            <input type="file" id="fileAttachment" name="attachFile">
                            <p class="help-block">Your file here please.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" id="saveBtn">{{ trans('common.submit') }}</a>
                <a class="btn btn-default" data-dismiss="modal">{{ trans('common.cancel') }}</a>
            </div>
        </div>
    </div>
</div>