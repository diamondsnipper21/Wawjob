<?php

use iJobDesk\Models\Ticket;

?>
<div id="dispute_result_{{ $dispute->id }}" class="modal fade view-modal" tabindex="-1" data-width="600">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Determine Details</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-3">Contract&nbsp;:</div>
            <div class="col-md-9">
                <a href="{{ route('admin.super.contract', ['contract_id' => ($dispute->contract ? $dispute->contract->id : '')]) }}">#{{ $dispute->contract ? $dispute->contract->id : '' }}</a>&nbsp; - &nbsp; {{ $dispute->subject }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">Buyer&nbsp;:</div>
            <div class="col-md-9">
                <a href="{{ route('admin.super.user.overview', ['user_id' => $dispute->buyer_id]) }}">{{ $dispute->buyer }}</a>
                @if ($dispute->buyer == $dispute->creator)
                    {{ '(Creator)' }}
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">Freelancer&nbsp;:</div>
            <div class="col-md-9">
                <a href="{{ route('admin.super.user.overview', ['user_id' => $dispute->contractor_id]) }}">{{ $dispute->contractor }}</a>
                @if ($dispute->contractor == $dispute->creator)
                    {{ '(Creator)' }}
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">Ticket #&nbsp;:</div>
            <div class="col-md-9">
                <a href="{{ route('admin.super.ticket.detail', ['id' => $dispute->id]) }}"># {{ $dispute->id }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">Type&nbsp;:</div>
            <div class="col-md-9">
                {{ $dispute->archive_type?Ticket::getOptions('result')[$dispute->archive_type]:'' }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">Comment&nbsp;:</div>
            <div class="col-md-9">
                <!--
                @if ( strlen($dispute->reason) > $config['admin']['description_modal_reason_length'] )
                {{ substr($dispute->reason, 0, $config['admin']['description_modal_reason_length']) }}
                ...
                @else
                {{ $dispute->reason }}
                @endif
                -->
                {{ $dispute->reason }}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
    </div>
</div>