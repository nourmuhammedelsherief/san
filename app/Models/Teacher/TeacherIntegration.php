<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherIntegration extends Model
{
    use HasFactory;
    protected $table = 'teacher_integrations';
    protected $fillable = [
        'master_teacher_id',
        'teacher_id',
        'status',
    ];

    public function master()
    {
        return $this->belongsTo(Teacher::class , 'master_teacher_id');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class , 'teacher_id');
    }
}



/// التحكم بنوع الدفع بالتطبيق من لوحة الادارة
/// تعديل اتصل بنا
