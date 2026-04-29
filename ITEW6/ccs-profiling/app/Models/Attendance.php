<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $fillable = ['student_id', 'subject_code', 'section', 'date', 'status', 'remarks'];
    protected $casts = ['date' => 'date'];
    public function student() { return $this->belongsTo(Student::class); }
}
