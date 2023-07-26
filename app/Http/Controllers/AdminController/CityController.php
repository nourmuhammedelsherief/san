<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\School\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::orderBy('id' , 'desc')->paginate(200);
        return view('admin.cities.index' , compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'name_ar'  => 'required|string|max:191',
            'name_en'  => 'required|string|max:191',
        ]);
        City::create([
            'name_ar'  => $request->name_ar,
            'name_en'  => $request->name_en,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('cities.index');
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
        $city = City::findOrFail($id);
        return view('admin.cities.edit' , compact('city'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $city = City::findOrFail($id);
        $this->validate($request , [
            'name_ar'  => 'required|string|max:191',
            'name_en'  => 'required|string|max:191',
        ]);
        $city->update([
            'name_ar'  => $request->name_ar,
            'name_en'  => $request->name_en,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('cities.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('cities.index');
    }
}
