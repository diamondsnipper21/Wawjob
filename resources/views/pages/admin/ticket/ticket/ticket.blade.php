<?php
/**
* Ticket Detail Page on Ticket Manager
*
* @author KCG
* @since July 4, 2017
* @version 1.0
*/

use iJobDesk\Models\Ticket;

?>
@extends('layouts/admin/' . $role_id)

@section('content')

<script type="text/javascript">
</script>

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-cogs font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold">Tickets</span>
        </div>
        <div class="tools">
            <button class="btn green" data-toggle="modal" data-target="#modal_ticket">Edit <i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="portlet-body">
    </div>
</div>

@endsection