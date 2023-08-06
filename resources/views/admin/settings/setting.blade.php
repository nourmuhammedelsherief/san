@extends('admin.lteLayout.master')

@section('title')
    @lang('messages.settings')
@endsection

@section('styles')

@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.edit') @lang('messages.settings') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/admin/home')}}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('settings.index')}}">
                                @lang('messages.settings')
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.edit') @lang('messages.settings') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{route('store_setting' , $setting->id)}}" method="post" enctype="multipart/form-data">
                            <input type = 'hidden' name = '_token' value = '{{Session::token()}}'>

                            <div class="card-body">
                                <h3 class="text-center"> بيانات البنك </h3>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.bank_name') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="bank_name" type="text" class="form-control" value="{{$setting->bank_name}}" placeholder="@lang('messages.bank_name')">
                                            @if ($errors->has('bank_name'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('bank_name') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.account_number') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="account_number" type="text" class="form-control" value="{{$setting->account_number}}" placeholder="@lang('messages.account_number')">
                                            @if ($errors->has('account_number'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('account_number') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.Iban_number') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="Iban_number" type="text" class="form-control" value="{{$setting->Iban_number}}" placeholder="@lang('messages.Iban_number')">
                                            @if ($errors->has('Iban_number'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('Iban_number') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h3 class="text-center"> بيانات الدفع الاونلاين </h3>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.online_token') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="online_token" type="text" class="form-control" value="{{$setting->online_token}}" placeholder="@lang('messages.online_token')">
                                            @if ($errors->has('Iban_number'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('online_token') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h3 class="text-center"> بيانات الرسائل النصية </h3>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.bearer_token') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="bearer_token" type="text" class="form-control" value="{{$setting->bearer_token}}" placeholder="@lang('messages.bearer_token')">
                                            @if ($errors->has('bearer_token'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('bearer_token') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.sender_name') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="sender_name" type="text" class="form-control" value="{{$setting->sender_name}}" placeholder="@lang('messages.sender_name')">
                                            @if ($errors->has('sender_name'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('sender_name') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <h3 class="text-center"> سعر الاشتراك للمدارس والمعلمين </h3>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.school_subscribe_price') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="school_subscribe_price" type="number" class="form-control" value="{{$setting->school_subscribe_price}}" placeholder="@lang('messages.school_subscribe_price')">
                                            @if ($errors->has('school_subscribe_price'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('school_subscribe_price') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-2">@lang('messages.SR')</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.teacher_subscribe_price') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="teacher_subscribe_price" type="number" class="form-control" value="{{$setting->teacher_subscribe_price}}" placeholder="@lang('messages.teacher_subscribe_price')">
                                            @if ($errors->has('teacher_subscribe_price'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('teacher_subscribe_price') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-2">@lang('messages.SR')</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.invitation_code_discount') </label>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <input name="invitation_code_discount" type="number" class="form-control" value="{{$setting->invitation_code_discount}}" placeholder="@lang('messages.invitation_code_discount')">
                                            @if ($errors->has('invitation_code_discount'))
                                                <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('invitation_code_discount') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-2">%</div>
                                    </div>
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
    <script>
        $(document).ready(function() {
            $(document).on('submit', 'form', function() {
                $('button').attr('disabled', 'disabled');
            });
        });
    </script>
@endsection
