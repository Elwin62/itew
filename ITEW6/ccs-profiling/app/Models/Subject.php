<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['curriculum_id', 'code', 'title', 'units', 'semester', 'year_level'];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function syllabus()
    {
        return $this->hasOne(Syllabus::class);
    }
}
