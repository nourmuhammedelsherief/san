<?php

namespace App\Models;

use App\Models\Father\Father;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'teacher_id',
        'father_id',
        'student_id',
        'type',
        'user',
        'title',
        'message',
        'photo',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
    public function father()
    {
        return $this->belongsTo(Father::class , 'father_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class , 'student_id');
    }
}
