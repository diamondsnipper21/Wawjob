<?php
/**
* User Message Thread Page on Super Admin
*
* @author KCG
* @since July 14, 2017
* @version 1.0
*/

use iJobDesk\Models\User;
use iJobDesk\Models\File;

?>
@extends('layouts/admin/super/user')

@section('content')
<div id="user_message_thread">
	<div class="portlet light">
	    <div class="portlet-title">
	    	<div class="caption">
				<i class="icon-speech font-green-sharp"></i>
				&nbsp;<span class="caption-subject font-green-sharp bold">{{ $thread->subject }}</span>
			</div>
			<div class="actions">
				<!-- <button class="btn red btn-delete"><i class="fa fa-trash-o"></i> Delete</button> -->
			</div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	<!-- <form class="form-datatable" action="{{ route('admin.super.user.messages.thread', ['user_id' => $user->id, 'thread_id' => $thread->thread_id]) }}" method="post"> -->
	    		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
    			<input type="hidden" name="action" value="" />
			    <input type="hidden" name="reason" value="" />

    			{{ show_messages() }}

    			<div class="row messages-container">
    				<div class="col-md-3 short-info">
    					<div class="label-attendees"><i class="fa fa-user"></i>&nbsp;&nbsp;Attendees</div>
                        <div class="user-short-info margin-top-20">
                            <div class="row">
                                <div class="col-sm-4">
                                    <img src="{{ avatar_url($thread->buyer) }}" class="img-circle user-avatar" />
                                </div>
                                <div class="col-sm-8">
                                    <div class="user-name-loc">
                                        <div class="user-fullname">{{ $thread->buyer_name }}</div>
                                        <div class="user-role">Buyer</div>
                                        <div class="user-location"><i class="fa fa-map-marker"></i> {{ $thread->buyer->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .user-short-info -->
                        <div class="user-short-info margin-top-20">
                            <div class="row">
                                <div class="col-sm-4">
                                    <img src="{{ avatar_url($thread->freelancer) }}" class="img-circle user-avatar" />
                                </div>
                                <div class="col-sm-8">
                                    <div class="user-name-loc">
                                        <div class="user-fullname">{{ $thread->freelancer_name }}</div>
                                        <div class="user-role">Freelancer</div>
                                        <div class="user-location"><i class="fa fa-map-marker"></i> {{ $thread->freelancer->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .user-short-info -->
    				</div><!-- .short-info -->
    				<div class="col-md-9 messages">
    					@include('pages.partials.messages', [
                                'id' => $thread->id, 
                                'messages' => $messages, 
                                'type' => File::TYPE_MESSAGE, 
                                'class' => 'Message', 
                                'can_send' => false, 
                                'totals' => $message_count,
                                'limit' => $message_limit
                        ])
    				</div>
    			</div>
    		<!-- </form>    	 -->
	    </div><!-- .portlet-body -->
	</div>
</div>
@endsection