<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\School\SchoolReward;
use App\Models\Teacher\Reward;
use App\Models\Teacher\Teacher;
use Illuminate\Http\Request;

class SchoolTeacherRewardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $teacher = Teacher::findOrFail($id);
        $rewards = Reward::whereTeacherId($id)->get();
        return view('school.teachers.rewards.index' , compact('rewards' , 'teacher'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $teacher = Teacher::findOrFail($id);
        $rewards = SchoolReward::whereSchoolId(auth()->guard('school')->user()->id)->get();
        return view('school.teachers.rewards.create' , compact('rewards' , 'teacher'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request , $id)
    {
        $teacher = Teacher::findOrFail($id);
        $this->validate($request, [
            'rewards' => 'required',
        ]);
        if ($request->rewards != null) {
            foreach ($request->rewards as $reward) {
                $school_reward = SchoolReward::find($reward);
                // create teacher rate
                Reward::create([
                    'teacher_id' => $teacher->id,
                    'name'       => $school_reward->name,
                    'points'     => $school_reward->points,
                    'photo'      => $school_reward->photo,
                    'school_reward_id' => $school_reward->id,
                ]);
            }
        }
        flash(trans('messages.created'))->success();
        return redirect()->route('schoolTeacherReward' , $teacher->id);
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
        $reward = Reward::findOrFail($id);
        $reward->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
}
