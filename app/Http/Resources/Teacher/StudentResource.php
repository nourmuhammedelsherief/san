<?php

namespace App\Http\Resources\Teacher;

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
            'name'     => $this->name,
            'gender'   => $this->gender,
            'photo'    => $this->photo == null ? null : asset('/uploads/students/' . $this->photo),
            'birth_date' => $this->birth_date->format('Y-m-d'),
        ];
    }
}
