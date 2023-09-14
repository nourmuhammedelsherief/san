<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\School\SchoolRate;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherRate;
use App\Models\Teacher\TeacherSubject;
use Illuminate\Http\Request;

class SchoolTeacherRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $teacher = Teacher::findOrFail($id);
        $rates = TeacherRate::whereTeacherId($id)->get();
        return view('school.teachers.rates.index' , compact('rates' , 'teacher'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $teacher = Teacher::findOrFail($id);
        $rates = SchoolRate::whereSchoolId(auth()->guard('school')->user()->id)->get();
        return view('school.teachers.rates.create' , compact('rates' , 'teacher'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request , $id)
    {
        $teacher = Teacher::findOrFail($id);
        $this->validate($request, [
            'rates' => 'required',
        ]);
        if ($request->rates != null) {
            foreach ($request->rates as $rate) {
                $school_rate = SchoolRate::find($rate);
                // create teacher rate
                TeacherRate::create([
                    'teacher_id' => $teacher->id,
                    'rate_name'  => $school_rate->rate_name,
                    'points'     => $school_rate->points,
                    'type'       => $school_rate->type,
                    'school_rate_id' => $school_rate->id,
                ]);
            }
        }
        flash(trans('messages.created'))->success();
        return redirect()->route('schoolTeacherRate' , $teacher->id);
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
    public function destroy($id)
    {
        $rate = TeacherRate::findOrFail($id);
        $rate->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
}
