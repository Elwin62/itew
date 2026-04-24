<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentOrganization extends Model {
    protected $fillable = ['student_id', 'name', 'position', 'academic_year', 'start_date', 'end_date', 'status'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];
}
