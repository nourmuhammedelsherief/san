@extends('school.lteLayout.master')

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
    <?php $school = Auth::guard('school')->user(); ?>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if($school->status == 'finished')
                <h3 class="text-center" style="color: red">
                    {{app()->getLocale() == 'ar' ? 'هذا الحساب منتهي يجب تجديد الاشتراك حتي يتمكن المعلمين من استخدام التطبيق' : 'this account is finished got to activate it'}}
                </h3>
            @endif
        <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/school/classrooms')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-school"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.classrooms')</span>
                                <span class="info-box-number">
                                {{\App\Models\Classroom::whereSchoolId($school->id)->count()}}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/school/rates')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fa fa-star"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.rates')</span>
                                <span class="info-box-number">
                                {{\App\Models\School\SchoolRate::whereSchoolId($school->id)->count()}}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/school/rewards')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fa fa-gifts"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.rewards')</span>
                                <span class="info-box-number">
                               {{\App\Models\School\SchoolReward::whereSchoolId($school->id)->count()}}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/school/students')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-users"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.students')</span>
                                <span class="info-box-number">
                                {{\App\Models\Student::with('classroom')
                                ->whereHas('classroom' , function ($q){
                                    $q->whereSchoolId(auth()->guard('school')->user()->id);
                                })->count()}}
                            </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
                <div class="clearfix hidden-md-up"></div>
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="{{url('/school/teachers')}}">
                        <div class="info-box">
                        <span class="info-box-icon bg-gray elevation-1">
                            <i class="fas fa-users"></i>
                        </span>

                            <div class="info-box-content">
                                <span class="info-box-text">@lang('messages.teachers')</span>
                                <span class="info-box-number">
                                    {{\App\Models\Teacher\Teacher::with('teacher_classrooms')
                                ->whereHas('teacher_classrooms', function ($q) {
                                    $q->with('classroom');
                                    $q->whereHas('classroom', function ($c) {
                                        $c->whereSchoolId(auth()->guard('school')->user()->id);
                                    });
                                })->count()}}
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </a>
                    <!-- /.info-box -->
                </div>
            {{--                <div class="col-12 col-sm-6 col-md-3">--}}
            {{--                    <a href="{{url('/admin/courses')}}">--}}
            {{--                        <div class="info-box">--}}
            {{--                        <span class="info-box-icon bg-danger elevation-1">--}}
            {{--                            <i class="fas fa-graduation-cap"></i>--}}
            {{--                        </span>--}}

            {{--                            <div class="info-box-content">--}}
            {{--                                <span class="info-box-text">@lang('messages.courses')</span>--}}
            {{--                                <span class="info-box-number">--}}
            {{--                                    {{\App\Models\Course::count()}}--}}
            {{--                                </span>--}}
            {{--                            </div>--}}
            {{--                            <!-- /.info-box-content -->--}}
            {{--                        </div>--}}
            {{--                    </a>--}}
            {{--                    <!-- /.info-box -->--}}
            {{--                </div>--}}
            {{--                <div class="col-12 col-sm-6 col-md-3">--}}
            {{--                    <a href="{{url('/admin/sliders')}}">--}}
            {{--                        <div class="info-box">--}}
            {{--                        <span class="info-box-icon bg-dark elevation-1">--}}
            {{--                            <i class="fas fa-image"></i>--}}
            {{--                        </span>--}}

            {{--                            <div class="info-box-content">--}}
            {{--                                <span class="info-box-text">@lang('messages.sliders')</span>--}}
            {{--                                <span class="info-box-number">--}}
            {{--                                    {{\App\Models\Slider::count()}}--}}
            {{--                                </span>--}}
            {{--                            </div>--}}
            {{--                            <!-- /.info-box-content -->--}}
            {{--                        </div>--}}
            {{--                    </a>--}}
            {{--                    <!-- /.info-box -->--}}
            {{--                </div>--}}
            {{--                <div class="col-12 col-sm-6 col-md-3">--}}
            {{--                    <a href="{{url('/admin/pages')}}">--}}
            {{--                        <div class="info-box">--}}
            {{--                        <span class="info-box-icon bg-success elevation-1">--}}
            {{--                            <i class="fas fa-pager"></i>--}}
            {{--                        </span>--}}

            {{--                            <div class="info-box-content">--}}
            {{--                                <span class="info-box-text">@lang('messages.pages')</span>--}}
            {{--                                <span class="info-box-number">--}}
            {{--                                    {{\App\Models\Page::count()}}--}}
            {{--                                </span>--}}
            {{--                            </div>--}}
            {{--                            <!-- /.info-box-content -->--}}
            {{--                        </div>--}}
            {{--                    </a>--}}
            {{--                    <!-- /.info-box -->--}}
            {{--                </div>--}}
            {{--                <div class="clearfix hidden-md-up"></div>--}}
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
