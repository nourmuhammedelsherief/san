<?php

namespace App\Http\Controllers\Api\ParentController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Father\FatherResource;
use App\Http\Resources\SubjectResource;
use App\Mail\NotifyMail;
use App\Models\Father\Father;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher\ClassRoomSubject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;
class AuthParentController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:fathers,email|max:191',
            'photo' => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password|min:6',
            'device_token' => 'required|string|max:191',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        // create new Father
        $code = mt_rand(1000, 9999);
        $father = Father::create([
            'name' => $request->name,
            'email' => $request->email,
            'photo' => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/fathers'),
            'password' => Hash::make($request->password),
            'verification_code' => $code
        ]);
        // create teacher device token
        $created = ApiController::createFatherDeviceToken($father->id, $request->device_token);
        // send email to father
        $msg = trans('messages.verification_code_at_sanaidi_is') . $code;
        Mail::to($father->email)->send(new NotifyMail($msg));
        return  ApiController::respondWithSuccess(new FatherResource($father));
    }

    public function verify_email(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'code' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $father = Father::where('email', $request->email)
            ->where('verification_code', $request->code)
            ->first();
        if ($father) {
            $father->update([
                'verification_code' => null,
                'email_verified_at' => Carbon::now(),
            ]);
            $success = [
                'message' => trans('messages.code_success')
            ];
            auth()->guard('father')->attempt(['email' => $request->email, 'password' => $father->password]);
            $father->update([
                'api_token' => generateApiToken($father->id, 50),
            ]);
            return ApiController::respondWithSuccess(new FatherResource($father));
        } else {
            $errorsLogin = [
                'message' => trans('messages.error_code')
            ];
            return ApiController::respondWithErrorClient($errorsLogin);
        }
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

        $father = Father::whereEmail($request->email)->first();
        if (auth()->guard('father')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $father->update([
                'api_token' => generateApiToken($father->id, 50),
            ]);
            // create teacher device token
            $created = ApiController::createFatherDeviceToken($father->id, $request->device_token);
            return ApiController::respondWithSuccess(new FatherResource($father));
        } else {
            if ($father == null) {
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
        $father = Father::find($request->user()->id);
        $father->update([
            'api_token' => null
        ]);
        $father->device_token()->delete();
        $success = [
            'message' => trans('messages.logout_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }

    public function forgetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $father = Father::whereEmail($request->email)->first();
        if ($father != null) {
            $code = mt_rand(1000, 9999);
            $msg = trans('messages.verification_code_at_sanaidi_is') . $code;
            Mail::to($father->email)->send(new NotifyMail($msg));

            $father->update([
                'verification_code' => $code,
            ]);
            $success = [
                'message' => trans('messages.code_sent_successfully'),
                'code' => $code
            ];

            return $father
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

        $father = Father::where('email', $request->email)
            ->where('verification_code', $request->code)
            ->first();
        if ($father) {
            $father->update([
                'verification_code' => null
            ]);
            $success = [
                'message' => trans('messages.code_success')
            ];
            return $father
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

        $father = Father::where('email', $request->email)->first();

        if ($father)
            $updated = $father->update(['password' => Hash::make($request->password)]);
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
        $father = $request->user();
        return ApiController::respondWithSuccess(new FatherResource($father));
    }

    public function edit_profile(Request $request)
    {
        $father = $request->user();
        $rules = [
            'email' => 'nullable|email|max:191',
            'name'  => 'nullable|string|max:191',
            'photo' => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,bmp|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $father->update([
            'name'    => $request->name == null ? $father->name : $request->name,
            'email'   => $request->email == null ? $father->email : $request->email,
            'photo'   => $request->file('photo') == null ? $father->photo : UploadImageEdit($request->file('photo') , 'photo' , '/uploads/fathers/' , $father->photo),
        ]);
        return ApiController::respondWithSuccess(new FatherResource($father));
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
    public function my_subjects(Request $request)
    {
        $rules = [
            'student_id' => 'required|exists:students,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $student = Student::find($request->student_id);
        $classroom = $student->classroom;
        $subjects = ClassRoomSubject::with('class_room')
            ->whereHas('class_room', function ($q) use ($classroom) {
                $q->with('classroom');
                $q->whereHas('classroom', function ($c) use ($classroom) {
                    $c->where('id', $classroom->id);
                });
            })->get();
//        $subjects = Subject::all();
        return ApiController::respondWithSuccess(SubjectResource::collection($subjects));
    }
}
