<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id' , 'desc')
            ->paginate(300);
        return view('admin.users.index' , compact('users'));
    }
    public function course_subscribers($id)
    {
        $users = User::with('courses')
            ->whereHas('courses' , function ($q) use ($id){
                $q->whereCourseId($id);
                $q->whereStatus('active');
            })
            ->paginate(300);
        return view('admin.users.index' , compact('users'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'name'          => 'required|string|max:191',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'phone'  => ['required', 'unique:users','regex:/^((05)|(01))[0-9]{8}/' , 'max:11'],
        ]);
        // create new  users
        User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'phone'        => $request->phone,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return  view('admin.users.edit' , compact('user' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->validate($request , [
            'name'          => 'required|string|max:191',
            'email'         => 'required|email|unique:users,email,' . $id,
            'password'      => 'nullable|string|min:6|confirmed',
            'phone'         => ['required', 'unique:users,phone,'.$id,'regex:/^((05)|(01))[0-9]{8}/' , 'max:11'],
        ]);
        $user->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'password'     => $request->password == null ? $user->password : Hash::make($request->password)
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->photo != null) {
            if (file_exists(public_path('uploads/users/' . $user->photo))) {
                unlink(public_path('uploads/users/' . $user->photo));
            }
        }
        $user->delete();
        flash(trans('messages.deleted'))->success();
        return back();
    }


    public function active_user($id, $active)
    {
//        dd($active);
        $user = User::findOrFail($id);
        $user->update([
            'active' => $active
        ]);
        flash('تم تغيير الخصوصية بنجاح')->success();
        return redirect()->back();
    }
}
