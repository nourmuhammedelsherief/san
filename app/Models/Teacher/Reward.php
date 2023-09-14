<?php

namespace App\Models\Teacher;

use App\Models\School\SchoolReward;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;
    protected $table = 'rewards';
    protected $fillable = [
        'teacher_id',
        'name',
        'points',
        'photo',
        'school_reward_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
    public function school_reward()
    {
        return $this->belongsTo(SchoolReward::class , 'school_reward_id');
    }
}
