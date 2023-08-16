<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\SubjectResource;
use App\Models\School\City;
use App\Models\Slider;
use App\Models\Subject;
use Illuminate\Http\Request;
use Spatie\FlareClient\Api;

class PublicController extends Controller
{
    public function cities()
    {
        $cities = City::all();
        return ApiController::respondWithSuccess(CityResource::collection($cities));
    }

    public function subjects()
    {
        $subjects = Subject::all();
        return ApiController::respondWithSuccess(SubjectResource::collection($subjects));
    }
    public function sliders()
    {
        $sliders = Slider::all();
        return ApiController::respondWithSuccess(SliderResource::collection($sliders));
    }
}
