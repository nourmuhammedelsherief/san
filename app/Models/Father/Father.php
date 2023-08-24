<?php

namespace App\Models\Father;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Father extends Authenticatable
{
    use HasFactory;
    protected $guard = 'father';
    protected $table = 'fathers';
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'verification_code',
        'api_token',
        'email_verified_at',
    ];
    protected $hidden = ['password'];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function device_token()
    {
        return $this->hasMany(FatherDeviceToken::class , 'father_id');
    }
    public function children()
    {
        return $this->hasMany(FatherChild::class , 'father_id');
    }
}
