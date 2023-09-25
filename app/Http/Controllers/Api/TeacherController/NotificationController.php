<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Models\Father\FatherChild;
use App\Models\Father\FatherDeviceToken;
use App\Models\Notification;
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
        }else{
            $photo = null;
        }
        sendNotification($firebaseToken , $request->title, $request->message , asset('/uploads/notifications/' . $photo));
        saveNotification('student' , '3' , $request->title , $request->message , $request->user()->id , null , $student->id , $photo);
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
        }else{
            $photo = null;
        }
        if ($parents->count()>0)
        {
            foreach ($parents as $parent)
            {
                $firebaseToken = FatherDeviceToken::whereFatherId($parent->father_id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , asset('/uploads/notifications/' . $photo));
                saveNotification('father' , '2' , $request->title , $request->message , $request->user()->id , $parent->father_id , $student->id , $photo);
            }
        }
        $success = [
            'message' => trans('messages.notificationSendSuccessfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
    public function notification_list(Request $request)
    {
        $teacher = $request->user();
        $notifications = Notification::whereUser('teacher')
            ->whereTeacherId($teacher->id)
            ->get();
        return ApiController::respondWithSuccess(NotificationResource::collection($notifications));
    }
    public function student_notification_list(Request $request)
    {
        $std = $request->user();
        $notifications = Notification::whereUser('student')
            ->whereStudentId($std->id)
            ->get();
        return ApiController::respondWithSuccess(NotificationResource::collection($notifications));
    }
    public function father_notification_list(Request $request)
    {
        $father = $request->user();
        $notifications = Notification::whereUser('father')
            ->whereFatherId($father->id)
            ->get();
        return ApiController::respondWithSuccess(NotificationResource::collection($notifications));
    }
    public function delete_notification($id)
    {
        $notification = Notification::find($id);
        if ($notification)
        {
            $notification->delete();
            $success = [
                'message' => trans('messages.notificationDeletedSuccessfully')
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
}
