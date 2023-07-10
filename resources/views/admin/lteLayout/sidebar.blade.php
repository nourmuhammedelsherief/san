<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #0e3b68 !important;">


    <!-- Brand Logo -->
    <a href="{{url('/admin/home')}}" class="brand-link">
        <img src="{{asset('/uploads/Logo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">@lang('messages.control_panel')</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{asset('lte/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{url('/admin/home')}}" class="d-block">
                    <?php if (Auth::guard('admin')->check()) {
                        echo Auth::guard('admin')->user()->name;
                    } ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <?php $admin = Auth::guard('admin')->user(); ?>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{url('/admin/profile')}}"
                       class="nav-link {{ strpos(URL::current(), '/admin/profile') !== false ? 'active' : '' }}">
                        <i class="nav-icon far fa-user"></i>
                        <p>
                            @lang('messages.profile')
                        </p>
                    </a>
                </li>
                <li class="nav-item has-treeview {{ strpos(URL::current(), 'admins') !== false ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ strpos(URL::current(), 'admins') !== false ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            @lang('messages.admins')
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/admin/admins') }}"
                               class="nav-link {{ strpos(URL::current(), '/admin/admins') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.admins')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/admins/create') }}"
                               class="nav-link {{ strpos(URL::current(), '/admin/admins/create') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.add_admin')
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{route('settings.index')}}"
                       class="nav-link {{ strpos(URL::current(), '/admin/settings') !== false ? 'active' : '' }}">
                        <i class="fa fa-cog"></i>
                        <p>
                            @lang('messages.settings')
                        </p>
                    </a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a href="{{route('about')}}"--}}
{{--                       class="nav-link {{ strpos(URL::current(), '/admin/about_us') !== false ? 'active' : '' }}">--}}
{{--                        <i class="fa fa-cog"></i>--}}
{{--                        <p>--}}
{{--                            @lang('messages.about_us')--}}
{{--                        </p>--}}
{{--                    </a>--}}
{{--                </li>--}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
