@extends('admin.lteLayout.master')

@section('title')
    @lang('messages.public_notifications')
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
                    <h1> @lang('messages.public_notifications') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/admin/home')}}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('public_notification')}}">
                                @lang('messages.public_notifications')
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
                            <h3 class="card-title">@lang('messages.public_notifications') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{route('store_public_notification')}}" method="post" enctype="multipart/form-data">
                            <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>

                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.select_type') </label>
                                    <select name="type" class="form-control">
                                        <option disabled selected> @lang('messages.choose_one') </option>
                                        <option value="teachers">@lang('messages.teachers')</option>
                                        <option value="fathers">@lang('messages.parents')</option>
                                        <option value="students">@lang('messages.students')</option>
                                        <option value="all">@lang('messages.all')</option>
                                    </select>
                                    @if ($errors->has('type'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.title') </label>
                                    <input name="title" type="text" class="form-control" value="{{old('title')}}" placeholder="@lang('messages.title')">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.message') </label>
                                    <textarea name="message" class="form-control" rows="5"></textarea>
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">@lang('messages.send')</button>
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
