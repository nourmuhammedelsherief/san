<?php

namespace App\Models\Teacher;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoomSubject extends Model
{
    use HasFactory;
    protected $table = 'class_room_subjects';
    protected $fillable = [
        'class_room_id',
        'subject_id'
    ];

    public function class_room()
    {
        return $this->belongsTo(TeacherClassRoom::class , 'class_room_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class , 'subject_id');
    }
}
