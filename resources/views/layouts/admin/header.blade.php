<div class="navbar-header">
  <div class="logo">
    <a href="{{ url('/') }}">iJob<span>Desk</span></a>
  </div>
  <a href="#" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"></a>
</div>
<div class="nav navbar-right top-nav">
  <ul class="nav navbar-nav pull-right clearfix">
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
        <img class="img-circle" src="{{ avatar_url($auth_user, 32) }}" width="32" height="32">
        <span class="username">{{ $auth_user->fullname() }}</span>
        <i class="fa fa-angle-down"></i>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a href="extra_profile.html">
          <i class="icon-user"></i> My Profile </a>
        </li>
        <li class="divider">
        </li>
        <li>
          <a href="{{ route('user.logout') }}">
          <i class="icon-key"></i> Log Out </a>
        </li>
      </ul>
    </li>
  </ul>
</div>