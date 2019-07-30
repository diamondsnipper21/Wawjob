<div class=navbar-header>
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="{{ $current_user ? route('user.dashboard') : '/' }}"><img src="/assets/images/common/logo.png" alt="{{ config('app.name') }}" /></a>
</div>