<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\School\School;
use App\Models\School\SchoolSubscription;
use App\Models\SellerCode;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function my_subscription()
    {
        $school = Auth::guard('school')->user();
        return view('school.subscription.index' , compact('school'));
    }
    public function print_subscription_pdf()
    {
        $school = Auth::guard('school')->user();
        $pdf = PDF::loadView('school.subscription.index' , compact('school'));
        // download PDF file with download method
        return $pdf->download('invoice.pdf');
    }

    public function pay_subscription($id)
    {
        $school = School::findOrFail($id);
        return view('school.subscription.payment' , compact('school'));
    }
    public function submit_subscription(Request $request , $id)
    {
        $school = School::findOrFail($id);
        $school = School::findOrFail($id);
        $this->validate($request, [
            'payment_method' => 'required|in:online,bank',
            'transfer_photo' => 'required_if:payment_method,bank|mimes:jpg,jpeg,webp,png,gif,tif,psd,pmp|max:5000',
            'online_type' => 'required_if:payment_method,online',
            'seller_code' => 'nullable|exists:seller_codes,code',
        ]);

        $seller_code = null;
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
        if ($request->payment_method == 'bank') {
            // create school subscription with bank
            SchoolSubscription::updateOrCreate(
                ['school_id' => $school->id],[
                'paid_amount' => $amount,
                'seller_code_id' => $seller_code?->id,
                'discount' => $discount,
                'status' => 'not_active',
                'transfer_photo' => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo'), 'transfer', '/uploads/school_transfers'),
                'payment_type' => 'bank',
                'payment' => 'false',
            ]);
            $school->update(['status' => 'not_active']);
            flash(trans('messages.register_and_wait_active'))->success();
            return redirect()->to(url('school/my_subscription'));
        }
        elseif ($request->payment_method == 'online') {
            // online payment by my fatoourah
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
                'CallBackUrl' => url('/check-school-subscription-status'),
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
                SchoolSubscription::updateOrCreate(
                    ['school_id' => $school->id],[
                    'paid_amount' => $amount,
                    'seller_code_id' => $seller_code?->id,
                    'discount' => $discount,
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
            return redirect()->to(url('school/my_subscription'));
        } else {
            flash(trans('messages.errorPayment'))->error();
            return redirect()->back();
        }
    }

}
