<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'classroom_id'  => $this->classroom_id,
            'classroom'    => $this->classroom->name,
            'name'     => $this->name,
            'gender'   => $this->gender,
            'photo'    => $this->photo == null ? null : asset('/uploads/students/' . $this->photo),
            'birth_date' => $this->birth_date->format('Y-m-d'),
            'age'      => \Carbon\Carbon::parse($this->birth_date)->diff(\Carbon\Carbon::now())->format('%y'),
            'points'  => $this->points,
            'rates'   => StudentRateResource::collection($this->rates),
            'rewards' => StudentRewardResource::collection($this->rewards),
            'identity_id' => $this->identity_id,
            'password'  => $this->un_hashed_password,
            'api_token'  => $this->api_token,
        ];
    }
}
