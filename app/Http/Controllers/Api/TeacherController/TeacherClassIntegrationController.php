<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\IntegrationResource;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\Reward;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherClassRoom;
use App\Models\Teacher\TeacherDeviceToken;
use App\Models\Teacher\TeacherIntegration;
use App\Models\Teacher\TeacherRate;
use App\Models\Teacher\TeacherSubject;
use Illuminate\Http\Request;
use Validator;


class TeacherClassIntegrationController extends Controller
{
    public function integrate_with_another_teacher_request(Request $request)
    {
        $rules = [
            'integration_code'      => 'required|exists:teachers,integration_code',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $master = Teacher::whereIntegrationCode($request->integration_code)->first();
        // create new integration request
        TeacherIntegration::updateOrCreate([
            'master_teacher_id' => $master->id,
            'teacher_id'        => $request->user()->id,
        ]
        ,
        [
            'status'            => 'requested'
        ]);

        // send notification to teacher
        $message = [
            'title' => 'ldldldl',
            'body'  => 'dddddddddd',
        ];

        $firebaseToken = TeacherDeviceToken::whereTeacherId($master->id)->pluck('device_token')->all();
        $title = trans('messages.integration');
        $message = trans('messages.integration_request') . $request->user()->name;
        sendNotification($firebaseToken , $title, $message , null);
        saveNotification('teacher' , '1' , $title , $message , $master->id , null , null);
        $success = [
            'message'  => trans('messages.IntegrationRequestedSuccessfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
    public function integration_requests(Request $request)
    {
        $teacher = $request->user();
        $integrations = TeacherIntegration::whereMasterTeacherId($teacher->id)->get();
        return ApiController::respondWithSuccess(IntegrationResource::collection($integrations));
    }
    public function my_integrations(Request $request)
    {
        $teacher = $request->user();
        $integrations = TeacherIntegration::whereTeacherId($teacher->id)->get();
        return ApiController::respondWithSuccess(IntegrationResource::collection($integrations));
    }
    public function teacher_apply_integration_request(Request $request)
    {
        $rules = [
            'integration_request_id'      => 'required|exists:teacher_integrations,id',
            'status'                      => 'required|in:done,cancel',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $integration_request = TeacherIntegration::find($request->integration_request_id);
        $master_teacher = Teacher::find($integration_request->master_teacher_id);
        if ($request->status == 'done')
        {
            $integration_request->update([
                'status'   => 'done',
            ]);
            // add the master teacher class to slave teacher
            $classes = TeacherClassRoom::whereTeacherId($integration_request->master_teacher_id)
                ->whereArchive('false')
                ->get();
            if ($classes->count() > 0)
            {
                foreach ($classes as $class)
                {
                    $pulled_class = TeacherClassRoom::create([
                        'classroom_id'    => $class->classroom_id,
                        'name'            => $class->name,
                        'teacher_id'      => $integration_request->teacher_id,
                        'main_teacher_id' => $class->teacher_id,
                        'pulled'          => 'true',
                        'archive'         => 'false',
                    ]);
                    // add classroom subjects
                    $teacherSubjects = TeacherSubject::whereTeacherId($integration_request->teacher_id)->get();
                    if ($teacherSubjects->count() > 0)
                    {
                        foreach ($teacherSubjects  as $teacherSubject)
                        {
                            ClassRoomSubject::create([
                                'class_room_id' => $pulled_class->id,
                                'subject_id'    => $teacherSubject->subject_id
                            ]);
                        }
                    }
                }
            }
            // add teachers rate and rewards to another teacher
            $rates = TeacherRate::whereTeacherId($integration_request->master_teacher_id)->get();
            if ($rates->count() > 0)
            {
                foreach ($rates  as $rate)
                {
                    TeacherRate::create([
                        'teacher_id'    => $integration_request->teacher_id,
                        'rate_name'     => $rate->rate_name,
                        'points'        => $rate->points,
                        'type'          => $rate->type,
                    ]);
                }
            }
            $rewards = Reward::whereTeacherId($integration_request->master_teacher_id)->get();
            if ($rewards->count() > 0)
            {
                foreach ($rewards  as $reward)
                {
                    Reward::create([
                        'teacher_id'    => $integration_request->teacher_id,
                        'name'          => $reward->name,
                        'points'        => $reward->points,
                        'photo'         => $reward->photo,
                    ]);
                }
            }

            // send notification to teacher with operation done
            $firebaseToken = TeacherDeviceToken::whereTeacherId($integration_request->teacher_id)->pluck('device_token')->all();
            $title = trans('messages.integration');
            $message = trans('messages.integration_request_done') . $master_teacher->name;
            sendNotification($firebaseToken , $title, $message , null);
            saveNotification('teacher' , '1' , $title , $message , $integration_request->teacher_id , null , null);

            $success = [
                'message' => trans('messages.operationDoneSuccessfully')
            ];
            return ApiController::respondWithSuccess($success);
        }elseif ($request->status == 'cancel')
        {
            $integration_request->update([
                'status'   => 'canceled',
            ]);
            // send notification to teacher with operation canceled
            $firebaseToken = TeacherDeviceToken::whereTeacherId($integration_request->teacher_id)->pluck('device_token')->all();
            $title = trans('messages.integration');
            $message = trans('messages.integration_request_canceled') . $master_teacher->name;
            sendNotification($firebaseToken , $title, $message , null);
            saveNotification('teacher' , '1' , $title , $message , $integration_request->teacher_id , null , null);

            $success = [
                'message' => trans('messages.operationCanceledSuccessfully')
            ];
            return ApiController::respondWithSuccess($success);
        }
    }
}
