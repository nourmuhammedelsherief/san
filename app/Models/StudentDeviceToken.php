<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDeviceToken extends Model
{
    use HasFactory;
    protected $table = 'student_device_tokens';
    protected $fillable = [
        'student_id',
        'device_token',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class , 'student_id');
    }
}
