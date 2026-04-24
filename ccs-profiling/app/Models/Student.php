<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'student_id', 'full_name', 'gender', 'birthdate', 'civil_status', 'nationality',
        'email', 'contact_number', 'address', 'profile_photo', 'academic_program',
        'enrollment_status', 'admission_type', 'academic_year', 'semester',
        'date_enrolled', 'year_level', 'section', 'is_scholar',
    ];

    protected $casts = ['is_scholar' => 'boolean', 'birthdate' => 'date', 'date_enrolled' => 'date'];

    public function skills(): HasMany { return $this->hasMany(StudentSkill::class); }
    public function achievements(): HasMany { return $this->hasMany(StudentAchievement::class); }
    public function organizations(): HasMany { return $this->hasMany(StudentOrganization::class); }
    public function violations(): HasMany { return $this->hasMany(StudentViolation::class); }
    public function academicRecords(): HasMany { return $this->hasMany(StudentAcademicRecord::class); }
    public function internship(): HasOne { return $this->hasOne(StudentInternship::class); }
    public function guardian(): HasOne { return $this->hasOne(StudentGuardian::class); }
    public function medical(): HasOne { return $this->hasOne(StudentMedical::class); }
}
