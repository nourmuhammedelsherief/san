<?php

namespace App\Models\Father;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FatherChild extends Model
{
    use HasFactory;
    protected $table = 'father_children';
    protected $fillable = [
        'father_id',
        'student_id'
    ];

    public function father()
    {
        return $this->belongsTo(Father::class , 'father_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class , 'student_id');
    }
}
