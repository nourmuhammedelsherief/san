<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\SubscriptionResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Mail\NotifyMail;
use App\Models\History;
use App\Models\SellerCode;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherSubject;
use App\Models\Teacher\TeacherSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;


class TeacherController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:teachers,email|max:191',
            'photo' => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'school' => 'required|string|max:191',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password|min:6',
            'subjects' => 'required',
            'subjects*' => 'exists|subjects,id',
            'payment_type' => 'required|in:online,bank',
            'transfer_photo' => 'required_if:payment_type,bank|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'seller_code' => 'sometimes|exists:seller_codes,code',
            'online_type' => 'required_if:payment_type,online|in:visa,mada,apple_pay',
            'invitation_code' => 'sometimes|exists:teachers,invitation_code',
            'device_token' => 'required|string|max:191',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        // Available alpha caracters
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $integration_code = $characters[rand(0, strlen($characters) - 1)]. $characters[rand(0, strlen($characters) - 1)] . mt_rand(100, 999);
        $invitation_code = $characters[rand(0, strlen($characters) - 1)] . mt_rand(1000, 9999);
//        $string = str_shuffle($integration_code);
        // create new Teacher
        $teacher = Teacher::create([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'email' => $request->email,
            'photo' => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/teachers'),
            'school' => $request->school,
            'type' => 'free',
            'active' => 'false',
            'integration_code' => $integration_code,
            'invitation_code' => $invitation_code,
            'password' => Hash::make($request->password),
        ]);
        // add teacher subjects
        if ($request->subjects != null) {
            foreach ($request->subjects as $subject) {
                // create teacher subject
                TeacherSubject::create([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject,
                ]);
            }
        }

        // create teacher device token
        $created = ApiController::createTeacherDeviceToken($teacher->id, $request->device_token);

        // first apply and check seller code
        $setting = Setting::first();
        $seller_code = null;
        $invitation_code_owner = null;
        $discount = 0;
        $invitation_discount = 0;
        $amount = $setting->teacher_subscribe_price;
        if ($request->seller_code != null) {
            // apply the seller code discount
            $seller_code = SellerCode::whereCode($request->seller_code)->first();
            if ($seller_code and $seller_code->start_at <= Carbon::now() and $seller_code->end_at >= Carbon::now()) {
                $discount = ($amount * $seller_code->discount) / 100;
                $amount = $amount - $discount;
            }
        }
        // second check and apply invitation code

        if ($request->invitation_code != null) {
            $invitation_discount = ($amount * $setting->invitation_code_discount) / 100;
            $amount = $amount - $invitation_discount;
            $discount += $invitation_discount;
            $invitation_code_owner = Teacher::where('invitation_code', $request->invitation_code)->first();
        }

        if ($request->payment_type == 'bank') {
            // create teacher subscription with bank
            TeacherSubscription::create([
                'teacher_id' => $teacher->id,
                'paid_amount' => $amount,
                'seller_code_id' => $seller_code?->id,
                'discount' => $discount,
                'status' => 'not_active',
                'transfer_photo' => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo'), 'transfer', '/uploads/teacher_transfers'),
                'payment_type' => 'bank',
                'payment' => 'false',
                'invitation_code_id' => $invitation_code_owner?->id,
                'invitation_discount' => $invitation_discount,
            ]);
            $success = [
                'message' => trans('messages.register_and_wait_active')
            ];
            return ApiController::respondWithSuccess($success);
        } elseif ($request->payment_type == 'online') {
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
            $fatooraRes = MyFatoorah($token, $data);
            $result = json_decode($fatooraRes);
            if ($result != null and $result->IsSuccess === true) {
                // create teacher subscription with online
                TeacherSubscription::create([
                    'teacher_id' => $teacher->id,
                    'paid_amount' => $amount,
                    'seller_code_id' => $seller_code?->id,
                    'discount' => $discount,
                    'status' => 'not_active',
                    'invoice_id' => $result->Data->InvoiceId,
                    'payment_type' => 'online',
                    'payment' => 'false',
                    'invitation_code_id' => $invitation_code_owner?->id,
                    'invitation_discount' => $invitation_discount,
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
//        return  ApiController::respondWithSuccess(new TeacherResource($teacher));
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'device_token' => 'required|string|max:191',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $teacher = Teacher::whereEmail($request->email)->first();
        if ($teacher and auth()->guard('teacher')->attempt(['email' => $request->email, 'password' => $request->password, 'active' => 'true'])) {
            $teacher->update([
                'api_token' => generateApiToken($teacher->id, 50),
            ]);
            // create teacher device token
            $created = ApiController::createTeacherDeviceToken($teacher->id, $request->device_token);
            return ApiController::respondWithSuccess(new TeacherResource($teacher));
        } else {
            if ($teacher == null) {
                $errors = [
                    'message' => trans('messages.wrong_email'),
                ];
                return ApiController::respondWithErrorObject(array($errors));
            } elseif ($teacher and $teacher->active == 'false') {
                $errors = [
                    'message' => trans('messages.waitManagementActivation'),
                ];
                return ApiController::respondWithErrorObject(array($errors));
            } else {
                $errors = [
                    'message' => trans('messages.wrong_password'),
                ];
                return ApiController::respondWithErrorObject(array($errors));
            }
        }

    }

    public function logout(Request $request)
    {
        $teacher = Teacher::find($request->user()->id);
        $teacher->update([
            'api_token' => null
        ]);
        $teacher->device_token()->delete();
        $success = [
            'message' => trans('messages.logout_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }

    public function check_status(Request $request)
    {
        $token = Setting::first()->online_token;
        $PaymentId = $request->query('paymentId');
        $resData = MyFatoorahStatus($token, $PaymentId);
        $result = json_decode($resData);
        if ($result->IsSuccess === true and $result->Data->InvoiceStatus === "Paid") {
            $InvoiceId = $result->Data->InvoiceId;
            $subscription = TeacherSubscription::where('invoice_id', $InvoiceId)->first();
            $subscription->update([
                'status' => 'active',
                'payment' => 'true',
                'paid_at' => Carbon::now(),
                'end_at' => Carbon::now()->addYear(),
            ]);
            $subscription->teacher->update([
                'active' => 'true',
            ]);
            if ($subscription->invitation_code_id != null) {
                $setting = Setting::first();
                $amount = $setting->teacher_subscribe_price;
                $commission = ($amount * $setting->invitation_code_commission) / 100;
                $subscription->invitation_code->update([
                    'balance' => $subscription->invitation_code->balance + $commission
                ]);
            }
            // add operation to History
            History::create([
                'teacher_id' => $subscription->teacher->id,
                'amount' => $subscription->paid_amount,
                'discount' => $subscription->discount,
                'type' => 'teacher',
                'invoice_id' => $InvoiceId,
                'payment_type' => 'online',
                'seller_code'   => $subscription->seller_code?->code,
                'invitation_code' => $subscription->invitation_code?->invitation_code,
            ]);
            $success = [
                'code' => 200,
                'message' => trans('messages.payment_done_successfully'),
            ];
            return ApiController::respondWithSuccess($success);
        } else {
            $error = [
                'code' => 422,
                'message' => trans('messages.errorPayment')
            ];
            return ApiController::respondWithErrorObject($error);
        }
    }

    public function forgetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $teacher = Teacher::whereEmail($request->email)->first();
        if ($teacher != null) {
            $code = mt_rand(1000, 9999);
            $msg = trans('messages.verification_code_at_sanaidi_is') . $code;
            Mail::to($teacher->email)->send(new NotifyMail($msg));
//            Mail::raw('Hello World!', function($msg) {$msg->to('nourmuhammed20121994@gmail.com')->subject('Test Email'); });
            $teacher->update([
                'verification_code' => $code,
            ]);
            $success = [
                'message' => trans('messages.code_sent_successfully'),
                'code' => $code
            ];

            return $teacher
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        } else {
            $errorsLogin = [
                'message' => trans('messages.wrong_email')
            ];
            return ApiController::respondWithErrorObject($errorsLogin);
        }
    }

    public function confirmResetCode(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'code' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $teacher = Teacher::where('email', $request->email)
            ->where('verification_code', $request->code)
            ->first();
        if ($teacher) {
            $teacher->update([
                'verification_code' => null
            ]);
            $success = [
                'message' => trans('messages.code_success')
            ];
            return $teacher
                ? ApiController::respondWithSuccess($success)
                : ApiController::respondWithServerErrorObject();
        } else {
            $errorsLogin = [
                'message' => trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient($errorsLogin);
        }


    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $teacher = Teacher::where('email', $request->email)->first();

        if ($teacher)
            $updated = $teacher->update(['password' => Hash::make($request->password)]);
        else {
            $errorsLogin = [
                'message' => trans('messages.wrong_email')
            ];
            return ApiController::respondWithErrorClient(array($errorsLogin));
        }
        $success = [
            'message' => trans('messages.Password_reset_successfully')
        ];
        return $updated
            ? ApiController::respondWithSuccess($success)
            : ApiController::respondWithServerErrorObject();
    }

    public function profile(Request $request)
    {
        $teacher = $request->user();
        return ApiController::respondWithSuccess(new TeacherResource($teacher));
    }
    public function get_teacher_by_integration_code($code)
    {
        $teacher = Teacher::where('integration_code' , $code)->first();
        if ($teacher)
        {
            return ApiController::respondWithSuccess(new TeacherResource($teacher));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required',
            'password_confirmation' => 'required|same:new_password',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $error_old_password = [
            'message' => trans('messages.error_old_password')
        ];
        if (!(Hash::check($request->current_password, $request->user()->password)))
            return ApiController::respondWithErrorNOTFoundObject($error_old_password);

        $updated = $request->user()->update(['password' => Hash::make($request->new_password)]);

        $success_password = [
            'message' => trans('messages.password_changed_successfully')
        ];

        return $updated
            ? ApiController::respondWithSuccess($success_password)
            : ApiController::respondWithServerErrorObject();
    }

    public function edit_account(Request $request)
    {
        $rules = [
            'email' => 'nullable|email|unique:teachers,email,' . $request->user()->id,
            'name' => 'nullable|string|max:191',
            'photo' => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $teacher = $request->user();
        $teacher->update([
            'name' => $request->name == null ? $teacher->name : $request->name,
            'email' => $request->email == null ? $teacher->email : $request->email,
            'photo' => $request->file('photo') == null ? $teacher->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/teachers', $teacher->photo),
        ]);
        return ApiController::respondWithSuccess(new TeacherResource($teacher));
    }

    public function edit_whats_info(Request $request)
    {
        $rules = [
            'whatsapp' => 'sometimes|in:true,false',
            'phone_number' => 'sometimes'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $teacher = $request->user();
        $teacher->update([
            'whatsapp' => $request->whatsapp == null ? $teacher->whatsapp : $request->whatsapp,
            'phone_number' => $request->phone_number == null ? $teacher->phone_number : $request->phone_number,
        ]);
        return ApiController::respondWithSuccess(new TeacherResource($teacher));
    }

    public function my_subscription(Request $request)
    {
        $teacher = $request->user();
        $subscription = $teacher->subscription;
        if ($subscription) {
            return ApiController::respondWithSuccess(new SubscriptionResource($subscription));
        } else {
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
}
