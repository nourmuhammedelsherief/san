<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@lang('messages.control_panel') | @lang('messages.school_subscription_payment')</title>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{asset('lte/plugins/fontawesome-free/css/all.min.css')}}">

    <link rel="stylesheet" href="{{asset('lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{asset('lte/dist/css/adminlte.min.css?v=3.2.0')}}">


    <style>
        .login-logo .change-lang {
            position: absolute;
            top: 24px;
            left: 17px;
            font-size: 1rem;
            font-weight: bold;
        }
    </style>

    <script nonce="376f936e-348d-4d08-9793-4830326ff13f">(function (w, d) {
            !function (a, e, t, r) {
                a.zarazData = a.zarazData || {}, a.zarazData.executed = [], a.zaraz = {deferred: []}, a.zaraz.q = [], a.zaraz._f = function (e) {
                    return function () {
                        var t = Array.prototype.slice.call(arguments);
                        a.zaraz.q.push({m: e, a: t})
                    }
                };
                for (const e of ["track", "set", "ecommerce", "debug"]) a.zaraz[e] = a.zaraz._f(e);
                a.addEventListener("DOMContentLoaded", (() => {
                    var t = e.getElementsByTagName(r)[0], z = e.createElement(r),
                        n = e.getElementsByTagName("title")[0];
                    for (a.zarazData.c = e.cookie, n && (a.zarazData.t = e.getElementsByTagName("title")[0].text), a.zarazData.w = a.screen.width, a.zarazData.h = a.screen.height, a.zarazData.j = a.innerHeight, a.zarazData.e = a.innerWidth, a.zarazData.l = a.location.href, a.zarazData.r = e.referrer, a.zarazData.k = a.screen.colorDepth, a.zarazData.n = e.characterSet, a.zarazData.o = (new Date).getTimezoneOffset(), a.zarazData.q = []; a.zaraz.q.length;) {
                        const e = a.zaraz.q.shift();
                        a.zarazData.q.push(e)
                    }
                    z.defer = !0, z.referrerPolicy = "origin", z.src = "/cdn-cgi/zaraz/s.js?z=" + btoa(encodeURIComponent(JSON.stringify(a.zarazData))), t.parentNode.insertBefore(z, t)
                }))
            }(w, d, 0, "script");
        })(window, document);</script>
</head>
<body class="hold-transition login-page">

<div class="login-box">
    @if (session('An_error_occurred'))
        <div class="alert alert-success">
            {{ session('An_error_occurred') }}
        </div>
    @endif
    @if (session('warning_login'))
        <div class="alert alert-danger">
            {{ session('warning_login') }}
        </div>
    @endif
    <div class="login-logo">
        <a href="#">@lang('messages.school_subscription_payment')</a>
        <br>
        <a href="#" style="color: red">({{$amount}}
            ) @lang('messages.SR') </a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            {{--            <h4 class="text-center mb-4">{{trans('messages.dash_school')}}</h4>--}}
            <form action="{{route('school.submit_register_payment' , $school->id)}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="amount" value="{{$amount}}">
                <input type="hidden" name="seller_code_id" value="{{$seller_code_id}}">
                <input type="hidden" name="discount" value="{{$discount}}">
                <div class="input-group mb-3">
                    <select name="payment_method" class="form-control" onchange="showDiv(this)">
                        <option disabled selected> @lang('messages.choose_payment_method') </option>
                        <option value="bank"> @lang('messages.bank_transfer') </option>
                        <option value="online"> @lang('messages.online_payment') </option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-money-bill"></span>
                        </div>
                    </div>
                    @if ($errors->has('payment_method'))

                        <div class="alert alert-danger">
                            <button class="close" data-close="alert"></button>
                            <span> {{ $errors->first('payment_method') }}</span>
                        </div>
                    @endif
                </div>
                <div id="bank" style="display: none">
                    <div class="input-group mb-3">
                        <?php  $setting = \App\Models\Setting::first(); ?>
                        <span style="color: #ff224f"> Bank  : {{$setting->bank_name}} </span>
                        <span style="color: #ff224f"> Account Number  : {{$setting->account_number}} </span>
                        <span style="color: #ff224f"> IBAN Number : {{$setting->Iban_number}} </span>
                    </div>
                    <div class="input-group mb-3">
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
                    <div class="input-group mb-3">
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

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            @lang('messages.continue')
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>


<script src="{{asset('lte/plugins/jquery/jquery.min.js')}}"></script>

<script src="{{asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('lte/dist/js/adminlte.min.js?v=3.2.0')}}"></script>

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
</body>
</html>
