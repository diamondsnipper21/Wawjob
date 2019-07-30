@if (!$current_user)
<div class="redirect-login-block">
	<div class="default-boxshadow text-center">
		<a href="{{ route('user.login') }}" class="icon login-icon"><i class="icon-login"></i></a>
        <div>{{ trans('home.help.redirect_login.title') }}</div>
        <a href="{{ route('user.signup') }}">{{ trans('home.help.redirect_login.question') }}</a>
	</div>
</div>
@else
<div class="submit-ticket default-boxshadow text-center">
    <a href="{{ route('ticket.list') . '?_action=new' }}" class="icon"><i class="icon-envelope-open"></i></a>
    <div>{{ trans('home.help.submit_ticket.title') }}</div>
    <p>{{ trans('home.help.submit_ticket.desc') }}</p>
</div>
@endif