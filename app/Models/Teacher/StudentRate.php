<?php

namespace App\Models\Teacher;

use App\Models\Student;
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
    ];

    public function student()
    {
        return $this->belongsTo(Student::class , 'student_id');
    }
    public function rate()
    {
        return $this->belongsTo(TeacherRate::class , 'rate_id');
    }
}
