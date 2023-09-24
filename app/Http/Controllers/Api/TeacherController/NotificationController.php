<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Father\FatherChild;
use App\Models\Father\FatherDeviceToken;
use App\Models\Student;
use App\Models\StudentDeviceToken;
use Illuminate\Http\Request;
use Validator;

class NotificationController extends Controller
{
    public function send_notification_to_student(Request $request)
    {
        $rules = [
            'student_id' => 'required|exists:students,id',
            'title'    => 'required|string|max:191',
            'message'  => 'required|string',
            'photo'   => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        // sent notification to student
        $student = Student::find($request->student_id);
        $firebaseToken = StudentDeviceToken::whereStudentId($student->id)->pluck('device_token')->all();
        sendNotification($firebaseToken , $request->title, $request->message);
        $success = [
            'message' => trans('messages.notificationSendSuccessfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
    public function send_notification_to_parent(Request $request)
    {
        $rules = [
            'student_id' => 'required|exists:students,id',
            'title'    => 'required|string|max:191',
            'message'  => 'required|string',
            'photo'   => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        // sent notification to student
        $student = Student::find($request->student_id);
        $parents = FatherChild::whereStudentId($student->id)->get();
        if ($parents->count()>0)
        {
            foreach ($parents as $parent)
            {
                $firebaseToken = FatherDeviceToken::whereFatherId($parents->id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message);
            }
        }
        $success = [
            'message' => trans('messages.notificationSendSuccessfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
}
