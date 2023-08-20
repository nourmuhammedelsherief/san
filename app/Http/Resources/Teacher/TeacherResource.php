<?php

namespace App\Http\Resources\Teacher;

use App\Http\Resources\CityResource;
use App\Http\Resources\SubjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->teacher):
            return [
                'id'    => $this->teacher->id,
                'city_id' => new CityResource($this->teacher->city),
                'name'    => $this->teacher->name,
                'active'  => $this->teacher->active,
                'email'   => $this->teacher->email,
                'integration_code' => $this->teacher->integration_code,
                'photo'      => $this->teacher->photo == null ? null : asset('/uploads/teachers/' . $this->teacher->photo),
                'school'   => $this->teacher->school,
                'type'     => $this->teacher->type,
                'api_token' => $this->teacher->api_token,
                'whatsapp'  => $this->teacher->whatsapp,
                'phone_number' => $this->teacher->phone_number,
                'subjects'  => SubjectResource::collection($this->teacher->subjects),
                'subscription' => new SubscriptionResource($this->teacher->subscription),
                'invitation_code' => $this->teacher->invitation_code,
                'balance'   => $this->teacher->balance
            ];
        else:
            return [
                'id'    => $this->id,
                'city_id' => new CityResource($this->city),
                'name'    => $this->name,
                'active'  => $this->active,
                'email'   => $this->email,
                'integration_code' => $this->integration_code,
                'photo'      => $this->photo == null ? null : asset('/uploads/teachers/' . $this->photo),
                'school'   => $this->school,
                'type'     => $this->type,
                'api_token' => $this->api_token,
                'whatsapp'  => $this->whatsapp,
                'phone_number' => $this->phone_number,
                'subjects'  => SubjectResource::collection($this->subjects),
                'subscription' => new SubscriptionResource($this->subscription),
                'invitation_code' => $this->invitation_code,
                'balance'   => $this->balance
            ];
        endif;
    }
}
