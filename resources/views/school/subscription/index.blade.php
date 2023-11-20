@extends('school.lteLayout.master')

@section('title')
    @lang('messages.school_subscription')
@endsection

@section('page_header')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ url('school/home') }}">@lang('messages.control_panel')</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ url('school/my_subscription') }}">@lang('messages.school_subscription')</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>@lang('messages.show')@lang('messages.school_subscription')</span>
            </li>
        </ul>
    </div>

    <h1 class="page-title">@lang('messages.show')@lang('messages.school_subscription')
        <small>@lang('messages.edit')@lang('messages.school_subscription')</small>
    </h1>
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>@lang('messages.school_subscription')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('school/home') }}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">@lang('messages.school_subscription')</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                @include('flash::message')
                <!-- Main content -->
                    <div class="invoice p-3 mb-3" id="barcode-svg">
                        <!-- Table row -->
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('messages.name')</th>
                                        <th>@lang('messages.email')</th>
                                        <th>@lang('messages.city')</th>
                                        <th>@lang('messages.identity_code')</th>
                                        <th>@lang('messages.status')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{$school->name}}</td>
                                        <td>{{$school->email}}</td>
                                        <td>{{app()->getLocale() == 'ar' ? $school->city->name_ar : $school->city->name_en}}</td>
                                        <td>{{$school->identity_code}}</td>
                                        <td>
                                            @if($school->status == 'active')
                                                <a class="btn btn-success">
                                                    @lang('messages.active')
                                                </a>
                                            @elseif($school->status == 'not_active')
                                                <a class="btn btn-dark">
                                                    @lang('messages.not_active')
                                                </a>
                                            @elseif($school->status == 'finished')
                                                <a class="btn btn-danger">
                                                    @lang('messages.finished')
                                                </a>
                                            @elseif($school->status == 'in_complete')
                                                <a class="btn btn-warning">
                                                    @lang('messages.in_complete')
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <div class="row">
                            <!-- accepted payments column -->
                            <div class="col-6">
                                <p class="lead">
                                    @lang('messages.payment_type'):
                                    @if($school->subscription->payment == 'true' and $school->subscription->payment_type == 'bank')
                                        @lang('messages.bank_transfer')
                                    @elseif($school->subscription->payment == 'true' and $school->subscription->payment_type == 'online')
                                        @lang('messages.online_payment')
                                    @else
                                        @lang('messages.not_found')
                                    @endif
                                </p>

                                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                    @if($school->subscription->payment == 'true' and $school->subscription->payment_type == 'bank' and $school->subscription->transfer_photo != null)
                                        @lang('messages.transfer_photo') :
                                        <button type="button" class="btn btn-success" data-toggle="modal"
                                                data-target="#modal-success-{{$school->subscription->id}}">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                <div class="modal fade" id="modal-success-{{$school->subscription->id}}">
                                    <div class="modal-dialog">
                                        <div class="modal-content bg-success">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('messages.transfer_photo')</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    <img
                                                        src="{{asset('/uploads/school_transfers/' . $school->subscription->transfer_photo)}}"
                                                        width="400" height="400">
                                                </p>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-outline-light"
                                                        data-dismiss="modal">@lang('messages.close')</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                @elseif($school->subscription->payment == 'true' and $school->subscription->payment_type == 'online' and $school->subscription->invoice_id != null)
                                    @lang('messages.invoice_id') : {{$school->subscription->invoice_id}}
                                    @endif
                                    </p>
                            </div>
                            <!-- /.col -->
                            <div class="col-6">
                                <p class="lead">@lang('messages.subscription_data')</p>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">@lang('messages.payment_status'):</th>
                                            <td>
                                                @if($school->subscription->payment == 'true')
                                                    <a class="btn btn-primary">
                                                        @lang('messages.paid')
                                                    </a>
                                                @else
                                                    <a class="btn btn-danger">
                                                        @lang('messages.not_paid')
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">@lang('messages.paid_at'):</th>
                                            <td>
                                                {{$school->subscription->paid_at->format('Y-m-d')}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">@lang('messages.subscription_end'):</th>
                                            <td>
                                                {{$school->subscription->end_at->format('Y-m-d')}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('messages.subscription_price')</th>
                                            <td>{{\App\Models\Setting::first()->school_subscribe_price}} @lang('messages.SR')</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('messages.discount'):</th>
                                            <td>{{$school->subscription->discount}}  @lang('messages.SR')</td>
                                        </tr>
                                        @if($school->subscription->seller_code)
                                            <tr>
                                                <th>@lang('messages.seller_code'):</th>
                                                <td>{{$school->subscription->seller_code->code}}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>@lang('messages.total'):</th>
                                            <td>{{$school->subscription->paid_amount}} @lang('messages.SR')</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <!-- this row will not appear when printing -->
                        <div class="row no-print">
                            <div class="col-12">
                                <a href="#" id="printPage" class="printPage btn btn-default">
                                    <i class="fas fa-print"></i>
                                    {{app()->getLocale() == 'ar' ? 'تحميل الفاتورة': 'Download Invoice'}}
                                </a>
                                @if($school->subscription->status == 'not_active' or $school->subscription->status == 'finished')
                                    <a href="{{route('pay_subscription' , $school->id)}}"
                                       class="btn btn-success float-right">
                                        <i class="far fa-credit-card"></i>
                                        @if($school->subscription->status == 'finished')
                                            {{app()->getLocale() == 'ar' ? 'تجديد الاشتراك': 'Renew Subscription'}}
                                        @else
                                            @lang('messages.pay_subscription')
                                        @endif
                                    </a>
                                @endif
                                {{--                                <a href="{{url('school/print_subscription_pdf')}}" target="_blank" class="btn btn-primary float-right" style="margin-right: 5px;">--}}
                                {{--                                    <i class="fas fa-download"></i> @lang('messages.generate_pdf')--}}
                                {{--                                </a>--}}
                            </div>
                        </div>
                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection
<script src="{{asset('/dist/js/html2canvas.min.js')}}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {

        document.getElementById("printPage").addEventListener("click", function () {
            html2canvas(document.getElementById("barcode-svg")).then(function (canvas) {
                var anchorTag = document.createElement("a");
                document.body.appendChild(anchorTag);
                // document.getElementById("previewImg").appendChild(canvas);
                anchorTag.download = "{{$school->name}}-invoice.jpg";
                anchorTag.href = canvas.toDataURL();
                anchorTag.target = '_blank';
                anchorTag.click();
            });
        });
    });
</script>
