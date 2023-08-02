<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherClassRoom extends Model
{
    use HasFactory;
    protected $table = 'teacher_class_rooms';
    protected $fillable = [
        'name',
        'teacher_id',
        'main_teacher_id',
        'pulled',
        'archive',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
    public function subjects()
    {
        return $this->hasMany(ClassRoomSubject::class , 'class_room_id');
    }
}
