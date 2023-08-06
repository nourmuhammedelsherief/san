<?php

namespace App\Http\Resources\Teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'teacher_id'      => $this->teacher_id,
            'seller_code_id'  => $this->seller_code_id,
            'paid_amount'     => $this->paid_amount,
            'discount'        => $this->discount,
            'status'          => $this->status,
            'payment_type'    => $this->payment_type,
            'payment'         => $this->payment,
            'paid_at'         => $this->paid_at == null ? null : $this->paid_at->format('Y-m-d'),
            'end_at'          => $this->end_at == null ? null : $this->end_at->format('Y-m-d'),
            'balance'         => $this->teacher->balance,
        ];
    }
}
