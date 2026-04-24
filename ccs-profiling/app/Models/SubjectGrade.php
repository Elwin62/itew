<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SubjectGrade extends Model {
    protected $fillable = ['academic_record_id', 'code', 'name', 'grade', 'units'];
}
