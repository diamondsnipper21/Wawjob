<?php
/**
* Freelancer Contracts Page on Super Admin
*
* @author KCG
* @since July 14, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Contract;

$statusList = Contract::$str_contract_status;

?>
@extends('layouts/admin/super)

@section('content')
<div id="contact_us">
</div>
@endsection