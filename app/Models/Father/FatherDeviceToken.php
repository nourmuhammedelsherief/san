<?php

namespace App\Models\Father;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FatherDeviceToken extends Model
{
    use HasFactory;
    protected $table = 'father_device_tokens';
    protected $fillable = [
        'father_id',
        'device_token'
    ];

    public function father()
    {
        return $this->belongsTo(Father::class , 'father_id');
    }
}
