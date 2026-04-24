<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentMedical extends Model {
    protected $table = 'student_medical';
    protected $fillable = ['student_id', 'blood_type', 'allergies', 'conditions', 'emergency_contact', 'emergency_number'];
    protected $casts = ['allergies' => 'array', 'conditions' => 'array'];
}
