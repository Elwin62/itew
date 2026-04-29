<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $fillable = ['program_name', 'effective_year', 'description', 'status'];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
