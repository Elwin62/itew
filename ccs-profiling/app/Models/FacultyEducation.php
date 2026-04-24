<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FacultyEducation extends Model {
    protected $table = 'faculty_education';
    protected $fillable = ['faculty_id', 'degree'];
}
