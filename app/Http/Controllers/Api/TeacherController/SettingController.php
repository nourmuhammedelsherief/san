<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Models\SellerCode;
use App\Models\Setting;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\FlareClient\Api;
use Validator;

class SettingController extends Controller
{
    public function bank_info()
    {
        $setting = Setting::first();
        $bank_data = [
            'bank_name'  => $setting->bank_name,
            'account_number'  => $setting->account_number,
            'Iban_number'  => $setting->Iban_number,
        ];
        return ApiController::respondWithSuccess($bank_data);
    }
    public function teacher_annual_subscription_value()
    {
        $setting = Setting::first();
        $bank_data = [
            'teacher_subscribe_price' => $setting->teacher_subscribe_price,
        ];
        return ApiController::respondWithSuccess($bank_data);
    }
    public function pay_annual_subscription(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'payment_type' => 'required|in:online,bank',
            'transfer_photo' => 'required_if:payment_type,bank|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'seller_code' => 'sometimes|exists:seller_codes,code',
            'online_type' => 'required_if:payment_type,online|in:visa,mada,apple_pay'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $teacher = Teacher::whereEmail($request->email)->first();
        if ($teacher)
        {
            $setting = Setting::first();
            $seller_code = null;
            $discount = 0;
            $amount = $setting->teacher_subscribe_price;
            if ($request->seller_code != null) {
                // apply the seller code discount
                $seller_code = SellerCode::whereCode($request->seller_code)->first();
                if ($seller_code and $seller_code->start_at <= Carbon::now() and $seller_code->end_at >= Carbon::now()) {
                    $discount = ($amount * $seller_code->discount) / 100;
                    $amount = $amount - $discount;
                }
            }
            if ($request->payment_type == 'bank') {
                $subscription = $teacher->subscription;
                TeacherSubscription::updateOrCreate([
                    'teacher_id' => $teacher->id
                ] , [
                    'paid_amount' => $amount,
                    'seller_code_id' => $seller_code?->id,
                    'discount' => $discount,
                    'status' => 'not_active',
                    'transfer_photo' => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo'), 'transfer', '/uploads/teacher_transfers'),
                    'payment_type' => 'bank',
                    'payment' => 'false',
                ]);
                $success = [
                    'message' => trans('messages.register_and_wait_active')
                ];
                return ApiController::respondWithSuccess($success);
            }
            elseif ($request->payment_type == 'online') {
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
                $name = $teacher->name;
                $token = Setting::first()->online_token;
                $data = array(
                    'PaymentMethodId' => $charge,
                    'CustomerName' => $name,
                    'DisplayCurrencyIso' => 'SAR',
                    'MobileCountryCode' => '00966',
                    'CustomerMobile' => $teacher->phone_number,
                    'CustomerEmail' => $teacher->email,
                    'InvoiceValue' => $amount,
                    'CallBackUrl' => url('/api/check-teacher-status'),
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
//                dd($data);
                $fatooraRes = MyFatoorah($token, $data);
                $result = json_decode($fatooraRes);
                if ($result != null and $result->IsSuccess === true) {
                    // create teacher subscription with online
                    TeacherSubscription::updateOrCreate(
                        ['teacher_id' => $teacher->id]
                        ,[
                        'paid_amount' => $amount,
                        'seller_code_id' => $seller_code?->id,
                        'discount' => $discount,
                        'status' => 'not_active',
                        'invoice_id' => $result->Data->InvoiceId,
                        'payment_type' => 'online',
                        'payment' => 'false',
                    ]);
                    $success = [
                        'payment_url' => $result->Data->PaymentURL
                    ];
                    return ApiController::respondWithSuccess($success);
                } else {
                    $error = [
                        'message' => trans('messages.errorPayment')
                    ];
                    return ApiController::respondWithErrorObject($error);
                }
            }
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
}
