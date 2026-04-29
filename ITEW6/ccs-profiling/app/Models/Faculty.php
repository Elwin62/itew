<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model {
    protected $table = 'faculty';
    protected $fillable = [
        'faculty_id', 'full_name', 'gender', 'birthdate', 'civil_status', 'nationality',
        'email', 'contact_number', 'address', 'department', 'profile_photo',
        'years_experience', 'employment_status', 'academic_rank', 'date_hired', 'contract_end_date',
    ];
    protected $casts = ['birthdate' => 'date', 'date_hired' => 'date', 'contract_end_date' => 'date'];

    public function skills(): HasMany { return $this->hasMany(FacultySkill::class); }
    public function education(): HasMany { return $this->hasMany(FacultyEducation::class); }
    public function subjects(): HasMany { return $this->hasMany(FacultySubject::class); }
}
