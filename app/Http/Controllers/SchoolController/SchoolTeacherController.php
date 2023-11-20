<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\School\City;
use App\Models\Subject;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherClassRoom;
use App\Models\Teacher\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SchoolTeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::with('teacher_classrooms')
            ->whereHas('teacher_classrooms', function ($q) {
                $q->with('classroom');
                $q->whereHas('classroom', function ($c) {
                    $c->whereSchoolId(auth()->guard('school')->user()->id);
                });
            })->paginate(100);
        return view('school.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classrooms = Classroom::whereSchoolId(auth()->guard('school')->user()->id)->get();
        $cities = City::all();
        $subjects = Subject::all();
        return view('school.teachers.create', compact('classrooms', 'subjects', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'classrooms' => 'required',
            'classrooms*' => 'exists|classrooms,id',
            'subjects' => 'required',
            'subjects*' => 'exists|subjects,id',
            "city_id" => 'required|exists:cities,id',
            "name" => 'required|string|max:191',
            "email" => 'required|email|max:191|unique:teachers,email',
            "password" => 'required|min:6',
            "password_confirmation" => 'required|same:password',
            "active" => 'required|in:true,false',
            "whatsapp" => 'nullable|in:true,false',
            "phone_number" => 'nullable|min:10',
            "photo" => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000'
        ]);
        // Available alpha caracters
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $integration_code = $characters[rand(0, strlen($characters) - 1)]. $characters[rand(0, strlen($characters) - 1)] . mt_rand(100, 999);
        $invitation_code = $characters[rand(0, strlen($characters) - 1)] . mt_rand(1000, 9999);
        // create new Teacher
        $teacher = Teacher::create([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'email' => $request->email,
            'photo' => $request->file('photo') == null ? null : UploadImage($request->file('photo'), 'photo', '/uploads/teachers'),
            'school' => auth()->guard('school')->user()->name,
            'type' => 'school',
            'active' => $request->active,
            'whatsapp' => $request->whatsapp,
            'phone_number' => $request->phone_number,
            'integration_code' => $integration_code,
            'invitation_code' => $invitation_code,
            'password' => Hash::make($request->password),
        ]);
        // add teacher subjects
        if ($request->subjects != null) {
            foreach ($request->subjects as $subject) {
                // create teacher subject
                TeacherSubject::create([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject,
                ]);
            }
        }
        // add teacher classrooms
        if ($request->classrooms != null) {
            foreach ($request->classrooms as $classroom) {
                $teacher_classroom = TeacherClassRoom::create([
                    'teacher_id' => $teacher->id,
                    'classroom_id' => $classroom,
                    'name' => Classroom::find($classroom)->name,
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
            }
        }
        flash(trans('messages.created'))->success();
        return redirect()->route('teachers.index');
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
        $teacher = Teacher::findOrFail($id);
        $classrooms = Classroom::whereSchoolId(auth()->guard('school')->user()->id)->get();
        $cities = City::all();
        $subjects = Subject::all();
        return view('school.teachers.edit', compact('classrooms', 'subjects', 'cities', 'teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::findOrFail($id);
        $this->validate($request, [
            'classrooms' => 'nullable',
            'classrooms*' => 'exists|classrooms,id',
            'subjects' => 'nullable',
            'subjects*' => 'exists|subjects,id',
            "city_id" => 'required|exists:cities,id',
            "name" => 'required|string|max:191',
            "email" => 'required|email|max:191|unique:teachers,email,'.$teacher->id,
            "password" => 'nullable|min:6|confirmed',
            "active" => 'required|in:true,false',
            "whatsapp" => 'nullable|in:true,false',
            "phone_number" => 'nullable|min:10',
            "photo" => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000'
        ]);
        $teacher->update([
            'city_id' => $request->city_id,
            'name' => $request->name,
            'email' => $request->email,
            'photo' => $request->file('photo') == null ? $teacher->photo : UploadImageEdit($request->file('photo'), 'photo', '/uploads/teachers' , $teacher->photo),
            'school' => auth()->guard('school')->user()->name,
            'active' => $request->active == null ? $teacher->active : $request->active,
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
        // add teacher classrooms
        if ($request->classrooms != null) {
            TeacherClassRoom::whereTeacherId($teacher->id)->delete();
            foreach ($request->classrooms as $classroom) {
                $teacher_classroom = TeacherClassRoom::create([
                    'teacher_id' => $teacher->id,
                    'classroom_id' => $classroom,
                    'name' => Classroom::find($classroom)->name,
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
            }
        }
        flash(trans('messages.updated'))->success();
        return redirect()->route('teachers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);
        if ($teacher->photo):
            @unlink(public_path('/uploads/teachers/' . $teacher->photo));
        endif;
        $teacher->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('teachers.index');
    }
}
