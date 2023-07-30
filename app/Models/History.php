<?php

namespace App\Models;

use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $table = 'histories';
    protected $fillable = [
        'teacher_id',
        'amount',
        'discount',
        'type',
        'transfer_photo',
        'invoice_id',
        'payment_type'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
}
