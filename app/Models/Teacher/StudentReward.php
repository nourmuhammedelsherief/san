<?php

namespace App\Models\Teacher;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReward extends Model
{
    use HasFactory;
    protected $table = 'student_rewards';
    protected $fillable = [
        'student_id',
        'reward_id',
        'points',
        'subject_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class , 'student_id');
    }
    public function reward()
    {
        return $this->belongsTo(Reward::class , 'reward_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class , 'subject_id');
    }

}
