<?php

namespace App\Http\Resources\Teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->reward != null)
        {
            return [
                'id'   => $this->reward->id,
                'teacher_id' => $this->reward->teacher_id,
                'name'    => $this->reward->name,
                'points'  => $this->reward->points,
                'photo'   => $this->reward->photo == null ? null : asset('/uploads/rewards/' . $this->reward->photo),
            ];
        }else{
            return [
                'id'   => $this->id,
                'teacher_id' => $this->teacher_id,
                'name'    => $this->name,
                'points'  => $this->points,
                'photo'   => $this->photo == null ? null : asset('/uploads/rewards/' . $this->photo),
            ];
        }
    }
}
