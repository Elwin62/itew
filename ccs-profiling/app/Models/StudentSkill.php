<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model {
    protected $fillable = ['student_id', 'name', 'category', 'proficiency'];
}
