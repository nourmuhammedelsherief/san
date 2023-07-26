@extends('admin.lteLayout.master')

@section('title')
    المشرفين
@endsection



@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ url('admin/home') }}">لوحة التحكم</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ route('admins.index') }}">المشرفين</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>عرض الصفحة الشخصية</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">عرض الصفحة الشخصية
        <small>تعديل الصفحة الشخصية</small>
    </h1>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>@lang('messages.profile')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/admin/home')}}">
                                @lang('messages.control_panel')
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            @lang('messages.profile')
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    @include('flash::message')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-6">
                <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">البيانات الرئيسية</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" method="post" action="{{ url('/admin/profileEdit') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="username">الاسم</label>
                                    <input id="username" name="name" type="text" value="{{ $data->name }}"
                                           class="form-control" placeholder="الاسم">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif

                                </div>

                                <div class="form-group">
                                    <label for="email">الايميل</label>
                                    <input id="email" name="email" type="email" value="{{ $data->email }}"
                                           class="form-control" placeholder="الايميل">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif

                                </div>

                                <div class="form-group">
                                    <label for="phone">الهاتف</label>
                                    <input id="phone" name="phone" type="text" value="{{ $data->phone }}"
                                           class="form-control" placeholder="الهاتف">
                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <!-- /.card-body -->


                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"
                                        onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">
                                    حفظ
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->

                </div>
                <div class="col-md-6">
                    <!-- general form elements -->
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.change_password')</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form method="post" action="{{ url('/admin/profileChangePass') }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="password" class="control-label">@lang('messages.password')</label>
                                    <input id="password" name="password" type="password"
                                           class="form-control" required>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="password_confirm" class="control-label">@lang('messages.password_confirmation')
                                    </label>
                                    <input id="password_confirm" name="password_confirmation"
                                           type="password" class="form-control" required>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                       <strong
                                           style="color: red;">{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>


                                <div style="clear: both"></div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"
                                            onclick="this.disabled=true;this.value='تم الارسال, انتظر...';this.form.submit();">
                                        @lang('messages.save')
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!-- /.card -->

                </div>
                <!--/.col (left) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

@endsection
