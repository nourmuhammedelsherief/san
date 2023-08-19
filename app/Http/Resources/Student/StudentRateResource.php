<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\SubjectResource;
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
            'teacher_id'   => $this->teacher_id,
            'rate_name'    => $this->rate_name,
            'points'       => $this->points,
            'type'         => $this->type,
            'subject'      => new SubjectResource($this->subject),
            'created_at'   => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
