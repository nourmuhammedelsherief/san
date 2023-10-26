<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\SubjectResource;
use App\Http\Resources\Teacher\TeacherRateResource;
use App\Http\Resources\Teacher\TeacherResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'           => $this->id,
            'teacher'      => new TeacherResource($this->rate->teacher),
            'rate'         => new TeacherRateResource($this->rate),
            'points'       => $this->points,
            'subject'      => new SubjectResource($this->subject),
            'created_at'   => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
