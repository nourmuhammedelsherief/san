<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #0e3b68 !important;">


    <!-- Brand Logo -->
    <a href="{{url('/admin/home')}}" class="brand-link">
        <img src="{{asset('/uploads/' . \App\Models\Setting::first()->logo)}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
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
                    <a href="{{route('cities.index')}}"
                       class="nav-link {{ strpos(URL::current(), '/admin/cities') !== false ? 'active' : '' }}">
                        <i class="fa fa-building"></i>
                        <span class="badge badge-info right">
                            {{\App\Models\School\City::count()}}
                        </span>
                        <p>
                            @lang('messages.cities')
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('subjects.index')}}"
                       class="nav-link {{ strpos(URL::current(), '/admin/subjects') !== false ? 'active' : '' }}">
                        <i class="fa fa-book-open"></i>
                        <span class="badge badge-info right">
                            {{\App\Models\Subject::count()}}
                        </span>
                        <p>
                            @lang('messages.subjects')
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('seller_codes.index')}}"
                       class="nav-link {{ strpos(URL::current(), '/admin/seller_codes') !== false ? 'active' : '' }}">
                        <i class="fa fa-code"></i>
                        <span class="badge badge-info right">
                            {{\App\Models\SellerCode::count()}}
                        </span>
                        <p>
                            @lang('messages.seller_codes')
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('sliders.index')}}"
                       class="nav-link {{ strpos(URL::current(), '/admin/sliders') !== false ? 'active' : '' }}">
                        <i class="fa fa-images"></i>
                        <span class="badge badge-info right">
                            {{\App\Models\Slider::count()}}
                        </span>
                        <p>
                            @lang('messages.sliders')
                        </p>
                    </a>
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
