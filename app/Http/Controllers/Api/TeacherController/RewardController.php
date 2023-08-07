<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\RewardResource;
use App\Models\Teacher\Reward;
use Illuminate\Http\Request;
use Validator;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $rewards = Reward::whereTeacherId($request->user()->id)->get();
        return ApiController::respondWithSuccess(RewardResource::collection($rewards));
    }

    public function create(Request $request)
    {
        $rules = [
            'name'    => 'required|string|max:191',
            'points'  => 'required|numeric',
            'photo'   => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        // create new Reward
        $reward = Reward::create([
            'teacher_id' => $request->user()->id,
            'name'       => $request->name,
            'points'     => $request->points,
            'photo'      => $request->file('photo') == null ? null : UploadImage($request->file('photo') , 'photo' , '/uploads/rewards'),
        ]);
        return  ApiController::respondWithSuccess(new RewardResource($reward));
    }
    public function edit(Request $request , $id)
    {
        $reward = Reward::find($id);
        if ($reward)
        {
            $rules = [
                'name'    => 'nullable|string|max:191',
                'points'  => 'nullable|numeric',
                'photo'   => 'nullable|mimes:jpg,jpeg,png,gif,tif,psd,pmp|max:5000',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            // update Reward
            $reward->update([
                'name'       => $request->name == null ? $reward->name : $request->name,
                'points'     => $request->points == null ? $reward->points : $request->points,
                'photo'      => $request->file('photo') == null ? $reward->photo : UploadImageEdit($request->file('photo') , 'photo' , '/uploads/rewards' , $reward->photo),
            ]);
            return  ApiController::respondWithSuccess(new RewardResource($reward));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function show($id)
    {
        $reward = Reward::find($id);
        if ($reward)
        {
            return  ApiController::respondWithSuccess(new RewardResource($reward));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function destroy($id)
    {
        $reward = Reward::find($id);
        if ($reward)
        {
            $reward->delete();
            $success = [
                'message' => trans('messages.deleted')
            ];
            return ApiController::respondWithSuccess($success);
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
}
