<?php

namespace App\Models\School;

use App\Models\SellerCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSubscription extends Model
{
    use HasFactory;
    protected $table = 'school_subscriptions';
    protected $fillable = [
        'school_id',
        'seller_code_id',
        'paid_amount',
        'discount',
        'status',
        'transfer_photo',
        'invoice_id',
        'payment_type',
        'payment',
        'paid_at',
        'end_at',
    ];

    protected $casts = ['paid_at' => 'datetime','end_at' => 'datetime'];

    public function school()
    {
        return $this->belongsTo(School::class , 'school_id');
    }
    public function seller_code()
    {
        return $this->belongsTo(SellerCode::class , 'seller_code_id');
    }
}
