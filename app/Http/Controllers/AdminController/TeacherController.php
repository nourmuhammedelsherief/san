<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Father\Father;
use App\Models\History;
use App\Models\School\City;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherClassRoom;
use App\Models\Teacher\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $classrooms = Classroom::where('school_id' , null)
            ->orderBy('id' , 'desc')
            ->get();
        $school = null;
        return view('admin.teachers.classrooms' , compact('classrooms' ,'school'));
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
    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);
        $cities = City::all();
        $subjects = Subject::all();
        return view('admin.teachers.edit' , compact('teacher' , 'cities' , 'subjects'));
    }
    public function update(Request $request , $id)
    {
        $teacher = Teacher::findOrFail($id);
        $this->validate($request, [
            'subjects' => 'nullable',
            'subjects*' => 'exists|subjects,id',
            "city_id" => 'required|exists:cities,id',
            "name" => 'required|string|max:191',
            "email" => 'required|email|max:191|unique:teachers,email,'.$teacher->id,
            "password" => 'nullable|min:6|confirmed',
            "whatsapp" => 'nullable|in:true,false',
            "phone_number" => 'nullable|min:10',
        ]);
        $teacher->update([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'email' => $request->email,
            'school' => $request->school == null ? $teacher->school : $request->school,
            'whatsapp' => $request->whatsapp == null ? $teacher->whatsapp : $request->whatsapp,
            'phone_number' => $request->phone_number == null ? $teacher->phone_number : $request->phone_number,
            'password' => $request->password != null ? Hash::make($request->password) : $teacher->password,
        ]);
        // add teacher subjects
        if ($request->subjects != null) {
            TeacherSubject::whereTeacherId($teacher->id)->delete();
            foreach ($request->subjects as $subject) {
                // create teacher subject
                TeacherSubject::updateOrCreate([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject,
                ]);
            }
        }
        flash(trans('messages.updated'))->success();
        return redirect()->to(url('/admin/teachers/'. $teacher->subscription->status));
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

    public function teacher_history($id)
    {
        $teacher = Teacher::findOrFail($id);
        $histories = History::whereTeacherId($teacher->id)
            ->paginate(100);
        return view('admin.teachers.teacher_history' , compact('histories' , 'teacher'));
    }
}
