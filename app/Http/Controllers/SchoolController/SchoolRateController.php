<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\School\SchoolRate;
use App\Models\Teacher\TeacherRate;
use Illuminate\Http\Request;

class SchoolRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rates = SchoolRate::whereSchoolId(auth()->guard('school')->user()->id)->get();
        return view('school.rates.index' , compact('rates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('school.rates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'rate_name' => 'required|string|max:191',
            'points'    => 'required|numeric',
            'type'      => 'required',
        ]);
        // create new rate
        SchoolRate::create([
            'school_id'  => auth()->guard('school')->user()->id,
            'rate_name'  => $request->rate_name,
            'points'     => $request->points,
            'type'       => $request->type,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('rates.index');
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
        $rate = SchoolRate::findOrFail($id);
        return view('school.rates.edit' , compact('rate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rate = SchoolRate::findOrFail($id);
        $this->validate($request , [
            'rate_name' => 'required|string|max:191',
            'points'    => 'required|numeric',
            'type'      => 'required',
        ]);
        $rate->update([
            'rate_name'  => $request->rate_name,
            'points'     => $request->points,
            'type'       => $request->type,
        ]);
        // update the school teachers rates
        $rates = TeacherRate::where('school_rate_id' , $id)->get();
        if ($rates->count() > 0)
        {
            foreach ($rates as $rate)
            {
                $rate->update([
                    'rate_name'  => $request->rate_name,
                    'points'     => $request->points,
                    'type'       => $request->type,
                ]);
            }
        }
        flash(trans('messages.updated'))->success();
        return redirect()->route('rates.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rate = SchoolRate::findOrFail($id);
        $rate->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('rates.index');
    }
}
