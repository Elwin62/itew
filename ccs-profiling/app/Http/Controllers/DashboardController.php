<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Faculty;
use App\Models\Event;
use App\Models\ActivityLog;
use App\Models\StudentSkill;
use App\Models\Schedule;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;

        // Route to role-specific dashboard view
        return match($role) {
            'Student' => $this->studentDashboard(),
            'Faculty' => $this->facultyDashboard(),
            default   => $this->adminDashboard(),  // Admin
        };
    }

    // ── Admin Dashboard ───────────────────────────────────────────
    private function adminDashboard()
    {
        $stats = [
            'total_students'   => Student::count(),
            'enrolled'         => Student::where('enrollment_status', 'Enrolled')->count(),
            'total_faculty'    => Faculty::count(),
            'scholars'         => Student::where('is_scholar', true)->count(),
            'basketball_count' => StudentSkill::where('name', 'Basketball')->count(),
            'programming_count'=> StudentSkill::where('name', 'Programming')->count(),
        ];

        $programDist      = Student::selectRaw('academic_program, count(*) as count')->groupBy('academic_program')->get();
        $recentActivities = ActivityLog::latest()->limit(10)->get();
        $upcomingEvents   = Event::where('status', 'Upcoming')->orderBy('date')->limit(5)->get();

        // Report data for dashboard summary
        $reportData = [
            'genderDist'    => Student::selectRaw("gender, count(*) as count")->groupBy('gender')->get(),
            'yearLevelDist' => Student::selectRaw("year_level, count(*) as count")->groupBy('year_level')->orderBy('year_level')->get(),
            'scholarCount'  => $stats['scholars'],
            'nonScholarCount' => $stats['total_students'] - $stats['scholars'],
            'facultyDeptDist' => Faculty::selectRaw("department, count(*) as count")->groupBy('department')->get(),
            'enrollmentStatusDist' => Student::selectRaw("enrollment_status, count(*) as count")->groupBy('enrollment_status')->get(),
        ];

        return view('dashboard.index', compact('stats', 'programDist', 'recentActivities', 'upcomingEvents', 'reportData'));
    }

    // ── Student Dashboard ─────────────────────────────────────────
    private function studentDashboard()
    {
        $user    = auth()->user();
        // Try to find the linked student record by email — eager load everything
        $student = Student::where('email', $user->email)
            ->with(['skills', 'achievements', 'organizations', 'academicRecords.grades'])
            ->first();

        $upcomingEvents = Event::where('status', 'Upcoming')->orderBy('date')->limit(5)->get();
        $schedules      = collect();

        // If student has a section, get their schedule
        if ($student && $student->section) {
            $schedules = Schedule::where('section', 'like', '%' . substr($student->section, -2) . '%')
                ->orderByRaw("CASE day
                    WHEN 'Monday' THEN 1
                    WHEN 'Tuesday' THEN 2
                    WHEN 'Wednesday' THEN 3
                    WHEN 'Thursday' THEN 4
                    WHEN 'Friday' THEN 5
                    WHEN 'Saturday' THEN 6
                    WHEN 'Sunday' THEN 7
                    ELSE 8 END")
                ->orderBy('start_time')
                ->get();
        }

        // Academic records for reports
        $academicRecords = $student ? $student->academicRecords : collect();

        return view('dashboard.student', compact('user', 'student', 'upcomingEvents', 'schedules', 'academicRecords'));
    }

    // ── Faculty Dashboard ─────────────────────────────────────────
    private function facultyDashboard()
    {
        $user    = auth()->user();
        $faculty = Faculty::where('email', $user->email)
            ->with(['skills', 'education', 'subjects'])
            ->first();

        // Schedules — cross-DB compatible ordering
        $dayOrder = "CASE day
            WHEN 'Monday' THEN 1
            WHEN 'Tuesday' THEN 2
            WHEN 'Wednesday' THEN 3
            WHEN 'Thursday' THEN 4
            WHEN 'Friday' THEN 5
            WHEN 'Saturday' THEN 6
            WHEN 'Sunday' THEN 7
            ELSE 8 END";

        $schedules = $faculty
            ? Schedule::where('faculty_id', $faculty->faculty_id)
                ->orderByRaw($dayOrder)
                ->orderBy('start_time')->get()
            : Schedule::orderByRaw($dayOrder)
                ->orderBy('start_time')->limit(8)->get();

        // Today's classes
        $today         = now()->format('l');
        $todaySchedule = $schedules->where('day', $today)->values();

        // Pre-compute student counts per section (avoid N+1)
        $allSections = $schedules->pluck('section')->unique()->values();
        $sectionStudentCounts = Student::whereIn('section', $allSections)
            ->selectRaw("section, count(*) as count")
            ->groupBy('section')
            ->pluck('count', 'section');

        // Sections taught → student count per section
        $sectionCounts = $schedules->groupBy('section')->map(function ($rows) use ($sectionStudentCounts) {
            $section = $rows->first()->section;
            return [
                'section'       => $section,
                'subject'       => $rows->first()->subject_name,
                'subject_code'  => $rows->first()->subject_code,
                'room'          => $rows->first()->room,
                'student_count' => $sectionStudentCounts[$section] ?? 0,
            ];
        })->values()->unique('section');

        $upcomingEvents = Event::where('status', 'Upcoming')->orderBy('date')->limit(4)->get();
        $totalStudents  = Student::count();
        $totalSections  = $sectionCounts->count();

        return view('dashboard.faculty', compact(
            'user', 'faculty', 'upcomingEvents', 'schedules',
            'todaySchedule', 'sectionCounts', 'totalStudents', 'totalSections',
            'sectionStudentCounts'
        ));
    }
}
