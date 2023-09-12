<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
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
}
