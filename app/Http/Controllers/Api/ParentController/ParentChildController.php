<?php

namespace App\Http\Controllers\Api\ParentController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\Teacher\StudentResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\Father\FatherChild;
use App\Models\Student;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\StudentRate;
use App\Models\Teacher\StudentReward;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Http\Request;
use Validator;

class ParentChildController extends Controller
{
    public function add_child(Request $request)
    {
        $rules = [
            'identity_id' => 'required|exists:students,identity_id',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $student = Student::whereIdentityId($request->identity_id)->first();
        if ($student):
            return ApiController::respondWithSuccess(new StudentResource($student));
        else:
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        endif;
    }

    public function confirm_add_child(Request $request)
    {
        $rules = [
            'identity_id' => 'required|exists:students,identity_id',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $student = Student::whereIdentityId($request->identity_id)->first();
        if ($student):
            // add student to parent
            FatherChild::updateOrCreate([
                'father_id' => $request->user()->id,
                'student_id' => $student->id
            ]);
            $success = [
                'message' => trans('messages.childAddedSuccessfully')
            ];
            return ApiController::respondWithSuccess($success);
        else:
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        endif;
    }

    public function my_children(Request $request)
    {
        $father = $request->user();
        $children = FatherChild::whereFatherId($father->id)->get();
        return ApiController::respondWithSuccess(StudentResource::collection($children));
    }

    public function get_child(Request $request ,  $id)
    {
        $rules = [
            'subject_id' => 'required|exists:subjects,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $student = Student::find($id);
        if ($student) {
            return ApiController::respondWithSuccess(new StudentResource($student));
        } else {
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function my_child_arrange(Request $request, $id)
    {
        $student = Student::find($id);
        if ($student) {
            $classroom = $student->classroom;
            $subjects = ClassRoomSubject::with('class_room')
                ->whereHas('class_room', function ($q) use ($classroom) {
                    $q->with('classroom');
                    $q->whereHas('classroom', function ($c) use ($classroom) {
                        $c->where('id', $classroom->id);
                    });
                })->get();
            $arranges = [];
            if ($subjects->count() > 0) {
                foreach ($subjects as $subject) {
                    $points = StudentRate::whereStudentId($student->id)->whereSubjectId($subject->subject_id)->sum('points');
                    if ($points) {
                        array_push($arranges, [
                            'arrange' => getStudentArrange($subject->subject_id, $student->id, $points) + 1,
                            'subject' => new SubjectResource($subject->subject),
                            'points' => intval($points - StudentReward::whereStudentId($student->id)->whereSubjectId($subject->subject_id)->sum('points')),
                        ]);
                    }
                }
            }
            $all = [
                'student' => new StudentResource($student),
                'arranges' => $arranges,
            ];
            return ApiController::respondWithSuccess($arranges);
        } else {
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function my_child_teachers_list($id)
    {
        $student = Student::find($id);
        if ($student):
            $classroom = $student->classroom;
            $teachers = TeacherClassRoom::whereClassroomId($classroom->id)->get();
            return ApiController::respondWithSuccess(TeacherResource::collection($teachers));
        else:
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        endif;
    }
}
