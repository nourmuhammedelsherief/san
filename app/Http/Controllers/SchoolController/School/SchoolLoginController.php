<?php

namespace App\Http\Controllers\SchoolController\School;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\School\City;
use App\Models\School\School;
use App\Models\School\SchoolSubscription;
use App\Models\SellerCode;
use App\Models\Setting;
use App\Models\Teacher\TeacherSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class SchoolLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:school')->except('logout');
    }

    public function showLoginForm()
    {
        return view('school.authAdmin.login');
    }

    public function login(Request $request)
    {
        App::setLocale('ar');
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        // Verified - send email
        $credential = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'active'
        ];

        if (Auth::guard('school')->attempt($credential, $request->remember)) {
            return redirect()->route('school.home');
        }
        return redirect()->back()->withInput($request->only(['email', 'remember']))->with('warning_login', trans('messages.warning_login'));
    }

    public function showRegisterForm()
    {
        $cities = City::all();
        return view('school.authAdmin.register', compact('cities'));
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'city_id' => 'required|exists:cities,id',
            'identity_code' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'password' => 'required|min:6|confirmed',
            'seller_code' => 'nullable|exists:seller_codes,code',
        ]);
        // register new school
        $school = School::create([
            'name' => $request->name,
            'identity_code' => $request->identity_code,
            'city_id' => $request->city_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'in_complete',
        ]);
        $seller_code_id = null;
        $discount = 0;
        $amount = Setting::first()->school_subscribe_price;
        if ($request->seller_code != null) {
            // apply the seller code discount
            $seller_code = SellerCode::whereCode($request->seller_code)->first();
            if ($seller_code and $seller_code->start_at <= Carbon::now() and $seller_code->end_at >= Carbon::now()) {
                $discount = ($amount * $seller_code->discount) / 100;
                $amount = $amount - $discount;
                $seller_code_id = $seller_code->id;
            }
        }
        return view('school.authAdmin.register_payment', compact('school', 'amount', 'seller_code_id' , 'discount'));

//        return redirect()->route('school.register_payment' , $school->id);
    }

    public function register_payment($id)
    {
        $school = School::findOrFail($id);
        return view('school.authAdmin.register_payment', compact('school'));
    }

    public function submit_register_payment(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $this->validate($request, [
            'payment_method' => 'required|in:online,bank',
            'transfer_photo' => 'required_if:payment_method,bank|mimes:jpg,jpeg,webp,png,gif,tif,psd,pmp|max:5000',
            'online_type' => 'required_if:payment_method,online',
        ]);
        if ($request->seller_code_id != null):
            $seller_code = SellerCode::find($request->seller_code_id);
        else:
            $seller_code = null;
        endif;
        if ($request->payment_method == 'bank') {
            // create teacher subscription with bank
            SchoolSubscription::create([
                'school_id' => $school->id,
                'paid_amount' => $request->amount,
                'seller_code_id' => $seller_code?->id,
                'discount' => $request->discount,
                'status' => 'not_active',
                'transfer_photo' => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo'), 'transfer', '/uploads/school_transfers'),
                'payment_type' => 'bank',
                'payment' => 'false',
            ]);
            $school->update(['status' => 'not_active']);
            flash(trans('messages.register_and_wait_active'))->success();
            return redirect()->route('school.login');
        }
        elseif ($request->payment_method == 'online') {
            // online payment by my fatoourah
            $amount = $request->amount;
            $amount = number_format((float)$amount, 2, '.', '');
            if ($request->online_type == 'visa') {
                $charge = 2;
            } elseif ($request->online_type == 'mada') {
                $charge = 6;
            } elseif ($request->online_type == 'apple_pay') {
                $charge = 11;
            } else {
                $charge = 2;
            }
            $name = $school->name;
            $token = Setting::first()->online_token;
            $data = array(
                'PaymentMethodId' => $charge,
                'CustomerName' => $name,
                'DisplayCurrencyIso' => 'SAR',
                'MobileCountryCode' => '00966',
                'CustomerMobile' => $school->phone_number,
                'CustomerEmail' => $school->email,
                'InvoiceValue' => $amount,
                'CallBackUrl' => url('/check-school-status'),
                'ErrorUrl' => url('/error'),
                'Language' => app()->getLocale(),
                'CustomerReference' => 'ref 1',
                'CustomerCivilId' => '12345678',
                'UserDefinedField' => 'Custom field',
                'ExpireDate' => '',
                'CustomerAddress' => array(
                    'Block' => '',
                    'Street' => '',
                    'HouseBuildingNo' => '',
                    'Address' => '',
                    'AddressInstructions' => '',
                ),
                'InvoiceItems' => [array(
                    'ItemName' => $name,
                    'Quantity' => '1',
                    'UnitPrice' => $amount,
                )],
            );
            $data = json_encode($data);
            $fatooraRes = MyFatoorah($token, $data);
            $result = json_decode($fatooraRes);
            if ($result != null and $result->IsSuccess === true) {
                // create teacher subscription with online
                SchoolSubscription::create([
                    'school_id' => $school->id,
                    'paid_amount' => $amount,
                    'seller_code_id' => $seller_code?->id,
                    'discount' => $request->discount,
                    'status' => 'not_active',
                    'invoice_id' => $result->Data->InvoiceId,
                    'payment_type' => 'online',
                    'payment' => 'false',
                ]);
                return redirect()->to($result->Data->PaymentURL);
            } else {
                flash(trans('messages.errorPayment'))->error();
                return redirect()->back();
            }
        }
    }

    public function check_status(Request $request)
    {
        $token = Setting::first()->online_token;
        $PaymentId = $request->query('paymentId');
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if ($result->IsSuccess === true and $result->Data->InvoiceStatus === "Paid") {
            $InvoiceId = $result->Data->InvoiceId;
            $subscription = SchoolSubscription::where('invoice_id', $InvoiceId)->first();
            $subscription->update([
                'status' => 'active',
                'payment' => 'true',
                'paid_at' => Carbon::now(),
                'end_at' => Carbon::now()->addYear(),
            ]);
            $subscription->school->update([
                'status' => 'active',
            ]);
            // add operation to History
            History::create([
                'school_id' => $subscription->school->id,
                'amount' => $subscription->paid_amount,
                'discount' => $subscription->discount,
                'type' => 'teacher',
                'invoice_id' => $InvoiceId,
                'payment_type' => 'online'
            ]);
            flash(trans('messages.payment_done_successfully'))->success();
            Auth::guard('school')->login($subscription->school);
            return redirect()->route('school.home');
        } else {
            flash(trans('messages.errorPayment'))->error();
            return redirect()->back();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('school')->logout();
        return redirect('/school/login');
    }
}
