<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'about' => app()->getLocale() == 'ar' ? strip_tags(str_replace('&nbsp;', ' ', $this->about_ar)) : strip_tags(str_replace('&nbsp;', ' ', $this->about_en)),
        ];
    }
}
