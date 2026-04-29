<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['syllabus_id', 'week_number', 'topic_title', 'learning_outcomes', 'materials_link'];

    public function syllabus()
    {
        return $this->belongsTo(Syllabus::class);
    }
}
