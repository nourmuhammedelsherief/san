@extends('school.lteLayout.master')

@section('title')
    @lang('messages.edit') @lang('messages.teachers')
@endsection
@section('style')
    <link rel="stylesheet" href="{{ URL::asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-fileinput.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    {{-- selec2 cdn --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        .select2-container--default[dir="rtl"] .select2-selection--multiple .select2-selection__choice {
            background-color: #0e3b68;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.edit') @lang('messages.teachers') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/school/home')}}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('teachers.index')}}">
                                @lang('messages.teachers')
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
                            <h3 class="card-title">@lang('messages.edit') @lang('messages.teachers') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{route('teachers.update' , $teacher->id)}}" method="post"
                              enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{Session::token()}}'>

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="select2Multiple">@lang('messages.classrooms')</label>
                                    <select class="select2-multiple form-control" name="classrooms[]"
                                            multiple="multiple"
                                            id="select2Multiple">
                                        <option disabled> @lang('messages.choose_one') </option>
                                        @foreach($classrooms as $classroom)
                                            <option
                                                value="{{$classroom->id}}" {{\App\Models\Teacher\TeacherClassRoom::whereTeacherId($teacher->id)->whereClassroomId($classroom->id)->first() == null ? '' : 'selected'}}> {{$classroom->name}} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('classrooms'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('classrooms') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="select2Multiple">@lang('messages.subjects')</label>
                                    <select class="select2-multiple form-control" name="subjects[]" multiple="multiple"
                                            id="select2Multiple">
                                        <option disabled> @lang('messages.choose_one') </option>
                                        @foreach($subjects as $subject)
                                            <option
                                                value="{{$subject->id}}" {{\App\Models\Teacher\TeacherSubject::whereTeacherId($teacher->id)->whereSubjectId($subject->id)->first() == null ? '' : 'selected'}}> {{app()->getLocale() == 'ar' ? $subject->name_ar : $subject->name_en}} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('subjects'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('subjects') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.city') </label>
                                    <select name="city_id" class="form-control">
                                        <option selected disabled> @lang('messages.choose_one') </option>
                                        @foreach($cities as $city)
                                            <option
                                                value="{{$city->id}}" {{$teacher->city_id == $city->id ? 'selected' : ''}}> {{app()->getLocale() == 'ar' ? $city->name_ar : $city->name_en}} </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('city_id'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('city_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.name') </label>
                                    <input name="name" type="text" class="form-control" value="{{$teacher->name}}"
                                           placeholder="@lang('messages.name')">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.email') </label>
                                    <input name="email" type="email" class="form-control" value="{{$teacher->email}}"
                                           placeholder="@lang('messages.email')">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.password') </label>
                                    <input name="password" type="password" class="form-control"
                                           placeholder="@lang('messages.password')">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.password_confirmation') </label>
                                    <input name="password_confirmation" type="password" class="form-control"
                                           placeholder="@lang('messages.password_confirmation')">
                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong
                                                style="color: red;">{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.activation') </label>
                                    <input name="active" {{$teacher->active == 'true' ? 'checked' : ''}} type="radio"
                                           value="true"> @lang('messages.yes')
                                    <input name="active" {{$teacher->active == 'false' ? 'checked' : ''}} type="radio"
                                           value="false"> @lang('messages.no')
                                    @if ($errors->has('active'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('active') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.whatsapp_activation') </label>
                                    <input name="whatsapp"
                                           {{$teacher->whatsapp == 'true' ? 'checked' : ''}} type="radio"
                                           value="true"> @lang('messages.yes')
                                    <input name="whatsapp"
                                           {{$teacher->whatsapp == 'false' ? 'checked' : ''}} type="radio"
                                           value="false"> @lang('messages.no')
                                    @if ($errors->has('whatsapp'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('whatsapp') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.phone_number') </label>
                                    <input name="phone_number" type="text" class="form-control"
                                           value="{{$teacher->phone_number}}"
                                           placeholder="@lang('messages.phone_number')">
                                    @if ($errors->has('phone_number'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('phone_number') }}</strong>
                                        </span>
                                    @endif
                                </div>

{{--                                <div class="form-group ">--}}
{{--                                    <label class="control-label col-md-3"> @lang('messages.photo') </label>--}}
{{--                                    <div class="col-md-9">--}}
{{--                                        <div class="fileinput fileinput-new" data-provides="fileinput">--}}
{{--                                            <div class="fileinput-preview thumbnail" data-trigger="fileinput"--}}
{{--                                                 style="width: 200px; height: 150px; border: 1px solid black;">--}}
{{--                                                @if($teacher->photo != null)--}}
{{--                                                    <img src="{{asset('/uploads/teachers/' . $teacher->photo)}}">--}}
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Select2 Multiple
            $('.select2-multiple').select2({
                placeholder: "Select",
                allowClear: true
            });

        });

    </script>
@endsection
