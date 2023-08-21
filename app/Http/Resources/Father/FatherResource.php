<?php

namespace App\Http\Resources\Father;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'photo'    => $this->photo == null ? null : asset('/uploads/fathers/' . $this->photo),
            'api_token' => $this->api_token,
        ];
    }
}
