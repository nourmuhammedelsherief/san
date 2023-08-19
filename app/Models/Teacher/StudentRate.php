<?php

namespace App\Models\Teacher;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRate extends Model
{
    use HasFactory;
    protected $table = 'student_rates';
    protected $fillable = [
        'rate_id',
        'student_id',
        'points',
        'subject_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class , 'student_id');
    }
    public function rate()
    {
        return $this->belongsTo(TeacherRate::class , 'rate_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class , 'subject_id');
    }
}
