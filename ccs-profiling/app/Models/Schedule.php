<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model {
    protected $fillable = ['subject_code', 'subject_name', 'room', 'day', 'start_time', 'end_time', 'faculty_id', 'section', 'type'];
}
