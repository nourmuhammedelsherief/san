<?php

namespace App\Http\Controllers\Api\TeacherController;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\TeacherRateResource;
use App\Models\Teacher\TeacherRate;
use Illuminate\Http\Request;
use Validator;

class TeacherRateController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();
        $rules = [
            'type'      => 'nullable|in:positive,negative,both',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        if ($request->type == null or $request->type == 'both'):
            $rates = TeacherRate::whereTeacherId($teacher->id)->get();
        else:
            $rates = TeacherRate::whereTeacherId($teacher->id)
                ->whereType($request->type)
                ->get();
        endif;
        return ApiController::respondWithSuccess(TeacherRateResource::collection($rates));
    }

    public function create(Request $request)
    {
        $teacher = $request->user();
        $rules = [
            'rate_name' => 'required|string|max:191',
            'points'    => 'required|numeric',
            'type'      => 'required|in:positive,negative',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

        // create new rate
        $rate = TeacherRate::create([
            'teacher_id' => $teacher->id,
            'rate_name'  => $request->rate_name,
            'points'     => $request->points,
            'type'       => $request->type,
        ]);
        return ApiController::respondWithSuccess(new TeacherRateResource($rate));
    }
    public function edit(Request $request , $id)
    {
        $rate = TeacherRate::find($id);
        if ($rate)
        {
            $teacher = $request->user();
            $rules = [
                'rate_name' => 'nullable|string|max:191',
                'points'    => 'nullable|numeric',
                'type'      => 'nullable|in:positive,negative',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
                return ApiController::respondWithErrorObject(validateRules($validator->errors(), $rules));

            $rate->update([
                'rate_name'  => $request->rate_name == null ? $rate->rate_name : $request->rate_name,
                'points'     => $request->points == null ? $rate->points : $request->points,
                'type'       => $request->type == null ? $rate->type : $request->type,
            ]);
            return ApiController::respondWithSuccess(new TeacherRateResource($rate));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }

    public function show($id)
    {
        $rate = TeacherRate::find($id);
        if ($rate)
        {
            return ApiController::respondWithSuccess(new TeacherRateResource($rate));
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
    public function destroy($id)
    {
        $rate = TeacherRate::find($id);
        if ($rate)
        {
            if ($rate->students->count() > 0)
            {
                $error = [
                    'message' => trans('messages.ItemCannotDeleted')
                ];
                return ApiController::respondWithErrorObject($error);
            }else{
                $rate->delete();
                $success = [
                    'message' => trans('messages.deleted')
                ];
                return ApiController::respondWithSuccess($success);
            }
        }else{
            $error = ['message' => trans('messages.not_found')];
            return ApiController::respondWithErrorNOTFoundObject($error);
        }
    }
}
