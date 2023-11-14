@extends('school.lteLayout.master')

@section('title')
    @lang('messages.edit') @lang('messages.rewards')
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
                    <h1> @lang('messages.edit') @lang('messages.rewards') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/school/home')}}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('rewards.index')}}">
                                @lang('messages.rewards')
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
                    <!-- general form elements -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.edit') @lang('messages.rewards') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{route('rewards.update' , $reward->id)}}" method="post" enctype="multipart/form-data">
                            <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>

                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.name') </label>
                                    <input name="name" type="text" class="form-control" value="{{$reward->name}}" placeholder="@lang('messages.name')">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.points') </label>
                                    <input name="points" type="number" class="form-control" value="{{$reward->points}}" placeholder="@lang('messages.points')">
                                    @if ($errors->has('points'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('points') }}</strong>
                                        </span>
                                    @endif
                                </div>

{{--                                <div class="form-group ">--}}
{{--                                    <label class="control-label col-md-3"> @lang('messages.photo') </label>--}}
{{--                                    <div class="col-md-9">--}}
{{--                                        <div class="fileinput fileinput-new" data-provides="fileinput">--}}
{{--                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput"--}}
{{--                                                 style="width: 200px; height: 150px; border: 1px solid black;">--}}
{{--                                                @if($reward->photo != null)--}}
{{--                                                    <img src="{{asset('/uploads/rewards/' . $reward->photo)}}">--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                            <div>--}}
{{--                                                <span class="btn red btn-outline btn-file">--}}
{{--                                                    <span--}}
{{--                                                        class="fileinput-new btn btn-info"> @lang('messages.choose_photo') </span>--}}
{{--                                                    <span--}}
{{--                                                        class="fileinput-exists btn btn-primary"> @lang('messages.change') </span>--}}
{{--                                                    <input type="file" name="photo"> </span>--}}
{{--                                                <a href="javascript:;" class="btn btn-danger fileinput-exists"--}}
{{--                                                   data-dismiss="fileinput"> @lang('messages.remove') </a>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        @if ($errors->has('photo'))--}}
{{--                                            <span class="help-block">--}}
{{--                                                <strong style="color: red;">{{ $errors->first('photo') }}</strong>--}}
{{--                                            </span>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}

{{--                                </div>--}}


                            </div>
                            <!-- /.card-body -->

                            @method('PUT')
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
