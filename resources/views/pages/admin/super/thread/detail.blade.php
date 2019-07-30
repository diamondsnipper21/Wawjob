<?php
/**
* Thread Detail Page on Super Admin
*
* @author KCG
* @since Sep 9, 2018
* @version 1.0
*/

use iJobDesk\Models\User;

?>
@extends('layouts/admin/super')

@section('content')

<div id="thread_detail">
	<div class="portlet light">
	    <div class="portlet-title">
	        <div class="caption">
	            <i class="fa fa-cogs font-green-sharp"></i>
	            <span class="caption-subject font-green-sharp bold">{{ $page_title }}</span>
	        </div>
	    </div><!-- .portlet-title -->
	    <div class="portlet-body">
	    	{{ show_messages() }}

	    	<div class="row">
	    		<div class="col-md-2"></div>
	    		<div class="col-md-8">
	    			<div class="sub-title">{{ $thread->subject }}</div>
	    			<div class="form-group">
			            <ul class="media-list">
			            @foreach ($messages as $message)
			            	<?php 
			            		$sender   = new \stdClass();
			            		$receiver = new \stdClass();

			            		$sender->id   = null;
			            		$receiver->id = null;

			            		if ($message->sender_id)
			            			$sender = $message->sender;
			            		else {
			            			$sender->fullname = $message->thread->receivers();
			            		}

			            		if ($message->sender_id == $current_user->id) {
			            			$receiver->fullname = $message->thread->receivers();
			            		} else {
			            			$receiver = $current_user;
			            		}
			            		
			            	?>
			            	<li class="media message {{ $message->isUnread()?'unread':'' }}" data-url="{{ route('admin.super.message.read', ['id' => $message->id]) }}">
							    <div class="media-body">
							        <div class="row">
							            <div class="col-md-7">
							                <a href="javascript:;" class="sender-avatar"><img class="img-circle" src="{{ avatar_url($message->sender) }}" width="60" /></a>
							                <div class="sender-names">
							                    <div class="sender-role">
							                    	{!! $sender->id == $current_user->id?'me':$sender->fullname !!}
							                   	</div>
							                </div>
							                <div class="receiver-names">
							                    to {!! $receiver->id == $current_user->id?'me':$receiver->fullname !!}
							                </div>
							            </div>
							            <div class="col-md-5">
							                <div class="send-date pull-right">{{ format_date('M j, Y, g:i A', $message->created_at) }}</div>
							            </div>
							        </div>
							        <div class="message-content">{!! strip_tags(nl2br(strip_tags($message->message)), '<br>') !!}</div>
							    </div>
							</li>
						@endforeach
			            </ul>
			        </div>
			        <form id="form_message" action="{{ route('admin.super.thread.send', ['id' => $thread->id]) }}" class="form-horizontal" method="post">
			            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
			            <div class="form-group">
			                <div class="col-md-12">
			                    <textarea name="message" class="form-control maxlength-handler" rows="5" maxlength="5000" rows="5" placeholder="Message..." data-rule-required="true"></textarea>
			                </div>
			            </div>
			            <div class="form-group row  margin-top-20">
			                <div class="col-md-6">
			                    <button type="submit" class="btn blue"><i class="fa fa-send"></i>&nbsp;Send</button>
			                </div>
			            </div>
			        </form>
	    		</div>
	    	</div>
	    </div>
	</div>
</div>

@endsection