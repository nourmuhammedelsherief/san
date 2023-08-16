<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\StudentResource;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class StudentController extends Controller
{
    public function index(Request $request , $id)
    {
        $classroom = Classroom::find($id);
        // check if the teacher related with this class
        $check = TeacherClassRoom::whereClassroomId($id)
            ->whereteacherId($request->user()->id)
            ->first();
        if ($classroom and $check)
        {
            $students = Student::whereClassroomId($id)->get();
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
                'password'      => $password,
                'hashed_password' => Hash::make($password),
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
    public function destroy($id)
    {
        $student = Student::find($id);
        if ($student)
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
}
