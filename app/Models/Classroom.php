<?php

namespace App\Models;

use App\Models\Teacher\TeacherClassRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classrooms';
    protected $fillable = [
        'name'
    ];

    public function teachers()
    {
        return $this->hasMany(TeacherClassRoom::class  , 'classroom_id');
    }
    public function students()
    {
        return $this->hasMany(Student::class  , 'classroom_id');
    }
}
