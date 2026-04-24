<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentInternship extends Model {
    protected $fillable = ['student_id', 'company_name', 'company_address', 'supervisor_name', 'supervisor_contact', 'role', 'duration', 'status', 'completion_date'];
    protected $casts = ['completion_date' => 'date'];
}
