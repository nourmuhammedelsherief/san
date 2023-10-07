<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Student\StudentRateResource;
use App\Http\Resources\Student\StudentRewardResource;
use App\Http\Resources\Teacher\StudentResource;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher\StudentRate;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class StudentController extends Controller
{
    public function index(Request $request , $id)
    {
        $rules = [
            'subject_id' => 'sometimes|exists:subjects,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $classroom = Classroom::find($id);
        // check if the teacher related with this class
        $check = TeacherClassRoom::whereClassroomId($id)
            ->whereteacherId($request->user()->id)
            ->first();
        if ($classroom and $check)
        {
            if ($request->subject_id == null)
            {
                $students = Student::whereClassroomId($id)
                    ->orderBy('points' , 'desc')
                    ->get();
            }else{
                $students = Student::with('rates' , 'rewards')
                    ->whereHas('rates' , function ($r) use ($request){
                        $r->whereSubjectId($request->subject_id);
                    })
                    ->whereClassroomId($id)
                    ->orderBy('points' , 'desc')
                    ->get();
            }
            return ApiController::respondWithSuccess(StudentResource::collection($students));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function create(Request $request , $id)
    {
        $classroom = Classroom::find($id);
        if ($classroom)
        {
            $rules = [
                'name'        => 'required|string|max:191',
                'gender'      => 'required|in:male,female',
                'photo'       => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
                'birth_date'  => 'required|date',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            // create new student
            $identity_id = mt_rand(10000000, 99999999);
            $password = mt_rand(10000000, 99999999);
            $student = Student::create([
                'classroom_id'  => $classroom->id,
                'name'          => $request->name,
                'gender'        => $request->gender,
                'photo'         => $request->file('photo') == null ? null : UploadImage($request->file('photo') , 'photo' , '/uploads/students'),
                'birth_date'    => $request->birth_date,
                'points'        => 0,
                'identity_id'   => $identity_id,
                'password'      => Hash::make($password),
                'un_hashed_password' => $password,
            ]);
            return ApiController::respondWithSuccess(new StudentResource($student));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function edit(Request $request , $id)
    {
        $student = Student::find($id);
        if ($student)
        {
            $rules = [
                'name'        => 'nullable|string|max:191',
                'gender'      => 'nullable|in:male,female',
                'photo'       => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
                'birth_date'  => 'nullable|date',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $student->update([
                'name'          => $request->name == null ? $student->name : $request->name,
                'gender'        => $request->gender == null ? $student->gender : $request->gender,
                'photo'         => $request->file('photo') == null ? $student->photo : UploadImageEdit($request->file('photo') , 'photo' , '/uploads/students' , $student->photo),
                'birth_date'    => $request->birth_date == null ? $student->birth_date : $request->birth_date,
            ]);
            return ApiController::respondWithSuccess(new StudentResource($student));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function show($id)
    {
        $student = Student::find($id);
        if ($student)
        {
            return ApiController::respondWithSuccess(new StudentResource($student));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function destroy(Request $request , $id)
    {
        $teacher = $request->user();
        $student = Student::find($id);
        $check = TeacherClassRoom::whereTeacherId($teacher->id)
            ->whereClassroomId($student->classroom_id)
            ->where('main_teacher_id' , $teacher->id)
            ->where('pulled' , 'false')
            ->first();
        if ($student and $check)
        {
            $student->delete();
            $success = [
                'message' => trans('messages.deleted')
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function honor_board(Request $request , $id)
    {
        $rules = [
            'subject_id' => 'sometimes|exists:subjects,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        $classroom = Classroom::find($id);
        // check if the teacher related with this class
        $check = TeacherClassRoom::whereClassroomId($id)
            ->whereteacherId($request->user()->id)
            ->first();
        if ($classroom and $check)
        {
            if ($request->subject_id == null)
            {
                $students = Student::whereClassroomId($id)
                    ->orderBy('points' , 'desc')
                    ->get();
            }else{
                $students = Student::with('rates' , 'rewards')
                    ->whereHas('rates' , function ($r) use ($request){
                        $r->whereSubjectId($request->subject_id);
                    })
                    ->whereClassroomId($id)
                    ->orderBy('points' , 'desc')
                    ->get();
            }
            return ApiController::respondWithSuccess(StudentResource::collection($students));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
}
