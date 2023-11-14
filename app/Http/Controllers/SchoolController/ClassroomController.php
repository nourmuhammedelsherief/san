<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\ClassRoomResource;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher\ClassRoomSubject;
use App\Models\Teacher\StudentRate;
use App\Models\Teacher\StudentReward;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classroom::whereSchool_id(auth()->guard('school')->user()->id)
            ->orderBy('id' , 'desc')
            ->paginate(100);
        return view('school.classes.index' , compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('school.classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'name'   => 'required|string|max:191',
        ]);
        // create new classroom
        Classroom::create([
            'school_id'  => auth()->guard('school')->user()->id,
            'name'       => $request->name,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('classrooms.index');
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
        $class = Classroom::findOrFail($id);
        return view('school.classes.edit' , compact('class'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $class = Classroom::findOrFail($id);
        $this->validate($request , [
            'name'   => 'required|string|max:191',
        ]);
        $class->update([
            'school_id'  => auth()->guard('school')->user()->id,
            'name'       => $request->name,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('classrooms.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $class = Classroom::findOrFail($id);
        $class->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('classrooms.index');
    }
    public function copy($id)
    {
        $classroom = Classroom::findOrFail($id);
        return view('school.classes.copy' , compact('classroom'));
    }
    public function submit_copy(Request $request, $id)
    {
        $this->validate($request , [
            'name' => 'nullable|string|max:191',
        ]);
        $old_classroom = Classroom::findOrFail($id);
        $Tclassrooms = TeacherClassRoom::whereClassroomId($old_classroom->id)
            ->get();
        if ($Tclassrooms->count() > 0)
        {
            // create classroom
            $classroom = Classroom::create([
                'name'  => $request->name == null ? $old_classroom->name : $request->name,
                'school_id' => auth()->guard()->user()->id,
            ]);
            // add students to new classroom
            $old_students = Student::whereClassroomId($old_classroom->id)->get();
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
            foreach ($Tclassrooms as $Tclassroom)
            {
                // add classroom to teacher
                $teacher_classroom = TeacherClassRoom::create([
                    'classroom_id' => $classroom->id,
                    'name'         => $classroom->name,
                    'teacher_id'   => $Tclassroom->teacher_id,
                    'main_teacher_id' => $Tclassroom->main_teacher_id,
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
                $Tclassroom->delete();
            }
        }
        $old_classroom->delete();
        flash(trans('messages.copiedSuccessfully'))->success();
        return redirect()->route('classrooms.index');
    }

}
