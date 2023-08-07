<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\RewardResource;
use App\Http\Resources\Teacher\StudentResource;
use App\Models\Student;
use App\Models\Teacher\Reward;
use App\Models\Teacher\StudentReward;
use Illuminate\Http\Request;
use Validator;

class StdRewardController extends Controller
{
    public function rewards_to_student(Request $request , $id)
    {
        $std = Student::find($id);
        if ($std)
        {
            $rewards = Reward::whereTeacherId($request->user()->id)
                ->where('points' , '<' , $std->points)
                ->get();
            return  ApiController::respondWithSuccess(RewardResource::collection($rewards));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function add_reward_to_student(Request $request)
    {
        $rules = [
            'student_id' => 'required|exists:students,id',
            'reward_id'  => 'required|exists:rewards,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $std = Student::find($request->student_id);
        $reward = Reward::find($request->reward_id);
        if ($std->points > $reward->points)
        {
            // add reward to student and remove its points from std points
            StudentReward::create([
                'student_id' => $std->id,
                'reward_id'  => $reward->id,
                'points'     => $reward->points
            ]);
            $std->update([
                'points' => $std->points - $reward->points
            ]);
            $success = [
                'message'  => trans('messages.rewardAddedToStdSuccessfully')
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $error = [
                'message'  => trans('messages.stdNotHaveEnoughPoints')
            ];
            return ApiController::respondWithErrorObject($error);
        }
    }

    public function get_students_to_reward(Request $request , $id)
    {
        $reward = Reward::find($id);
        if ($reward)
        {
            $students = Student::with('classroom')
                ->whereHas('classroom' , function ($q) use ($request){
                    $q->whereTeacherId($request->user()->id);
                })
                ->where('points' , '>' , $reward->points)
                ->get();
            return ApiController::respondWithSuccess(StudentResource::collection($students));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function add_reward_to_students(Request $request)
    {
        $rules = [
            'students' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        if ($request->students != null)
        {
            foreach (array_values(json_decode($request->students)) as $item)
            {
                $reward = Reward::find($item->reward_id);
                $student = Student::find($item->student_id);
                StudentReward::updateOrCreate([
                    'student_id' => $student->id,
                    'reward_id'  => $reward->id,
                    'points'     => $reward->points
                ]);
                $student->update([
                    'points' => $student->points - $reward->points
                ]);
            }
        }
        $success = [
            'messages' => trans('messages.rewarded_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }

}
