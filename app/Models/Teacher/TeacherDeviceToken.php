<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherDeviceToken extends Model
{
    use HasFactory;
    protected $table = 'teacher_device_tokens';
    protected $fillable = [
        'teacher_id',
        'device_token',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
}
