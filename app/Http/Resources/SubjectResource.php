<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->subject != null)
        {
            return [
                'id'    => $this->subject->id,
                'name'  => app()->getLocale() == 'ar' ? $this->subject->name_ar : $this->subject->name_en,
            ];
        }else{
            return [
                'id'    => $this->id,
                'name'  => app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en,
            ];
        }
    }
}
