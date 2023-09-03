<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link " id="btn-sidebar-menu" data-widget="pushmenux" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{url('/school/home')}}" class="nav-link">@lang('messages.control_panel')</a>
        </li>
        {{--        <li class="nav-item d-none d-sm-inline-block">--}}
        {{--            <a href="#" class="nav-link">Contact</a>--}}
        {{--        </li>--}}
        <li class="nav-item d-none d-sm-inline-block">
            {{--            <a href="#" class="nav-link">Lang</a>--}}
            @if(app()->getLocale() == 'en')
                <a href="{{ url('locale/ar')  }}" class="nav-link">
                    <span class="username username-hide-on-mobile">
                        عربى
                    </span>
                </a>
            @else
                <a href="{{  url('locale/en') }}" class="nav-link">
                    <span class="username username-hide-on-mobile">
                        English
                    </span>
                </a>
            @endif
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav mr-auto-navbav">
        <li class="nav-item">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <?php if (Auth::guard('school')->check()) {
                    echo Auth::guard('school')->user()->name;
                } ?>
                <span class="badge badge-warning navbar-badge"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                {{--                <span class="dropdown-item dropdown-header"> @lang('messages.profile') </span>--}}
                {{--                <div class="dropdown-divider"></div>--}}
                {{--                <a href="{{url('/admin/profile')}}" class="dropdown-item">--}}
                {{--                    <i class="fas fa-user"></i>--}}
                {{--                    @lang('messages.profile')--}}
                {{--                </a>--}}
                {{--                <div class="dropdown-divider"></div>--}}
                {{--                <a href="{{url('/admin/profileChangePass')}}" class="dropdown-item">--}}
                {{--                    <i class="fas fa-user"></i>--}}
                {{--                    @lang('messages.changPassword')--}}
                {{--                </a>--}}
                <div class="dropdown-divider"></div>
                <a onclick="document.getElementById('logout_form').submit()" class="dropdown-item">
                    <i class="fas fa-key"></i>
                    @lang('messages.logout')
                </a>
            </div>
        </li>
    </ul>
    <form style="display: none;" id="logout_form" action="{{ route('school.logout') }}" method="post">
        {!! csrf_field() !!}
    </form>
</nav>
<!-- /.navbar -->
