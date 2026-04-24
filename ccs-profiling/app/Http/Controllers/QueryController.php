<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentSkill;

class QueryController extends Controller
{
    public function basketball()
    {
        $students = Student::whereHas('skills', fn($q) => $q->where('name', 'Basketball'))
            ->with(['skills' => fn($q) => $q->where('name', 'Basketball')])
            ->orderBy('full_name')
            ->paginate(25);

        $total = Student::whereHas('skills', fn($q) => $q->where('name', 'Basketball'))->count();
        $skill = 'Basketball';
        return view('queries.skill', compact('students', 'total', 'skill'));
    }

    public function programming()
    {
        $students = Student::whereHas('skills', fn($q) => $q->where('name', 'Programming'))
            ->with(['skills' => fn($q) => $q->where('name', 'Programming')])
            ->orderBy('full_name')
            ->paginate(25);

        $total = Student::whereHas('skills', fn($q) => $q->where('name', 'Programming'))->count();
        $skill = 'Programming';
        return view('queries.skill', compact('students', 'total', 'skill'));
    }

    public function custom(string $skillName)
    {
        $students = Student::whereHas('skills', fn($q) => $q->where('name', 'like', "%{$skillName}%"))
            ->with('skills')
            ->orderBy('full_name')
            ->paginate(25);

        $total = $students->total();
        $skill = $skillName;
        return view('queries.skill', compact('students', 'total', 'skill'));
    }
}
