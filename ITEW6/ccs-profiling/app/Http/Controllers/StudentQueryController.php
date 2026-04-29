<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\StudentOrganization;
use Illuminate\Http\Request;

class StudentQueryController extends Controller
{
    public function advanced(Request $request)
    {
        $query = Student::with(['skills', 'organizations', 'violations', 'academicRecords']);

        // Academic Filters
        if ($request->filled('program')) {
            $query->where('academic_program', $request->program);
        }
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }
        if ($request->filled('status')) {
            $query->where('enrollment_status', $request->status);
        }

        // Skills Filter
        if ($request->filled('skill')) {
            $query->whereHas('skills', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->skill . '%');
                if ($request->filled('skill_proficiency')) {
                    $q->where('proficiency', $request->skill_proficiency);
                }
            });
        }

        // Organization Filter
        if ($request->filled('organization')) {
            $query->whereHas('organizations', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->organization . '%');
            });
        }

        // Violation Filter
        if ($request->filled('violations_filter')) {
            if ($request->violations_filter === 'none') {
                $query->whereDoesntHave('violations');
            } elseif ($request->violations_filter === 'has') {
                $query->whereHas('violations');
            }
        }

        $students = $query->orderBy('full_name')->paginate(20)->withQueryString();

        $programs = Student::distinct()->pluck('academic_program')->filter()->sort()->values();
        $skillsList = StudentSkill::distinct()->pluck('name')->filter()->sort()->values();
        $orgsList = StudentOrganization::distinct()->pluck('name')->filter()->sort()->values();

        return view('queries.advanced', compact('students', 'programs', 'skillsList', 'orgsList'));
    }
}
