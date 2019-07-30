<div class="messages">
	<form action="{{ Request::url() }}" method="post">
    	<div class="summary-desc">
	    	<h4 class="block"><i class="fa fa-comments"></i> Messages</h4>
	    	<div class="well">
				<ul class="media-list">
				@foreach ($messages as $message)
					<li class="media">
						<a class="pull-left" href="{{ route('admin.super.user.overview', ['user_id' => $message->sender_id]) }}">
							<img class="todo-userpic img-circle" src="{{ avatar_url($message->sender) }}" width="40" />
						</a>
						<div class="media-body todo-comment">
							<p class="todo-comment-head">
								<span class="todo-comment-username pull-left">{{ $message->sender->fullname }}</span> 
								<span class="todo-comment-date pull-right">{{ format_date('M j, Y \a\t g:i a', $message->created_at) }}</span>
							</p>
							<div class="clearfix"></div>
							<p class="todo-text-color">{{ $message->message }}</p>
						</div>
					</li>
				@endforeach
				</ul>
			</div>
		</div>
	    <div class="row margin-top-10">
            <div class="col-md-6">
                <!-- <div role="status" aria-live="polite">{{ render_admin_paginator_desc($messages) }}</div> -->
            </div>
            <div class="col-md-6">
                <div class="datatable-paginate pull-right">{!! $messages->render() !!}</div>
            </div>
        </div>
    </form>
</div>