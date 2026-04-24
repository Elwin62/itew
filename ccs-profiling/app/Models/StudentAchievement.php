<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model {
    protected $fillable = ['student_id', 'type', 'level', 'date_received', 'proof_url'];
    protected $casts = ['date_received' => 'date'];
}
