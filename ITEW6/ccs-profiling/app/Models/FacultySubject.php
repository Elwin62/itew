<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FacultySubject extends Model {
    protected $table = 'faculty_subjects';
    protected $fillable = ['faculty_id', 'subject_code'];
}
