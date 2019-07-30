<div id="modalViewPaymentGateway" class="modal fade modal-view-payment-gateway" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans('user.payment_method.view_qr_code') }}</h4>
            </div>

            <div class="modal-body">
                <div class="text-center">
                    <img class="img-qrcode">
                </div>
            </div>

            <div class="modal-footer">
                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.close') }}</a>
            </div>
        </div>
    </div>
</div>