<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::orderBy('id' , 'desc')->get();
        return view('admin.subjects.index' , compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'name_ar'   => 'required|string|max:191',
            'name_en'   => 'required|string|max:191',
        ]);
        Subject::create([
            'name_ar'   => $request->name_ar,
            'name_en'   => $request->name_en,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('subjects.index');
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
        $subject = Subject::findOrFail($id);
        return view('admin.subjects.edit' , compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subject = Subject::findOrFail($id);
        $this->validate($request , [
            'name_ar'   => 'required|string|max:191',
            'name_en'   => 'required|string|max:191',
        ]);
        $subject->update([
            'name_ar'   => $request->name_ar,
            'name_en'   => $request->name_en,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('subjects.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('subjects.index');
    }
}
