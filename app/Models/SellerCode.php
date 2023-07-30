<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerCode extends Model
{
    use HasFactory;
    protected  $table = 'seller_codes';
    protected $fillable = [
        'code',
        'type',
        'status',
        'discount',
        'start_at',
        'end_at',
    ];
    protected $casts = ['start_at' => 'datetime' , 'end_at' => 'datetime'];
}
