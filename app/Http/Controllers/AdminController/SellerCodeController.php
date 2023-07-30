<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\SellerCode;
use Illuminate\Http\Request;

class SellerCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seller_codes = SellerCode::orderBy('id' , 'desc')->get();
        return view('admin.seller_codes.index' , compact('seller_codes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.seller_codes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'code'      => 'required|unique:seller_codes|max:191',
            'type'      => 'required|in:teacher,school',
            'discount'  => 'required|integer',
            'start_at'  => 'required|date',
            'end_at'    => 'required|date',
            'status'    => 'required|in:active,not_active'
        ]);
        //  create new seller_code
        SellerCode::create([
            'code'       => $request->code,
            'type'       => $request->type,
            'status'     => $request->status,
            'discount'   => $request->discount,
            'start_at'   => $request->start_at,
            'end_at'     => $request->end_at,
        ]);
        flash(trans('messages.created'))->success();
        return redirect()->route('seller_codes.index');
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
        $seller_code = SellerCode::findOrFail($id);
        return view('admin.seller_codes.edit' , compact('seller_code'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $seller_code = SellerCode::findOrFail($id);
        $this->validate($request , [
            'code'      => 'required|max:191|unique:seller_codes,code,' . $id,
            'type'      => 'required|in:teacher,school',
            'discount'  => 'required|integer',
            'start_at'  => 'required|date',
            'end_at'    => 'required|date',
        ]);
        $seller_code->update([
            'code'       => $request->code,
            'type'       => $request->type,
            'status'     => $request->status,
            'discount'   => $request->discount,
            'start_at'   => $request->start_at,
            'end_at'     => $request->end_at,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->route('seller_codes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seller_code = SellerCode::findOrFail($id);
        $seller_code->delete();
        flash(trans('messages.updated'))->success();
        return redirect()->route('seller_codes.index');
    }
}
