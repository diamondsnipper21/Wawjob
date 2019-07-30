<?php
use iJobDesk\Models\Contract;
?>

@if ( count($contracts) )
<select class="contract-filter table-group-action-input form-control select2" id="contract_selector" name="contract_selector" data-required="1" aria-required="true" >
    <option value="0" {{ $contract_id == 0 ? 'selected' : ''  }}>{{ $current_user->isFreelancer() ? trans('common.all_contracts') : trans('common.all_freelancers') }}</option>
    @foreach($contracts as $c)
    <option value="{{ $c->id }}" {{ $contract_id == $c->id ? 'selected' : '' }}>{{ ($current_user->isFreelancer() ? $c->buyer->fullname() : $c->contractor->fullname()) . ' - ' . $c->title }}</option>
    @endforeach
</select>
@endif