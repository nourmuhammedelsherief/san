<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Father\Father;
use App\Models\Student;
use App\Models\Teacher\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index($status)
    {
        $teachers = Teacher::with('subscription')
            ->whereHas('subscription' , function ($q) use ($status){
                $q->whereStatus($status);
            })
            ->paginate(100);
        return view('admin.teachers.index' , compact('teachers' , 'status'));
    }

    public function classrooms()
    {
        $classrooms = Classroom::orderBy('id' , 'desc')->get();
        return view('admin.teachers.classrooms' , compact('classrooms'));
    }

    public function classroom_teachers($id)
    {
        $classroom = Classroom::findOrFail($id);
        $teachers = Teacher::with('teacher_classrooms')
            ->whereHas('teacher_classrooms' , function ($q) use ($classroom){
                $q->whereClassroomId($classroom->id);
            })
            ->paginate(100);
        $status = 'active';
        return view('admin.teachers.index' , compact('teachers' , 'status'));
    }
    public function classroom_students($id)
    {
        $classroom = Classroom::findOrFail($id);
        $students = $classroom->students;
        return view('admin.teachers.students' , compact('classroom' , 'students'));
    }
    public function father_children($id)
    {
        $parent = Father::findOrFail($id);
        $students = Student::with('parents')
            ->whereHas('parents' , function ($q) use ($parent){
                $q->whereFatherId($parent->id);
            })->get();
        return view('admin.teachers.father_children' , compact('students' , 'parent'));
    }
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
    public function delete_classroom($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
    public function delete_student($id)
    {
        $std = Student::findOrFail($id);
        $std->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }

    public function parents()
    {
        $parents = Father::orderBy('id' , 'desc')->paginate(200);
        return view('admin.teachers.parents' , compact('parents'));
    }
    public function delete_parent($id)
    {
        $parent = Father::findOrFail($id);
        $parent->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
}
