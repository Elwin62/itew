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

        return view('dashboard.index', compact('stats', 'programDist', 'recentActivities', 'upcomingEvents'));
    }

    // ── Student Dashboard ─────────────────────────────────────────
    private function studentDashboard()
    {
        $user    = auth()->user();
        // Try to find the linked student record by email
        $student = Student::where('email', $user->email)->first();

        $upcomingEvents = Event::where('status', 'Upcoming')->orderBy('date')->limit(5)->get();
        $schedules      = Schedule::limit(5)->get();

        return view('dashboard.student', compact('user', 'student', 'upcomingEvents', 'schedules'));
    }

    // ── Faculty Dashboard ─────────────────────────────────────────
    private function facultyDashboard()
    {
        $user    = auth()->user();
        $faculty = \App\Models\Faculty::where('email', $user->email)
            ->with(['skills', 'education', 'subjects'])
            ->first();

        // Schedules – if linked, show theirs; otherwise show sample data
        $schedules = $faculty
            ? Schedule::where('faculty_id', $faculty->faculty_id)
                ->orderByRaw("FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
                ->orderBy('start_time')->get()
            : Schedule::orderByRaw("FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
                ->orderBy('start_time')->limit(8)->get();

        // Today's classes
        $today         = now()->format('l');
        $todaySchedule = $schedules->where('day', $today)->values();

        // Sections taught → student count per section
        $sectionCounts = $schedules->groupBy('section')->map(function ($rows) {
            $section = $rows->first()->section;
            return [
                'section'       => $section,
                'subject'       => $rows->first()->subject_name,
                'subject_code'  => $rows->first()->subject_code,
                'room'          => $rows->first()->room,
                'student_count' => Student::where('section', $section)->count(),
            ];
        })->values()->unique('section');

        $upcomingEvents = Event::where('status', 'Upcoming')->orderBy('date')->limit(4)->get();
        $totalStudents  = Student::count();
        $totalSections  = $sectionCounts->count();

        return view('dashboard.faculty', compact(
            'user', 'faculty', 'upcomingEvents', 'schedules',
            'todaySchedule', 'sectionCounts', 'totalStudents', 'totalSections'
        ));
    }
}
