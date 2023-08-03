<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\StudentResource;
use App\Models\Student;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Http\Request;
use Validator;

class StudentController extends Controller
{
    public function index($id)
    {
        $students = Student::whereClassroomId($id)->get();
        return ApiController::respondWithSuccess(StudentResource::collection($students));
    }
    public function create(Request $request , $id)
    {
        $classroom = TeacherClassRoom::find($id);
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
            $student = Student::create([
                'classroom_id'  => $classroom->id,
                'name'          => $request->name,
                'gender'        => $request->gender,
                'photo'         => $request->file('photo') == null ? null : UploadImage($request->file('photo') , 'photo' , '/uploads/students'),
                'birth_date'    => $request->birth_date,
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