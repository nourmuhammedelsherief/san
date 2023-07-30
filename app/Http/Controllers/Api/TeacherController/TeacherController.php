<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\SellerCode;
use App\Models\Setting;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherSubject;
use App\Models\Teacher\TeacherSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class TeacherController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'city_id'   => 'required|exists:cities,id',
            'name'      => 'required|string|max:191',
            'email'     => 'required|email|unique:teachers,email|max:191',
            'photo'     => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'school'    => 'required|string|max:191',
            'password'  => 'required|min:6',
            'password_confirmation' => 'required|same:password|min:6',
            'subjects'  => 'required',
            'subjects*' => 'exists|subjects,id',
            'payment_type' => 'required|in:online,bank',
            'transfer_photo' => 'required_if:payment_type,bank|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'seller_code' => 'sometimes|exists:seller_codes,code'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

//         create new Teacher
        $teacher = Teacher::create([
            'city_id'  => $request->city_id,
            'name'     => $request->name,
            'email'    => $request->email,
            'photo'    => $request->file('photo') == null ? null : UploadImage($request->file('photo') , 'photo' , '/uploads/teachers'),
            'school'   => $request->school,
            'type'     => 'free',
            'active'   => 'false',
            'password' => Hash::make($request->password),
        ]);
        // add teacher subjects
        if ($request->subjects != null)
        {
            foreach ($request->subjects as $subject)
            {
                // create teacher subject
                TeacherSubject::create([
                    'teacher_id'  => $teacher->id,
                    'subject_id'  => $subject,
                ]);
            }
        }
        $setting = Setting::first();
        $seller_code = null;
        $discount = 0;
        $amount = $setting->teacher_subscribe_price;
        if ($request->seller_code != null)
        {
            // apply the seller code discount
            $seller_code = SellerCode::whereCode($request->seller_code)->first();
            if ($seller_code and $seller_code->start_at <= Carbon::now() and $seller_code->end_at >= Carbon::now())
            {
                $discount = ($amount * $seller_code->discount) / 100;
                $amount = $amount - $discount;
            }
        }
        if ($request->payment_type == 'bank')
        {
            // create teacher subscription with bank
            TeacherSubscription::create([
                'teacher_id'      => $teacher->id,
                'paid_amount'     => $amount,
                'seller_code_id'  => $seller_code?->id,
                'discount'        => $discount,
                'status'          => 'not_active',
                'transfer_photo'  => $request->file('transfer_photo') == null ? null : UploadImage($request->file('transfer_photo') , 'transfer' , '/uploads/teacher_transfers'),
                'payment_type'    => 'bank',
                'payment'         => 'false',
            ]);
            $success = [
                'message' => trans('messages.register_and_wait_active')
            ];
            return ApiController::respondWithSuccess($success);
        }
//        return  ApiController::respondWithSuccess(new TeacherResource($teacher));
    }
    public function verify_phone(Request $request)
    {
        $rules = [
            'phone_number'    => 'required|min:8',
            'code'            => 'required|min:4',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $hotel = Hotel::wherePhoneNumber($request->phone_number)
            ->orderBy('id' , 'desc')
            ->first();
        if ($hotel)
        {
            if ($hotel->phone_verification == $request->code)
            {
                $hotel->update([
                    'phone_verified_at' => Carbon::now(),
                    'status' => 'tentative',
                    'api_token' => generateApiToken($hotel->id, 50),
                    'phone_verification' => null
                ]);
                $success = [
                    'message' => trans('messages.phone_verified_successfully'),
                ];
                return ApiController::respondWithSuccess(new HotelResource($hotel));
            }else{
                $error = [
                    'message' => trans('messages.error_code')
                ];
                return ApiController::respondWithErrorObject($error);
            }
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $hotel = Hotel::whereEmail($request->email)->first();
        if (auth()->guard('hotel')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $hotel->update([
                'api_token' => generateApiToken($hotel->id, 50),
            ]);
            return ApiController::respondWithSuccess(new HotelResource($hotel));
        } else {
            if ($hotel == null) {
                $errors = [
                    'message' => trans('messages.wrong_email'),
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
        $hotel = Hotel::find($request->user()->id);
        $hotel->update([
            'api_token' => null
        ]);
        $success = [
            'message' => trans('messages.logout_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
}
