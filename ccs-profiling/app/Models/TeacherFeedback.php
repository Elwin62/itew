<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeacherFeedback extends Model {
    protected $table = 'teacher_feedbacks';
    protected $fillable = ['student_id', 'faculty_id', 'faculty_name', 'subject_code', 'type', 'comment'];
    public function student() { return $this->belongsTo(Student::class); }
}
