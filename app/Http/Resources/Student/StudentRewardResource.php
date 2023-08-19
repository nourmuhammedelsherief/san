<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\SubjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentRewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'teacher_id' => $this->teacher_id,
            'name'    => $this->name,
            'points'  => $this->points,
            'subject' => new SubjectResource($this->subject),
            'photo'   => $this->photo == null ? null : asset('/uploads/rewards/' . $this->photo),
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
