<?php
use iJobDesk\Models\Todo;
use iJobDesk\Models\User;
use iJobDesk\Models\Timezone;
?>
@extends('layouts.admin.' . ($user->isTicket()?'ticket':'super'))

@section('content')
<div class="portlet light settings-page">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-cogs font-green-sharp"></i>
            <span class="caption-subject font-green-sharp bold">My Account</span>
        </div>
    </div>
    <div class="portlet-body settings-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <div class="col-md-4 control-label">
                        <img src="{{ avatar_url($user) }}" width="150"  class="img-circle avatar-img"/>
                    </div>
                    <div class="col-md-6 user-info">
                        <div class="user-name">
                            <h4><p>{{ $user->fullname() }} ({{ $user->username }})</p></h4>
                        </div>
                        <div  class="user-role">
                            <h4><p>
                            @if ($user->role == User::ROLE_USER_TICKET_MANAGER)
                                Ticket Manager
                            @elseif ($user->role == User::ROLE_USER_SUPER_ADMIN)
                                Super Admin
                            @endif
                            </p></h4>
                        </div>
                    </div> 
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Email:</label>
                    <div class="col-md-6">
                        <p class="form-control-static">
                            {{ $user->email }}
                        </p>
                    </div>                    
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">Timezone:</label>
                    <div class="col-md-6">
                        <p class="form-control-static">
                            {{ $user->timezone_label }}
                        </p>
                    </div>
                </div>
            </div>
            <div id="edit_account" class="col-md-8">
                <form id="account_form" method="post" class="horizontal-form" role="form" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="_action" value="SAVE" />

                    {{ show_messages() }}

                    <div class="form-body">
                        <h3 class="form-section">My Account</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">First Name <span class="required">*</span></label>
                                    <input type="text" name="first_name" value="{{ old('first_name')?old('first_name'):$user->contact->first_name }}" class="form-control" placeholder="First Name" data-rule-required="1" />
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Last Name <span class="required">*</span></label>
                                    <input type="text" name="last_name" value="{{ old('last_name')?old('last_name'):$user->contact->last_name }}" class="form-control" placeholder="Last Name" data-rule-required="1" />
                                </div>
                            </div>
                            <!--/span-->
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Username <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" readonly name="username" value="{{ $user->username }}" class="form-control input-right" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Email Address <span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon input-left"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="email" value="{{ old('email')?old('email'):$user->email }}" class="form-control input-right" placeholder="Email Address" data-rule-required="1" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">Timezone <span class="required">*</span></label>
                                    <select name="timezone" class="form-control form-filter input-sm select2-timezone" data-rule-required="true">
                                        <option value="">Select...</option>
                                        @foreach (Timezone::orderBy('gmt_offset', 'asc')->get() as $timezone)
                                        <option value="{{ $timezone->id }}" {{ ($timezone->id == $user->contact->timezone_id) ? 'selected' : ''}} >{{ $timezone->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        @include('pages.admin.commons.account.partial.avatar')

                        <h3 class="form-section">Password</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Old Password</label>
                                    <input type="password" id="old_password" name="old_password" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">New Password</label>
                                    <input type="password" id="new_password" name="new_password" class="form-control" />
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Confrim Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" data-rule-equalTo="#new_password" />
                                </div>
                            </div>
                            <!--/span-->
                        </div>

                        <div class="margin-top-10">
                            <input type="submit" class="btn blue" value="Submit" />
                            <input type="button" class="btn btn-reset-form" value="Cancel" />
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection