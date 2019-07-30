{{-- Result Section begin--}}
<!-- User Listing Part -->
<div class="col-sm-12">
    <div class="notification-list-wrap">
        @forelse ($notification_list as $notification)
            @include('pages.notification.list_row')
        @empty
        <div class="no-items">{{ trans('common.no_notifications') }}</div>
        @endforelse
    </div>
</div>
{{-- Result Section end--}}
<div class="col-sm-12">
    <div class="pull-right">
        {!! $notification_list->render() !!}
    </div>
</div>