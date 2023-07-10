<?php

namespace App\Http\Controllers\AdminController;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\CountryPackage;
use App\Models\History;
use App\Models\MarketerOperation;
use App\Models\Package;
use App\Models\Report;
use App\Models\Restaurant;
use App\Models\SellerCode;
use App\Models\Setting;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function my_profile()
    {
        $data = Admin::find(Auth::id());
        return view('admin.admins.profile.profile', compact('data'));
    }

    public function my_profile_edit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:admins,email,' . Auth::id(),
//            'password' => 'required|string|min:6|confirmed',
//            'password_confirm' => 'required_with:password|same:password|min:4',
            'phone' => 'required',
        ]);
        $data = Admin::where('id', Auth::id())->update(['name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);
        flash(trans('messages.updated'))->success();
        return redirect(url('/admin/profile'));

    }

    public function change_pass()
    {
        return view('admin.admins.profile.change_pass');
    }

    public function change_pass_update(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);

        $updated = Admin::where('id', Auth::id())->update([
            'password' => Hash::make($request->password)
        ]);
        flash(trans('messages.updated'))->success();
        return redirect(url('/admin/profile'));
    }

    public function index()
    {
        $data = Admin::all();
        return view('admin.admins.admins.index', compact('data'));
    }

    public function create()
    {
        return view('admin.admins.admins.create');
    }

    public function edit($id)
    {
        $data = Admin::find($id);
        return view('admin.admins.admins.edit', compact('data'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
//            'password_confirm' => 'required_with:password|same:password|min:4',
            'phone' => 'required',
        ]);


        $request['remember_token'] = Str::random(60);
        $request['password'] = Hash::make($request->password);
        Admin::create($request->all());
        flash(trans('messages.created'))->success();
        return redirect(url('/admin/admins'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::where('id', $id)->firstOrFail();
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            //    'password_confirm' => 'nullable|same:password|min:4',
            'phone' => 'required',
            'role' => 'sometimes',
        ]);
        $data = $request->only(['name' , 'email' , 'phone']);
        if(!empty($request->password)):
            $data['password'] = Hash::make($request->password);
        endif;

        $data['remember_token'] = Str::random(60);

        $admin->update($data);
        flash(trans('messages.updated'))->success();
        return redirect(url('/admin/admins'));
    }

    public function admin_delete($id)
    {
        Admin::where('id', $id)->delete();
        flash(trans('messages.deleted'))->success();
        return back();
    }
}
