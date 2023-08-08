<?php

namespace App\Http\Resources\Teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'master_teacher_id'      => $this->master_teacher_id,
            'teacher_id'             => $this->teacher_id,
            'status'                 => $this->status,
            'created_at'             => $this->created_at->format('Y-m-d')
        ];

    }
}
