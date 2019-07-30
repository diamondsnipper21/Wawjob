<!-- BEGIN HEADER -->
<div class="header-wrapper default-boxshadow">
    <div class="header">
        <div class="header-section container">
            <nav class="navbar">
                <div class=container-fluid>
                    @include('layouts.section.logo')

                    @if ($page != 'auth.login')
                    <ul class="nav navbar-nav navbar-right guest-right-menu">
                        <li class="no-hover-effect">
                            <span>{{ trans('page.auth.signup.have_an_account') }}</span>
                            <a href="{{ route('user.login') }}" class="text-uppercase"><i class="icon-login"></i>{{ trans('page.auth.login.title_with_space') }}</a>
                        </li>
                    </ul>
                    @endif
                </div>
            </nav>

        </div>
    </div>
</div>
<!-- END HEADER -->