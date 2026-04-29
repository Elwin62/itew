<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FacultySkill extends Model {
    protected $table = 'faculty_skills';
    protected $fillable = ['faculty_id', 'skill'];
}
