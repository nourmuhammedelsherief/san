<?php

namespace App\Models\Teacher;

use App\Models\School\City;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    protected $guard = 'teacher';
    protected $table = 'teachers';


    protected $fillable = [
        'city_id',
        'name',
        'email',
        'integration_code',
        'photo',
        'school',
        'type',
        'password',
        'api_token',
        'phone_number',
        'active',
        'verification_code',
        'invitation_code',
        'balance',
        'whatsapp',

    ];
    protected $hidden = [
        'password',
    ];

    public function teacher_classrooms()
    {
        return $this->hasMany(TeacherClassRoom::class , 'teacher_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class , 'city_id');
    }
    public function subscription()
    {
        return $this->hasOne(TeacherSubscription::class , 'teacher_id');
    }
    public function subjects()
    {
        return $this->hasMany(TeacherSubject::class , 'teacher_id');
    }
    public function device_token()
    {
        return $this->hasMany(TeacherDeviceToken::class , 'teacher_id');
    }
}
