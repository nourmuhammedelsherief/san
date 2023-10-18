<?php

namespace App\Models;

use App\Models\Father\FatherChild;
use App\Models\Teacher\StudentRate;
use App\Models\Teacher\StudentReward;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;
    protected $guard = 'student';
    protected $table = 'students';
    protected $fillable = [
        'classroom_id',
        'name',
        'gender',
        'photo',
        'birth_date',
        'points',
        'identity_id',
        'password',
        'un_hashed_password',
        'api_token',
        'last_login_at',
        'last_login_ip_address',
    ];

    protected $casts = ['birth_date' => 'datetime' , 'last_login_at' => 'datetime'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class , 'classroom_id');
    }
    public function rates()
    {
        return $this->hasMany(StudentRate::class , 'student_id');
    }
    public function rewards()
    {
        return $this->hasMany(StudentReward::class , 'student_id');
    }
    public function device_token()
    {
        return $this->hasMany(StudentDeviceToken::class , 'student_id');
    }
    public function parents()
    {
        return $this->hasOne(FatherChild::class , 'student_id');
    }
}
