@extends('admin.lteLayout.master')

@section('title')
    @lang('messages.edit') @lang('messages.about_us')
@endsection
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-fileinput.css') }}">
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.edit') @lang('messages.about_us') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/admin/home')}}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('about')}}">
                                @lang('messages.about_us')
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    @include('flash::message')
                    <!-- general form elements -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.edit') @lang('messages.about_us') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{route('update_about')}}" method="post" enctype="multipart/form-data">
                            <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.about_ar') </label>
                                    <textarea class="textarea" name="about_ar" placeholder="@lang('messages.about_ar')"
                                              style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; pediting: 10px;">{{$about->about_ar}}</textarea>
                                    @if ($errors->has('about_ar'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('about_ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.about_en') </label>
                                    <textarea class="textarea" name="about_en" placeholder="@lang('messages.about_en')"
                                              style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; pediting: 10px;">{{$about->about_en}}</textarea>
                                    @if ($errors->has('about_en'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('about_en') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('scripts')
    <script src="{{ URL::asset('js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('js/components-select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/bootstrap-fileinput.js') }}"></script>
@endsection
