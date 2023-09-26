<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Father\Father;
use App\Models\Father\FatherDeviceToken;
use App\Models\Student;
use App\Models\StudentDeviceToken;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherDeviceToken;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function public_notification()
    {
        return view('admin.notifications.public_notification');
    }
    public function store_public_notification(Request $request)
    {
        $this->validate($request , [
            'type'   => 'required|in:teachers,fathers,students,all',
            'title'  => 'required|string|max:191',
            'message' => 'required|string'
        ]);

        if ($request->type == 'teachers')
        {
            $teachers = Teacher::all();
            foreach ($teachers  as $teacher)
            {
                $firebaseToken = TeacherDeviceToken::whereTeacherId($teacher->id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , null);
                saveNotification('teacher' , '1' , $request->title , $request->message , $teacher->id , null , null , null);
            }
        }elseif ($request->type == 'fathers')
        {
            $fathers = Father::all();
            foreach ($fathers as $father)
            {
                $firebaseToken = FatherDeviceToken::whereFatherId($father->id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , null);
                saveNotification('father' , '2' , $request->title , $request->message , null, $father->id , null , null);
            }
        }elseif ($request->type == 'students')
        {
            $students = Student::all();
            foreach ($students as $student)
            {
                $firebaseToken = StudentDeviceToken::whereStudentId($student->id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , null);
                saveNotification('student' , '3' , $request->title , $request->message , null, null , $student->id , null);
            }
        }elseif ($request->type == 'all')
        {
            $teachers = Teacher::all();
            foreach ($teachers  as $teacher)
            {
                $firebaseToken = TeacherDeviceToken::whereTeacherId($teacher->id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , null);
                saveNotification('teacher' , '1' , $request->title , $request->message , $teacher->id , null , null , null);
            }
            $fathers = Father::all();
            foreach ($fathers as $father)
            {
                $firebaseToken = FatherDeviceToken::whereFatherId($father->id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , null);
                saveNotification('father' , '2' , $request->title , $request->message , null, $father->id , null , null);
            }
            $students = Student::all();
            foreach ($students as $student)
            {
                $firebaseToken = StudentDeviceToken::whereStudentId($student->id)->pluck('device_token')->all();
                sendNotification($firebaseToken , $request->title, $request->message , null);
                saveNotification('student' , '3' , $request->title , $request->message , null, null , $student->id , null);
            }
        }
        flash(trans('messages.notificationSendSuccessfully'))->success();
        return redirect()->back();
    }
    public function teacher_notifications()
    {
        $teachers = Teacher::all();
        return view('admin.notifications.teachers_notifications' , compact('teachers'));
    }
    public function store_teacher_notifications(Request $request)
    {
        $this->validate($request , [
            'teachers*' => 'required',
            'title'  => 'required|string|max:191',
            'message' => 'required|string'
        ]);
        foreach ($request->teachers  as $teacher)
        {
            $firebaseToken = TeacherDeviceToken::whereTeacherId($teacher)->pluck('device_token')->all();
            sendNotification($firebaseToken , $request->title, $request->message , null);
            saveNotification('teacher' , '1' , $request->title , $request->message , $teacher , null , null , null);
        }
        flash(trans('messages.notificationSendSuccessfully'))->success();
        return redirect()->back();
    }

    public function parent_notifications()
    {
        $fathers = Father::all();
        return view('admin.notifications.fathers_notifications' , compact('fathers'));
    }
    public function store_parent_notifications(Request $request)
    {
        $this->validate($request , [
            'fathers*' => 'required',
            'title'  => 'required|string|max:191',
            'message' => 'required|string'
        ]);
        foreach ($request->fathers  as $father)
        {
            $firebaseToken = FatherDeviceToken::whereFatherId($father)->pluck('device_token')->all();
            sendNotification($firebaseToken , $request->title, $request->message , null);
            saveNotification('father' , '2' , $request->title , $request->message , null, $father , null , null);
        }
        flash(trans('messages.notificationSendSuccessfully'))->success();
        return redirect()->back();
    }

    public function student_notifications()
    {
        $students = Student::all();
        return view('admin.notifications.students_notifications' , compact('students'));
    }
    public function store_student_notifications(Request $request)
    {
        $this->validate($request , [
            'students*' => 'required',
            'title'  => 'required|string|max:191',
            'message' => 'required|string'
        ]);
        foreach ($request->students  as $student)
        {
            $firebaseToken = StudentDeviceToken::whereStudentId($student)->pluck('device_token')->all();
            sendNotification($firebaseToken , $request->title, $request->message , null);
            saveNotification('student' , '3' , $request->title , $request->message , null, null , $student , null);
        }
        flash(trans('messages.notificationSendSuccessfully'))->success();
        return redirect()->back();
    }
}
