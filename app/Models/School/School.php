<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class School extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    protected $guard = 'school';
    protected $table = 'schools';
    protected $fillable = [
        'name',
        'identity_code',
        'city_id',
        'email',
        'password',
        'status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
