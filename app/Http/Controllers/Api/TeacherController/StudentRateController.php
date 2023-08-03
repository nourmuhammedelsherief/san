<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher\StudentRate;
use App\Models\Teacher\TeacherRate;
use Illuminate\Http\Request;
use Validator;

class StudentRateController extends Controller
{
    public function rate(Request $request)
    {
        $teacher = $request->user();
        $rules = [
            'rate'      => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        if ($request->rate != null)
        {
            foreach (array_values(json_decode($request->rate)) as $item)
            {
                $rate = TeacherRate::find($item->rate_id);
                $student = Student::find($item->student_id);
                StudentRate::updateOrCreate([
                    'rate_id'    => $rate->id,
                    'student_id' => $student->id,
                    'points'     => $rate->points,
                ]);
                $student->update([
                    'points'  => $rate->type == 'positive' ? ($student->points + $rate->points) : ($student->points - $rate->points),
                ]);
            }
        }
        $success = [
            'messages' => trans('messages.rated_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
}
