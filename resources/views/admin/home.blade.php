@extends('admin.lteLayout.master')

@section('title')
    @lang('messages.control_panel')
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('messages.control_panel')</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">@lang('messages.control_panel')</a></li>
                        {{--                        <li class="breadcrumb-item active">Dashboard v1</li>--}}
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/admins')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-users"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.admins')</span>
                                <span class="info-box-number">
                                {{\App\Models\Admin::count()
                                 }}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/teachers/active')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-users"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.teachers')</span>
                                <span class="info-box-number">
                                {{\App\Models\Teacher\Teacher::whereType('free')->count()
                                 }}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/schools/active')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-gray elevation-1">
                            <i class="fas fa-building"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.schools')</span>
                                <span class="info-box-number">
                                {{\App\Models\School\School::count()
                                 }}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/classrooms')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-graduation-cap"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.classrooms')</span>
                                <span class="info-box-number">
                                {{\App\Models\Classroom::where('school_id' , null)->count()}}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/parents')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-users"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.parents')</span>
                                <span class="info-box-number">
                                {{\App\Models\Father\Father::count()}}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/cities')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-gray elevation-1">
                            <i class="fas fa-building"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.cities')</span>
                                <span class="info-box-number">
                                    {{\App\Models\School\City::count()
                                 }}
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/subjects')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-book-open"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.subjects')</span>
                                <span class="info-box-number">
                                    {{\App\Models\Subject::count()}}
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/seller_codes')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-dark elevation-1">
                            <i class="fas fa-code"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.seller_codes')</span>
                                <span class="info-box-number">
                                    {{\App\Models\SellerCode::count()}}
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/sliders')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-images"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.sliders')</span>
                                <span class="info-box-number">
                                    {{\App\Models\Slider::count()}}
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/admin/histories')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-money-bill"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.histories')</span>
                                <span class="info-box-number">
                                    {{\App\Models\History::count()}}
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="clearfix hidden-md-up"></div>
{{--                <div class="col-12 col-sm-6 col-md-3">--}}
{{--                    <a href="{{url('/admin/bank_transfers')}}">--}}
{{--                        <div class="info-box">--}}
{{--                        <span class="info-box-icon bg-danger elevation-1">--}}
{{--                            <i class="fas fa-money-bill"></i>--}}
{{--                        </span>--}}

{{--                            <div class="info-box-content">--}}
{{--                                <span class="info-box-text">@lang('messages.bank_transfers')</span>--}}
{{--                                <span class="info-box-number">--}}
{{--                                    {{ \App\Models\UserCourse::whereStatus('not_active')--}}
{{--                                ->wherePayment('false')--}}
{{--                                ->where('transfer_photo' , '!=' , null)--}}
{{--                                ->count()}}--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                            <!-- /.info-box-content -->--}}
{{--                        </div>--}}
{{--                    </a>--}}
{{--                    <!-- /.info-box -->--}}
{{--                </div>--}}
{{--                <div class="col-12 col-sm-6 col-md-3">--}}
{{--                    <a href="{{url('/admin/histories')}}">--}}
{{--                        <div class="info-box">--}}
{{--                        <span class="info-box-icon bg-warning elevation-1">--}}
{{--                            <i class="fas fa-cogs"></i>--}}
{{--                        </span>--}}

{{--                            <div class="info-box-content">--}}
{{--                                <span class="info-box-text">@lang('messages.histories')</span>--}}
{{--                                <span class="info-box-number">--}}
{{--                                     {{ \App\Models\History::count()}}--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                            <!-- /.info-box-content -->--}}
{{--                        </div>--}}
{{--                    </a>--}}
{{--                    <!-- /.info-box -->--}}
{{--                </div>--}}
{{--                <div class="col-12 col-sm-6 col-md-3">--}}
{{--                    <a href="{{url('/admin/contacts')}}">--}}
{{--                        <div class="info-box">--}}
{{--                        <span class="info-box-icon bg-primary elevation-1">--}}
{{--                            <i class="fas fa-phone"></i>--}}
{{--                        </span>--}}

{{--                            <div class="info-box-content">--}}
{{--                                <span class="info-box-text">@lang('messages.contact_us')</span>--}}
{{--                                <span class="info-box-number">--}}
{{--                                     {{ \App\Models\ContactUs::count()}}--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                            <!-- /.info-box-content -->--}}
{{--                        </div>--}}
{{--                    </a>--}}
{{--                    <!-- /.info-box -->--}}
{{--                </div>--}}
                <!-- fix for small devices only -->
                <div class="clearfix hidden-md-up"></div>

                <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
