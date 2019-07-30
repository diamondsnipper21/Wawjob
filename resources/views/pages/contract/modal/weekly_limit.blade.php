<div class="modal fade" id="modalWeeklyLimit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="weekly_limit_slot">
        <div class="modal-content">
            <form id="form_weeklylimit" method="post" action="{{ _route('contract.contract_view', ['id' => $contract->id])}}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ trans('common.weekly_limit') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="content-section">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_action" value="weekly_limit">

                        <div class="row form-group margin-bottom-10">
                            <div class="col-md-5">
                                <select class="form-control select2" name="weekly_limit" id="weekly_limit">
                                    @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i * 5 }}" {{ ($contract->isChangedLimit() && $i * 5 == $contract->new_limit) || (!$contract->isChangedLimit() && $i * 5 == $contract->limit) ? 'selected' : '' }}>{{ trans('common.n_hours_week', ['n' => $i * 5]) }}</option>
                                    @endfor
                                    <option value="-1" {{ ($contract->isChangedLimit() && $contract->new_limit == -1 ) || (!$contract->isChangedLimit() && $contract->isNoLimit()) ? 'selected' : '' }}>{{ trans('common.no_limit') }}</option>
                                </select> 
                            </div>
                            <div class="col-md-5">
                                <div class="label-week">{!! trans('common.max_week') !!}</div>
                            </div>
                        </div>
                    </div><!-- .content-section -->
                </div><!-- .modal-body -->
                <div class="modal-footer">
                    <button type="submit" class="charge-submit btn btn-primary">{{ trans('common.update') }}</button>
                    <button data-dismiss="modal" class="btn btn-link">{{ trans('common.cancel') }}</button>
                </div>
            </form>
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div><!-- .modal -->