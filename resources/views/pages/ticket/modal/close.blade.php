<div class="modal fade" id="closeModal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="closeModalLabel">{{ trans('ticket.modal.Close_Ticket') }} </h4>
            </div>
            <div class="modal-body">
                <form id="closeForm" class="form-horizontal" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="ticketId" id="ticketId">
                    <input type="hidden" name="type" value="close">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">{{ trans('ticket.modal.Comment') }}:</label>
                        <div class="col-sm-10">
                            <textarea name="comment" class="form-control" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-group" style="display:none;">
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