<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentAcademicRecord extends Model {
    protected $fillable = ['student_id', 'academic_year', 'semester', 'gpa', 'standing', 'units_enrolled', 'units_passed'];
    public function grades(): HasMany { return $this->hasMany(SubjectGrade::class, 'academic_record_id'); }
}
