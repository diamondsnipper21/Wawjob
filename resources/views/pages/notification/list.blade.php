<?php
use iJobDesk\Models\User;
?>

@extends('layouts/default/index')

@section('content')

<div id="notifications" class="notification-page">
	<form></form>
	<div class="view-section">
		<div class="title-section">
		    <i class="icon-bell icon title-icon"></i><span class="title">{{ trans('page.' . $page . '.title') }}</span>
		</div>

	    <div id="notification_rows" class="row list-rows mt-4">
	    	@include('pages.notification.list_rows')
	    </div>
	</div>
</div>
@endsection