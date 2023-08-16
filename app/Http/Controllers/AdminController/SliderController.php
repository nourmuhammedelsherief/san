<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = Slider::orderBy('id' , 'desc')->get();
        return view('admin.sliders.index' , compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'photo'  => 'required|mimes:jpg,jpeg,png,gif,tif,psd,bmp,webp|max:5000'
        ]);
        /// create new slider
        Slider::create([
            'photo'   => UploadImage($request->file('photo') , 'photo' , '/uploads/sliders')
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('sliders.index');
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
        $slider = Slider::findOrFail($id);
        return view('admin.sliders.edit' , compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $slider = Slider::findOrFail($id);
        $this->validate($request , [
            'photo'  => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,bmp,webp|max:5000'
        ]);

        $slider->update([
            'photo'   => $request->file('photo') == null ? $slider->photo : UploadImageEdit($request->file('photo') , 'photo' , '/uploads/sliders' , $slider->photo)
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('sliders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $slider = Slider::findOrFail($id);
        if ($slider->photo)
        {
            @unlink(public_path('/uploads/sliders/' . $slider->photo));
        }
        $slider->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->route('sliders.index');
    }
}
