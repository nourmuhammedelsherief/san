<?php

namespace App\Http\Resources\Teacher;

use App\Http\Resources\SubjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'  => $this->id,
            'name' => $this->name,
            'teacher_id' => $this->teacher_id,
            'subjects' => SubjectResource::collection($this->subjects),
            'archive' => $this->archive,
        ];
    }
}
