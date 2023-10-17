<?php

namespace App\Http\Controllers\Api\StudentController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\SubjectResource;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\StudentRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use function auth;
use function generateApiToken;
use function trans;
use function validateRules;

class AuthStudentController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'identity_id' => 'required',
            'password' => 'required',
            'device_token' => 'required|string|max:191',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $student = Student::where('identity_id', $request->identity_id)->first();
        if ($student and auth()->guard('student')->attempt(['identity_id' => $request->identity_id, 'password' => $request->password])) {
            $student->update([
                'api_token' => generateApiToken($student->id, 50),
                'last_login_at' => Carbon::now(),
                'last_login_ip_address' => request()->getClientIp()
            ]);
            // create teacher device token
            $created = ApiController::createStudentDeviceToken($student->id, $request->device_token);
            return ApiController::respondWithSuccess(new StudentResource($student));
        } else {
            if ($student == null) {
                $errors = [
                    'message' => trans('messages.wrong_identity_id'),
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
        $student = Student::find($request->user()->id);
        $student->update([
            'api_token' => null
        ]);
        $student->device_token()->delete();
        $success = [
            'message' => trans('messages.logout_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
    public function my_subjects(Request $request)
    {
        $subjects = StudentRate::whereStudentId($request->user()->id)
            ->groupBy('subject_id')
            ->get();
        return ApiController::respondWithSuccess(SubjectResource::collection($subjects));
    }
}
