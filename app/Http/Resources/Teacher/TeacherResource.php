<?php

namespace App\Http\Resources\Teacher;

use App\Http\Resources\CityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'city_id' => new CityResource($this->city),
            'name'    => $this->name,
            'active'  => $this->active,
            'email'   => $this->email,
            'integration_code' => $this->integration_code,
            'photo'      => $this->photo == null ? null : asset('/uploads/teachers/' . $this->photo),
            'school'   => $this->school,
            'type'     => $this->type,
            'api_token' => $this->api_token,
            'phone_number' => $this->phone_number,
        ];
    }
}
