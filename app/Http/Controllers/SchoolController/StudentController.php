<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id = null)
    {
        $students = Student::with('classroom')
            ->whereHas('classroom' , function ($q){
                $q->whereSchoolId(auth()->guard('school')->user()->id);
            })->paginate(100);
        return view('school.students.index' , compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classrooms = Classroom::whereSchoolId(auth()->guard('school')->user()->id)->get();
        return view('school.students.create' , compact('classrooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'classroom_id'=> 'required|exists:classrooms,id',
            'name'        => 'required|string|max:191',
            'gender'      => 'required|in:male,female',
            'photo'       => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'birth_date'  => 'required|date',
        ]);
        // create new student
        $identity_id = mt_rand(10000000, 99999999);
        $password = mt_rand(10000000, 99999999);
        $student = Student::create([
            'classroom_id'  => $request->classroom_id,
            'name'          => $request->name,
            'gender'        => $request->gender,
            'photo'         => $request->file('photo') == null ? null : UploadImage($request->file('photo') , 'photo' , '/uploads/students'),
            'birth_date'    => $request->birth_date,
            'points'        => 0,
            'identity_id'   => $identity_id,
            'password'      => Hash::make($password),
            'un_hashed_password' => $password,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('students.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        $classrooms = Classroom::whereSchoolId(auth()->guard('school')->user()->id)->get();
        return view('school.students.edit' , compact('classrooms' , 'student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        $this->validate($request , [
            'classroom_id'=> 'required|exists:classrooms,id',
            'name'        => 'required|string|max:191',
            'gender'      => 'required|in:male,female',
            'photo'       => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'birth_date'  => 'required|date',
        ]);

        $student->update([
            'classroom_id'  => $request->classroom_id,
            'name'          => $request->name,
            'gender'        => $request->gender,
            'photo'         => $request->file('photo') == null ? $student->photo : UploadImageEdit($request->file('photo') , 'photo' , '/uploads/students' , $student->photo),
            'birth_date'    => $request->birth_date,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('students.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        if ($student->photo)
        {
            @unlink(public_path('/uploads/students/' . $student->photo));
        }
        $student->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('students.index');
    }
}
