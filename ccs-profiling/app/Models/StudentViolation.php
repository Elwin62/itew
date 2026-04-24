<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentViolation extends Model {
    protected $fillable = ['student_id', 'category', 'sanction', 'status', 'reported_by', 'date_reported'];
    protected $casts = ['date_reported' => 'date'];
}
