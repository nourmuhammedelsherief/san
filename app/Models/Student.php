<?php

namespace App\Models;

use App\Models\Teacher\StudentRate;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $table = 'students';
    protected $fillable = [
        'classroom_id',
        'name',
        'gender',
        'photo',
        'birth_date',
        'points',
    ];

    protected $casts = ['birth_date' => 'datetime'];

    public function classroom()
    {
        return $this->belongsTo(TeacherClassRoom::class , 'classroom_id');
    }
    public function rates()
    {
        return $this->hasMany(StudentRate::class , 'student_id');
    }
}