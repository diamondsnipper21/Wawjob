<div class="modal fade" id="modalMilestone" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formMilestone" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id]) }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_action" value="edit_milestone">
                <input type="hidden" name="_id" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="create">{{ trans('common.create_a_milestone') }}</span>
                        <span class="edit">{{ trans('common.edit_milestone') }}</span>
                    </h4>
                </div>
                <div class="modal-body">
                	<div class="content-section">
	                	<div class="desc mb-4">
	                		<p>{{ trans('contract.milestone_release_once_satisfied') }}</p>
	                	</div>

                        <div class="row">
                            <label class="col-xs-4 control-label">{{ trans('common.description') }}<span class="form-required"> *</span></label>
                            <div class="col-xs-8">
                                <input type="input" name="name" id="name" class="form-control maxlength-handler" maxlength="40" data-rule-required="true">
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-xs-4 control-label">{{ trans('common.amount') }}<span class="form-required"> *</span></label>
                            <div class="col-xs-8">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" class="form-control amount-field" name="price" id="price" data-rule-required="true" data-rule-number="true">
                                </div>
                                <div class="pt-2 gray">{{ trans('common.available_funds') }}: {{ formatCurrency($balance, $currency_sign) }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-xs-4 control-label">{{ trans('common.due_date') }}</label>
                            <div class="col-xs-8">
                                <div class="input-group date-field">
                                    <input type="text" class="form-control" name="end_time" id="end_time" data-date="{{ date('m/d/Y') }}" value="{{ date('m/d/Y') }}">
                                    <span class="input-group-addon date-picker">
                                        <i class="fa icon-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-4">
                                <div class="chk">
                                    <label class="pl-0">
                                        <input type="checkbox" name="confirm_fund" id="confirm_fund" value="1" checked>
                                        {{ trans('contract.payment.confirm_escrow') }}
                                    </label>
                                </div>
                            </div>
                        </div>
	                </div>
                </div><!-- .modal-body -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit-milestone">{{ trans('common.submit') }}</button>
                    <button data-dismiss="modal" class="btn btn-link">{{ trans('common.cancel') }}</button>
                </div>
            </form>
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div><!-- .modal -->