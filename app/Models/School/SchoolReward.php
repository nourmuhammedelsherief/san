<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolReward extends Model
{
    use HasFactory;
    protected $table = 'school_rewards';
    protected $fillable = [
        'school_id',
        'name',
        'photo',
        'points',
    ];
    public function school()
    {
        return $this->belongsTo(School::class , 'school_id');
    }
}
