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
        if ($request->file('photo'))
        {
            $photo = UploadImage($request->file('photo') , 'photo' , '/uploads/notifications');
            $photo = asset('/uploads/notifications/' . $photo);
        }else{
            $photo = null;
        }
        sendNotification($firebaseToken , $request->title, $request->message , $photo);
        saveNotification('student' , '3' , $request->title , $request->message , $request->user()->id , null , $student->id);
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
        if ($request->file('photo'))
        {
            $photo = UploadImage($request->file('photo') , 'photo' , '/uploads/notifications');
            $photo = asset('/uploads/notifications/' . $photo);
        }else{
            $photo = null;
        }
        if ($parents->count()>0)
        {
            foreach ($parents as $parent)
            {
                $firebaseToken = FatherDeviceToken::whereFatherId($parent->father_id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , $photo);
                saveNotification('father' , '2' , $request->title , $request->message , $request->user()->id , $parent->father_id , $student->id);
            }
        }
        $success = [
            'message' => trans('messages.notificationSendSuccessfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
}
