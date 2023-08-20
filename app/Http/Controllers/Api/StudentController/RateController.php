<?php

namespace App\Http\Controllers\Api\StudentController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Student\StudentRateResource;
use App\Http\Resources\Student\StudentRewardResource;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\Teacher\StudentResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\StudentRate;
use App\Models\Teacher\StudentReward;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Http\Request;
use Validator;

class RateController extends Controller
{
    public function my_rates(Request $request)
    {
        $rules = [
            'subject_id' => 'sometimes|exists:subjects,id',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        if ($request->subject_id)
        {
            $rates = StudentRate::whereStudentId($request->user()->id)
                ->whereSubjectId($request->subject_id)
                ->get();
        }else{
            $rates = StudentRate::whereStudentId($request->user()->id)->get();
        }
        return ApiController::respondWithSuccess(StudentRateResource::collection($rates));

    }
    public function my_rewards(Request $request)
    {
        $rules = [
            'subject_id' => 'sometimes|exists:subjects,id',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        if ($request->subject_id)
        {
            $rewards = StudentReward::whereStudentId($request->user()->id)
                ->whereSubjectId($request->subject_id)
                ->get();
        }else{
            $rewards = StudentReward::whereStudentId($request->user()->id)->get();
        }
        return ApiController::respondWithSuccess(StudentRewardResource::collection($rewards));

    }
    public function my_arrange(Request $request)
    {
        $student = $request->user();
        $classroom = $student->classroom;
        $subjects = ClassRoomSubject::with('class_room')
            ->whereHas('class_room' , function ($q) use ($classroom){
                $q->with('classroom');
                $q->whereHas('classroom' , function ($c) use ($classroom){
                    $c->where('id' , $classroom->id);
                });
            })->get();
        $arranges = [];
        if ($subjects->count() > 0)
        {
            foreach ($subjects as $subject)
            {
                $points = StudentRate::whereStudentId($student->id)->whereSubjectId($subject->subject_id)->first()->points;
                array_push($arranges , [
                    'arrange' => getStudentArrange($subject->subject_id , $student->id , $points) + 1,
                    'subject' => new SubjectResource($subject->subject),
                    'points'  => $points,
                ]);
            }
        }
        $all = [
            'student' => new StudentResource($student),
            'arranges' => $arranges,
        ];
        return ApiController::respondWithSuccess($all);
    }
    public function my_teachers_list(Request $request)
    {
        $student = $request->user();
        $classroom = $student->classroom;
        $teachers  = TeacherClassRoom::whereClassroomId($classroom->id)->get();
        return ApiController::respondWithSuccess(TeacherResource::collection($teachers));
    }
}
