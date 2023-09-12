<?php

namespace App\Models;

use App\Models\School\School;
use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classrooms';
    protected $fillable = [
        'name',
        'school_id',
    ];

    public function teachers()
    {
        return $this->hasMany(TeacherClassRoom::class  , 'classroom_id');
    }
    public function students()
    {
        return $this->hasMany(Student::class  , 'classroom_id');
    }
    public function school()
    {
        return $this->belongsTo(School::class , 'school_id');
    }
}
