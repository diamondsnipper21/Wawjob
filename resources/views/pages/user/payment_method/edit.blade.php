<?php use iJobDesk\Models\PaymentGateway; ?>
<div id="modalEditPaymentGateway" class="modal fade modal-edit-payment-gateway" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="slot">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><img class="gateway-logo mr-4" />{{ trans('user.payment_method.edit_a_payment_method') }}</h4>
            </div>

            <div class="modal-body">                
                @include('pages.user.payment_method.creditcard')
                @include('pages.user.payment_method.bank')
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-submit-payment-gateway">{{ trans('common.update') }}</button>
                <a class="btn btn-link" data-dismiss="modal">{{ trans('common.cancel') }}</a>
            </div>
        </div>
    </div>
</div>