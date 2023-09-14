<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\School\School;
use App\Models\School\SchoolReward;
use App\Models\Teacher\Reward;
use Illuminate\Http\Request;

class SchoolRewardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rewards = SchoolReward::whereSchoolId(auth()->guard('school')->user()->id)->get();
        return view('school.rewards.index' , compact('rewards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('school.rewards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'name'    => 'required|string|max:191',
            'photo'   => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'points'  => 'required|numeric',
        ]);
        // create new reward
        SchoolReward::create([
            'school_id' => auth()->guard('school')->user()->id,
            'name'      => $request->name,
            'photo'     => $request->file('photo') == null ? null : UploadImage($request->file('photo') , 'photo' , '/uploads/rewards'),
            'points'    => $request->points,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('rewards.index');
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
        $reward = SchoolReward::findOrFail($id);
        return view('school.rewards.edit' , compact('reward'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reward = SchoolReward::findOrFail($id);
        $this->validate($request , [
            'name'    => 'required|string|max:191',
            'photo'   => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp,webp|max:5000',
            'points'  => 'required|numeric',
        ]);
        $reward->update([
            'name'      => $request->name,
            'photo'     => $request->file('photo') == null ? $reward->photo : UploadImageEdit($request->file('photo') , 'photo' , '/uploads/rewards' , $reward->photo),
            'points'    => $request->points,
        ]);
        // update school teacher rewards
        $rewards = Reward::where('school_reward_id' , $id)->get();
        if ($rewards->count() > 0)
        {
            foreach ($rewards as $reward)
            {
                $reward->update([
                    'name'      => $request->name,
                    'photo'     => $request->file('photo') == null ? $reward->photo : UploadImageEdit($request->file('photo') , 'photo' , '/uploads/rewards' , $reward->photo),
                    'points'    => $request->points,
                ]);
            }
        }
        flash(trans('messages.updated'))->success();
        return redirect()->route('rewards.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reward = SchoolReward::findOrFail($id);
        if ($reward->photo)
        {
            @unlink(public_path('/uploads/rewards/' . $reward->photo));
        }
        $reward->delete();
        flash(trans('messages.updated'))->success();
        return redirect()->route('rewards.index');
    }
}
