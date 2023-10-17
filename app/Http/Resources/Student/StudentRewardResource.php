<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\SubjectResource;
use App\Http\Resources\Teacher\RewardResource;
use App\Http\Resources\Teacher\TeacherResource;
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
            'teacher'      => new TeacherResource($this->reward->teacher),
            'reward' => new RewardResource($this->reward),
            'points'  => $this->points,
            'subject' => new SubjectResource($this->subject),
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
