<?php

namespace App\Http\Controllers\SchoolController;

use App\Http\Controllers\Controller;
use App\Models\School\City;
use App\Models\School\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    public function my_profile()
    {
        $cities = City::all();
        $data = School::find(Auth::guard('school')->user()->id);
        return view('school.admins.profile.profile', compact('data' , 'cities'));
    }

    public function my_profile_edit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:schools,email,' . Auth::guard('school')->user()->id,
//            'password' => 'required|string|min:6|confirmed',
//            'password_confirm' => 'required_with:password|same:password|min:4',
            'identity_code' => 'required',
            'city_id' => 'required',
        ]);
        $data = School::find(Auth::guard('school')->user()->id);
        $data->update([
            'name' => $request->name,
            'email' => $request->email,
            'city_id' => $request->city_id,
            'identity_code' => $request->identity_code
        ]);
        flash(trans('messages.updated'))->success();
        return redirect(url('/school/profile'));

    }

    public function change_pass()
    {
        return view('school.admins.profile.change_pass');
    }

    public function change_pass_update(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);
        $data = School::find(Auth::guard('school')->user()->id);
        $data->update([
            'password' => Hash::make($request->password)
        ]);
        flash(trans('messages.updated'))->success();
        return redirect(url('/school/profile'));
    }

}
