<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\Teacher\ClassRoomResource;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\StudentRate;
use App\Models\Teacher\StudentReward;
use App\Models\Teacher\TeacherClassRoom;
use App\Models\Teacher\TeacherSubject;
use Illuminate\Http\Request;
use Validator;

class TClassRoomController extends Controller
{
    public function my_class_rooms(Request $request)
    {
        $teacher = $request->user();
        $class_rooms = TeacherClassRoom::whereTeacherId($teacher->id)
            ->whereArchive('false')
            ->get();
        return ApiController::respondWithSuccess(ClassRoomResource::collection($class_rooms));
    }

    public function my_archived_class_rooms(Request $request)
    {
        $teacher = $request->user();
        $class_rooms = TeacherClassRoom::whereTeacherId($teacher->id)
            ->whereArchive('true')
            ->get();
        return ApiController::respondWithSuccess(ClassRoomResource::collection($class_rooms));
    }

    public function my_subjects(Request $request)
    {
        $teacher = $request->user();
        $subjects = TeacherSubject::whereTeacherId($teacher->id)->get();
        return ApiController::respondWithSuccess(SubjectResource::collection($subjects));
    }

    public function create(Request $request)
    {
        $teacher = $request->user();
        $rules = [
            'name' => 'required|string|max:191',
            'subjects' => 'required',
            'subjects*' => 'exists|subjects,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        // create new classroom
        $classroom = Classroom::create([
            'name' => $request->name,
            'school_name' => $teacher->school
        ]);
        $teacher_classroom = TeacherClassRoom::create([
            'classroom_id' => $classroom->id,
            'name' => $request->name,
            'teacher_id' => $teacher->id,
            'main_teacher_id' => $teacher->id,
            'pulled' => 'false',
            'archive' => 'false',
        ]);
        if ($request->subjects != null) {
            foreach ($request->subjects as $subject) {
                // create teacher subject
                ClassRoomSubject::create([
                    'class_room_id' => $teacher_classroom->id,
                    'subject_id' => $subject,
                ]);
            }
        }
        return ApiController::respondWithSuccess(new ClassRoomResource($teacher_classroom));
    }

    public function edit(Request $request, $id)
    {
        $teacher = $request->user();
        $classroom = TeacherClassRoom::find($id);
        if ($classroom and $classroom->pulled == 'false') {
            $rules = [
                'name' => 'sometimes|string|max:191',
                'subjects' => 'nullable',
                'subjects*' => 'exists|subjects,id',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $classroom->update([
                'name' => $request->name == null ? $classroom->name : $request->name,
            ]);
            $classroom->classroom->update([
                'name' => $request->name == null ? $classroom->name : $request->name,
            ]);
            if ($request->subjects != null) {
                ClassRoomSubject::whereClassRoomId($classroom->id)->delete();
                foreach ($request->subjects as $subject) {
                    // create teacher subject
                    ClassRoomSubject::create([
                        'class_room_id' => $classroom->id,
                        'subject_id' => $subject,
                    ]);
                }
            }
            return ApiController::respondWithSuccess(new ClassRoomResource($classroom));
        } else {
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function show($id)
    {
        $classroom = TeacherClassRoom::find($id);
        if ($classroom) {
            return ApiController::respondWithSuccess(new ClassRoomResource($classroom));
        } else {
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function destroy($id)
    {
        $classroom = TeacherClassRoom::find($id);
        if ($classroom and $classroom->main_teacher_id == $classroom->teacher_id) {
            $classroom->delete();
            $success = [
                'message' => trans('messages.deleted')
            ];
            return ApiController::respondWithSuccess($success);
        } else {
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function archive(Request $request, $id)
    {
        $classroom = TeacherClassRoom::find($id);
        if ($classroom) {
            $rules = [
                'archive' => 'required|in:true,false',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $classroom->update([
                'archive' => $request->archive
            ]);
            return ApiController::respondWithSuccess(new ClassRoomResource($classroom));
        } else {
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function copy(Request $request, $id)
    {
        $Tclassroom = TeacherClassRoom::find($id);
        if ($Tclassroom){
            $rules = [
                'name' => 'sometimes|string|max:191',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            // create classroom
            $classroom = Classroom::create([
                'name'  => $request->name == null ? $Tclassroom->classroom->name : $request->name,
            ]);
            // add classroom to teacher
            $teacher_classroom = TeacherClassRoom::create([
                'classroom_id' => $classroom->id,
                'name'         => $classroom->name,
                'teacher_id'   => $request->user()->id,
                'main_teacher_id' => $request->user()->id,
                'pulled'     => 'false',
                'archive'    => 'false',
            ]);
            // add subjects to classroom
            $classroom_subjects = ClassRoomSubject::whereClassRoomId($Tclassroom->id)->get();
            if ($classroom_subjects->count() > 0)
            {
                foreach ($classroom_subjects as $classroom_subject)
                {
                    $classroom_subject->update([
                        'class_room_id' => $teacher_classroom->id,
                    ]);
                }
            }
            // add students to new classroom
            $old_students = Student::whereClassroomId($Tclassroom->classroom_id)->get();
            foreach ($old_students as $old_student)
            {
                $old_student->update([
                    'classroom_id' => $classroom->id,
                    'points'       => 0,
                ]);
                // delete student rewards
                StudentReward::whereStudentId($old_student->id)->delete();
                // delete student rates
                StudentRate::whereStudentId($old_student->id)->delete();
            }
            return ApiController::respondWithSuccess(new ClassRoomResource($teacher_classroom));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

}
