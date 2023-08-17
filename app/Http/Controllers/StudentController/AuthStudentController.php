<?php

namespace App\Http\Controllers\StudentController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Validator;

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

        $student = Student::where('identity_id' , $request->identity_id)->first();
        if ($student and auth()->guard('student')->attempt(['identity_id' => $request->identity_id, 'password' => $request->password])) {
            $student->update([
                'api_token' => generateApiToken($student->id, 50),
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
}
