<?php

namespace App\Models\Teacher;

use App\Models\School\SchoolRate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherRate extends Model
{
    use HasFactory;
    protected $table = 'teacher_rates';
    protected $fillable = [
        'teacher_id',
        'rate_name',
        'points',
        'type',
        'school_rate_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
    public function students()
    {
        return $this->hasMany(StudentRate::class , 'rate_id');
    }
    public function school_rate()
    {
        return $this->belongsTo(SchoolRate::class , 'school_rate_id');
    }
}
