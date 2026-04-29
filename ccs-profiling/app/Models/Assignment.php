<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model {
    protected $fillable = ['title', 'description', 'subject_code', 'section', 'faculty_id', 'type', 'due_date', 'total_points'];
    protected $casts = ['due_date' => 'date'];
    public function submissions() { return $this->hasMany(AssignmentSubmission::class); }
}
