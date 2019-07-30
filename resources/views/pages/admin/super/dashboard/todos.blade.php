<?php
	use iJobDesk\Models\Todo;
?>
<div class="portlet light ">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-share font-blue-steel hide"></i>
			<span class="caption-subject font-blue-steel bold">TODOs</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
			<ul class="feeds todos">
				@foreach ($todos as $todo)
				<li>
					<div class="col1">
						<div class="cont">
							<div class="cont-col1">
								
							</div>
							<div class="cont-col2">								
								<div class="desc">
									<a href="{{ route('admin.super.todo.detail', ['id' => $todo->id]) }}">{!! nl2br($todo->subject) !!}</a>&nbsp;&nbsp;
									<span class="label label-sm label-{{ strtolower(array_search($todo->priority, Todo::options('priority'))) }}">{{ array_search($todo->priority, Todo::options('priority')) }}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col2">
						<div class="date">{{ format_date(null, $todo->due_date) }}</div>
					</div>
				</li>
				@endforeach
			</ul>
		</div>
		<div class="scroller-footer">
			<div class="btn-arrow-link pull-right">
				<a href="{{ route('admin.super.todo.list') }}">View All</a>
				<i class="icon-arrow-right"></i>
			</div>
		</div>
	</div>
</div>