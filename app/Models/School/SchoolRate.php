<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRate extends Model
{
    use HasFactory;
    protected $table = 'school_rates';
    protected $fillable = [
        'school_id',
        'rate_name',
        'points',
        'type',
    ];

    public function school()
    {
        return $this->belongsTo(School::class , 'school_id');
    }
}
