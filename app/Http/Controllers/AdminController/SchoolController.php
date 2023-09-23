<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\History;
use App\Models\School\School;
use App\Models\Student;
use App\Models\Teacher\Teacher;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($status)
    {
        $schools = School::with('subscription')
            ->whereHas('subscription' , function ($q) use ($status){
                $q->whereStatus($status);
            })
            ->paginate(100);
        return view('admin.schools.index' , compact('status' , 'schools'));
    }

    public function schoolTeachers($id)
    {
        $school = School::findOrFail($id);
        $teachers = Teacher::with('teacher_classrooms')
            ->whereHas('teacher_classrooms', function ($q) use($school){
                $q->with('classroom');
                $q->whereHas('classroom', function ($c) use($school){
                    $c->whereSchoolId($school->id);
                });
            })->paginate(100);
        return view('admin.schools.teachers' , compact('school' , 'teachers'));
    }

    public function schoolStudents($id)
    {
        $school = School::findOrFail($id);
        $students = Student::with('classroom')
            ->whereHas('classroom' , function ($q) use($school){
                $q->whereSchoolId($school->id);
            })->get();
        return view('admin.schools.students' , compact('school' , 'students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $school = School::findOrFail($id);
        $school->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
    public function school_history($id)
    {
        $school = School::findOrFail($id);
        $histories = History::whereSchoolId($school->id)
            ->paginate(100);
        return view('admin.schools.school_history' , compact('school' , 'histories'));
    }
    public function school_classrooms($id)
    {
        $school = School::findOrFail($id);
        $classrooms = Classroom::whereSchoolId($school->id)->get();
        return view('admin.teachers.classrooms' , compact('classrooms' , 'school'));
    }
    public function school_classroom_teachers($school_id , $class_id)
    {
        $school = School::findOrFail($school_id);
        $classroom = Classroom::findOrFail($class_id);
        $teachers = Teacher::with('teacher_classrooms')
            ->whereHas('teacher_classrooms' , function ($q) use ($classroom){
                $q->whereClassroomId($classroom->id);
            })
            ->paginate(100);
        $status = 'active';
        return view('admin.schools.teachers' , compact('teachers' , 'school'));
    }
}
