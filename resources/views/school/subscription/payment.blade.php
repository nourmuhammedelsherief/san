@extends('school.lteLayout.master')

@section('title')
    @lang('messages.profile')
@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ url('school/home') }}">@lang('messages.control_panel')</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ url('school/profile') }}">@lang('messages.profile')</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>@lang('messages.show')@lang('messages.profile')</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">@lang('messages.show')@lang('messages.profile')
        <small>@lang('messages.edit')@lang('messages.profile')</small>
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
                            <a href="{{url('/school/home')}}">
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
                            <h3 class="card-title">@lang('messages.main_data')</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" method="post" action="{{route('submit_subscription' , $school->id)}}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                @php
                                    $setting = \App\Models\Setting::first();
                                @endphp
                                <div class="form-group">
                                    <label for="username">@lang('messages.payment_type')</label>
                                    <select name="payment_method" class="form-control" onchange="showDiv(this)">
                                        <option disabled selected> @lang('messages.choose_one') </option>
                                        @if($setting->payment_type == 'both' or $setting->payment_type == 'bank')
                                            <option value="bank"> @lang('messages.bank_transfer') </option>
                                        @endif
                                        @if($setting->payment_type == 'both' or $setting->payment_type == 'online')
                                            <option value="online"> @lang('messages.online_payment') </option>
                                        @endif
                                    </select>
                                    @if ($errors->has('payment_method'))
                                        <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('payment_method') }}</strong>
                                    </span>
                                    @endif

                                </div>
                                <div id="bank" style="display: none">
                                    <div class="form-group">
                                        <?php  $setting = \App\Models\Setting::first(); ?>
                                        <p style="color: #ff224f"> Bank : {{$setting->bank_name}} </p>
                                        <hr>
                                        <p
                                            style="color: #ff224f"> Account Number : {{$setting->account_number}} </p>
                                        <hr>
                                        <p style="color: #ff224f"> IBAN Number : {{$setting->Iban_number}} </p>
                                    </div>
                                    <div class="form-group">
                                        <input type="file" name="transfer_photo" class="form-control">
                                        @if ($errors->has('transfer_photo'))
                                            <div class="alert alert-danger">
                                                <button class="close" data-close="alert"></button>
                                                <span> {{ $errors->first('transfer_photo') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div id="online" style="display: none">
                                    <div class="form-group">
                                        <select name="online_type" class="form-control">
                                            <option disabled selected> @lang('messages.choose_payment_method') </option>
                                            <option value="visa"> @lang('messages.visa') </option>
                                            <option value="mada"> @lang('messages.mada') </option>
                                            <option value="apple_pay"> @lang('messages.apple_pay') </option>
                                        </select>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-money-bill"></span>
                                            </div>
                                        </div>
                                        @if ($errors->has('online_type'))
                                            <div class="alert alert-danger">
                                                <button class="close" data-close="alert"></button>
                                                <span> {{ $errors->first('online_type') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="username">@lang('messages.seller_code')</label>
                                    <input type="text" name="seller_code" value="{{old('seller_code')}}"
                                           class="form-control" placeholder="@lang('messages.putSellerCodeHere')">
                                    @if ($errors->has('seller_code'))
                                        <span class="help-block">
                                       <strong style="color: red;">{{ $errors->first('seller_code') }}</strong>
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
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

@endsection

<script>
    function showDiv(element) {
        if (element.value == 'online') {
            document.getElementById('online').style.display = 'block';
            document.getElementById('bank').style.display = 'none';
        } else if (element.value == 'bank') {
            document.getElementById('bank').style.display = 'block';
            document.getElementById('online').style.display = 'none';
        }
    }
</script>
