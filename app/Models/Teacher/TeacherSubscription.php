<?php

namespace App\Models\Teacher;

use App\Models\Bank;
use App\Models\SellerCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubscription extends Model
{
    use HasFactory;
    protected $table = 'teacher_subscriptions';
    protected $fillable = [
        'teacher_id',
        'seller_code_id',
        'invitation_code_id',
        'paid_amount',
        'discount',
        'invitation_discount',
        'status',
        'transfer_photo',
        'invoice_id',
        'payment_type',
        'payment',
        'paid_at',
        'end_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
    public function invitation_code()
    {
        return $this->belongsTo(Teacher::class , 'invitation_code_id');
    }
    public function seller_code()
    {
        return $this->belongsTo(SellerCode::class , 'seller_code_id');
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class , 'bank_id');
    }

}
