<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\SubjectResource;
use App\Models\AboutUs;
use App\Models\School\City;
use App\Models\Setting;
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
    public function about_us()
    {
        $about = AboutUs::first();
        return ApiController::respondWithSuccess(new AboutResource($about));
    }
    public function contact_number()
    {
        $setting = Setting::first();
        $all = [
            'contact_number' => $setting->contact_number,
        ];
        return ApiController::respondWithSuccess($all);
    }
}
