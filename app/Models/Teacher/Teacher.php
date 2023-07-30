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

    ];
    protected $hidden = [
        'password',
    ];

    public function city()
    {
        return $this->belongsTo(City::class , 'city_id');
    }
    public function subscription()
    {
        return $this->hasOne(TeacherSubscription::class , 'teacher_id');
    }
}
