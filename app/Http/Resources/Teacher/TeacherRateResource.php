<?php

namespace App\Http\Resources\Teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->rate)
        {
            return [
                'id'           => $this->rate->id,
                'teacher_id'   => $this->rate->teacher_id,
                'rate_name'    => $this->rate->rate_name,
                'points'       => $this->rate->points,
                'type'         => $this->rate->type,
                'created_at'   => $this->rate->created_at->format('Y-m-d H:i:s')
            ];
        }else{
            return [
                'id'           => $this->id,
                'teacher_id'   => $this->teacher_id,
                'rate_name'    => $this->rate_name,
                'points'       => $this->points,
                'type'         => $this->type,
                'created_at'   => $this->created_at->format('Y-m-d H:i:s')
            ];
        }
    }
}
