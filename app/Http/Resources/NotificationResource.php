<?php

namespace App\Http\Resources;

use App\Http\Resources\Father\FatherResource;
use App\Http\Resources\Teacher\StudentResource;
use App\Http\Resources\Teacher\TeacherResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'teacher_id'    => $this->teacher_id != null ? new TeacherResource($this->teacher) : null,
            'father_id'     => $this->father_id != null ? new FatherResource($this->father) : null,
            'student_id'    => $this->student_id != null ? new StudentResource($this->student) : null,
            'type'          => $this->type,
            'title'         => $this->title,
            'message'       => $this->message,
            'photo'         => $this->photo != null ? asset('/uploads/notifications/' . $this->photo) : null,
            'created_at'    => $this->created_at->format('Y-m-d')
        ];
    }
}
