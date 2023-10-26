<?php

namespace App\Http\Controllers\Api\StudentController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Student\StudentRateResource;
use App\Http\Resources\Student\StudentRewardResource;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\Student\StudentResource;
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
        $all = [
            'student' => StudentRateResource::collection($rates),
            'total_points' => $request->subject_id ? StudentRate::whereStudentId($request->user()->id)
                ->whereSubjectId($request->subject_id)
                ->sum('points') : StudentRate::whereStudentId($request->user()->id)->sum('points'),
        ];
        return ApiController::respondWithSuccess($all);
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
        $all = [
            'student' => StudentRewardResource::collection($rewards),
            'total_points' => $request->subject_id ? StudentReward::whereStudentId($request->user()->id)
                ->whereSubjectId($request->subject_id)
                ->sum('points') : StudentReward::whereStudentId($request->user()->id)->sum('points'),
        ];
        return ApiController::respondWithSuccess($all);

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
            })
            ->select('subject_id')
            ->groupBy('subject_id')
            ->get();
        $arranges = [];
        if ($subjects->count() > 0)
        {
            foreach ($subjects as $subject)
            {
                $points = StudentRate::whereStudentId($student->id)->whereSubjectId($subject->subject_id)->sum('points');
                array_push($arranges , [
                    'arrange' => intval(getStudentArrange($subject->subject_id , $student->id , $points) + 1),
                    'subject' => new SubjectResource($subject->subject),
                    'points'  => intval($points),
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
