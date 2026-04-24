<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model {
    protected $table = 'student_guardians';
    protected $fillable = ['student_id', 'name', 'relationship', 'contact_number', 'address'];
}
