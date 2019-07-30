<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ trans('ticket.modal.Reply_Comment') }}</h4>
      </div>
      <div class="modal-body">
        <form id="replyForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="ticketId" id="ticketId">
          <input type="hidden" name="type" value="reply">
				  <div class="form-group">
				    <label for="" class="col-sm-2 control-label">{{ trans('ticket.modal.Comment') }}:</label>
				    <div class="col-sm-10">
				    	<textarea name="comment" class="form-control" rows="10" data-rule-required="true"></textarea>
				    </div>
				  </div>
				  <div class="form-group">
				    <label for="" class="col-sm-2 control-label">{{ trans('ticket.modal.Attachment') }}:</label>
				    <div class="col-sm-10">
				    	<input type="file" id="fileAttachment" name="attachFile">
            	<p class="help-block">{{ trans('ticket.modal.Your_file_here_please') }}</p>
            </div>
          </div>
				</form>
      </div>
      <div class="modal-footer">
        <a class="btn btn-default" data-dismiss="modal">{{ trans('common.cancel') }}</a>
        <a class="btn btn-primary" id="saveBtn">{{ trans('common.save') }}</a>
      </div>
    </div>
  </div>
</div>