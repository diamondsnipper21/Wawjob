<?php
/**
* Contact Info Page (user/contact-info)
*
* @author  - So Kwang
*/
?>
@extends('layouts/user/index')

@section('content')

<div class="section account-section">
	@include('pages.user.contact_info.account.view')
</div>

@if ($user->isCompany())
<div class="section company-detail-section">
	@include('pages.user.contact_info.company.view')
</div>
@endif

@endsection