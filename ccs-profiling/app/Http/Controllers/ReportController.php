<?php
namespace App\Http\Controllers;

use App\Models\{Student, Faculty, Event, ActivityLog, StudentSkill, Schedule, Attendance, Assignment, AssignmentSubmission, TeacherFeedback};
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // ══════════════════════ ADMIN ══════════════════════
    public function adminReports(Request $request)
    {
        $programFilter = $request->get('program');
        $yearFilter = $request->get('year_level');

        $query = Student::query();
        if ($programFilter) $query->where('academic_program', $programFilter);
        if ($yearFilter) $query->where('year_level', $yearFilter);

        $totalStudents = $query->count();
        $data = [
            'totalStudents'  => $totalStudents,
            'totalFaculty'   => Faculty::count(),
            'enrolled'       => (clone $query)->where('enrollment_status','Enrolled')->count(),
            'scholars'       => (clone $query)->where('is_scholar',true)->count(),
            'totalEvents'    => Event::count(),
            'upcomingEvents' => Event::where('status','Upcoming')->count(),
            'programDist'    => Student::selectRaw('academic_program, count(*) as count')->groupBy('academic_program')->orderByDesc('count')->get(),
            'genderDist'     => (clone $query)->selectRaw('gender, count(*) as count')->groupBy('gender')->get(),
            'yearLevelDist'  => (clone $query)->selectRaw('year_level, count(*) as count')->groupBy('year_level')->orderBy('year_level')->get(),
            'enrollmentDist' => Student::selectRaw('enrollment_status, count(*) as count')->groupBy('enrollment_status')->get(),
            'facultyDeptDist'=> Faculty::selectRaw('department, count(*) as count')->groupBy('department')->orderByDesc('count')->get(),
            'facultyRankDist'=> Faculty::selectRaw('academic_rank, count(*) as count')->groupBy('academic_rank')->orderByDesc('count')->get(),
            'recentActivity' => ActivityLog::latest()->limit(20)->get(),
            'programFilter'  => $programFilter,
            'yearFilter'     => $yearFilter,
            'programs'       => ['BS Information Technology','BS Computer Science','BS Information Systems'],
        ];

        // Attendance stats
        $attTotal = Attendance::count();
        $data['attendanceStats'] = [
            'present' => $attTotal > 0 ? Attendance::where('status','Present')->count() : 0,
            'late'    => $attTotal > 0 ? Attendance::where('status','Late')->count() : 0,
            'absent'  => $attTotal > 0 ? Attendance::where('status','Absent')->count() : 0,
            'total'   => $attTotal,
        ];

        // Assignment stats
        $subTotal = AssignmentSubmission::count();
        $data['assignmentStats'] = [
            'submitted' => AssignmentSubmission::where('status','Submitted')->count(),
            'late'      => AssignmentSubmission::where('status','Late')->count(),
            'missing'   => AssignmentSubmission::where('status','Missing')->count(),
            'total'     => $subTotal,
            'avgScore'  => round(AssignmentSubmission::where('status','!=','Missing')->avg('score') ?? 0, 1),
        ];

        // Top students by GPA
        $data['topStudents'] = \App\Models\StudentAcademicRecord::selectRaw('student_id, AVG(gpa) as avg_gpa')
            ->groupBy('student_id')->orderBy('avg_gpa')->limit(10)
            ->with('student')->get();

        // At-risk: students with >20% absence rate
        $data['atRiskCount'] = \DB::table('attendances')
            ->selectRaw('student_id, SUM(CASE WHEN status="Absent" THEN 1 ELSE 0 END) as absences, COUNT(*) as total')
            ->groupBy('student_id')
            ->havingRaw('absences / total > 0.2')
            ->count();

        return view('reports.admin', $data);
    }

    public function adminDownload(Request $request)
    {
        $type = $request->get('type', 'students');
        if ($type === 'students') {
            $rows = Student::with('skills')->orderBy('full_name')->get();
            $csv = "Student ID,Full Name,Email,Program,Year Level,Section,Status,Scholar,Gender,Skills\n";
            foreach ($rows as $s) {
                $skills = $s->skills->pluck('name')->implode('; ');
                $csv .= "\"{$s->student_id}\",\"{$s->full_name}\",\"{$s->email}\",\"{$s->academic_program}\",{$s->year_level},\"{$s->section}\",\"{$s->enrollment_status}\",".($s->is_scholar?'Yes':'No').",\"{$s->gender}\",\"{$skills}\"\n";
            }
            $fn = 'students_report_'.now()->format('Y-m-d').'.csv';
        } elseif ($type === 'faculty') {
            $rows = Faculty::with('skills')->orderBy('full_name')->get();
            $csv = "Faculty ID,Full Name,Email,Department,Rank,Status,Experience,Gender\n";
            foreach ($rows as $f) {
                $csv .= "\"{$f->faculty_id}\",\"{$f->full_name}\",\"{$f->email}\",\"{$f->department}\",\"{$f->academic_rank}\",\"{$f->employment_status}\",{$f->years_experience},\"{$f->gender}\"\n";
            }
            $fn = 'faculty_report_'.now()->format('Y-m-d').'.csv';
        } elseif ($type === 'attendance') {
            $rows = Attendance::orderBy('date','desc')->limit(5000)->get();
            $csv = "Date,Student ID,Subject,Section,Status,Remarks\n";
            foreach ($rows as $a) {
                $csv .= "\"{$a->date->format('Y-m-d')}\",{$a->student_id},\"{$a->subject_code}\",\"{$a->section}\",\"{$a->status}\",\"{$a->remarks}\"\n";
            }
            $fn = 'attendance_report_'.now()->format('Y-m-d').'.csv';
        } elseif ($type === 'enrollment') {
            $rows = Student::orderBy('academic_program')->orderBy('year_level')->get();
            $csv = "Program,Year,Section,Student ID,Name,Status,Scholar\n";
            foreach ($rows as $s) {
                $csv .= "\"{$s->academic_program}\",{$s->year_level},\"{$s->section}\",\"{$s->student_id}\",\"{$s->full_name}\",\"{$s->enrollment_status}\",".($s->is_scholar?'Yes':'No')."\n";
            }
            $fn = 'enrollment_report_'.now()->format('Y-m-d').'.csv';
        } else {
            $rows = ActivityLog::latest()->limit(500)->get();
            $csv = "Date,User,Action,Target,Module,Status\n";
            foreach ($rows as $l) {
                $csv .= "\"{$l->created_at->format('Y-m-d H:i')}\",\"{$l->user_name}\",\"{$l->action}\",\"{$l->target}\",\"{$l->module}\",\"{$l->status}\"\n";
            }
            $fn = 'activity_logs_'.now()->format('Y-m-d').'.csv';
        }
        return response($csv, 200, ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=\"{$fn}\""]);
    }

    // ══════════════════════ FACULTY ══════════════════════
    public function facultyReports(Request $request)
    {
        $user = auth()->user();
        $faculty = Faculty::where('email', $user->email)->with(['skills','education','subjects'])->first();
        $dayOrder = "CASE day WHEN 'Monday' THEN 1 WHEN 'Tuesday' THEN 2 WHEN 'Wednesday' THEN 3 WHEN 'Thursday' THEN 4 WHEN 'Friday' THEN 5 WHEN 'Saturday' THEN 6 WHEN 'Sunday' THEN 7 ELSE 8 END";

        $schedules = $faculty
            ? Schedule::where('faculty_id', $faculty->faculty_id)->orderByRaw($dayOrder)->orderBy('start_time')->get()
            : collect();

        $sections = $schedules->pluck('section')->unique()->values();
        $subjectCodes = $schedules->pluck('subject_code')->unique()->values();

        $sectionStudentCounts = Student::whereIn('section', $sections)
            ->selectRaw("section, count(*) as count")->groupBy('section')->pluck('count','section');

        // Students in my sections for detailed analytics
        $myStudents = Student::whereIn('section', $sections)->with(['academicRecords.grades'])->get();

        // Grade analytics per section
        $gradeAnalytics = [];
        foreach ($sections as $sec) {
            $secStudents = $myStudents->where('section', $sec);
            $allGpas = $secStudents->flatMap(fn($s) => $s->academicRecords->pluck('gpa'))->filter();
            $gradeAnalytics[$sec] = [
                'avg' => $allGpas->count() ? round($allGpas->avg(), 2) : 0,
                'max' => $allGpas->count() ? round($allGpas->min(), 2) : 0, // lower GPA = better
                'min' => $allGpas->count() ? round($allGpas->max(), 2) : 0,
                'count' => $secStudents->count(),
            ];
        }

        // Attendance summary per section
        $attendanceSummary = [];
        foreach ($subjectCodes as $code) {
            $att = Attendance::where('subject_code', $code)->selectRaw("status, count(*) as count")->groupBy('status')->pluck('count','status');
            $attendanceSummary[$code] = [
                'present' => $att['Present'] ?? 0,
                'late' => $att['Late'] ?? 0,
                'absent' => $att['Absent'] ?? 0,
                'total' => ($att['Present'] ?? 0) + ($att['Late'] ?? 0) + ($att['Absent'] ?? 0),
            ];
        }

        // Assignment tracking
        $assignments = Assignment::whereIn('subject_code', $subjectCodes)->with('submissions')->get();
        $assignmentSummary = $assignments->map(function ($a) {
            $subs = $a->submissions;
            return [
                'title' => $a->title, 'type' => $a->type, 'subject' => $a->subject_code,
                'section' => $a->section, 'due' => $a->due_date->format('M d'),
                'total_points' => $a->total_points,
                'submitted' => $subs->where('status','Submitted')->count(),
                'late' => $subs->where('status','Late')->count(),
                'missing' => $subs->where('status','Missing')->count(),
                'avg_score' => round($subs->where('status','!=','Missing')->avg('score') ?? 0, 1),
            ];
        });

        // At-risk students
        $atRiskStudents = [];
        foreach ($myStudents as $s) {
            $avgGpa = $s->academicRecords->avg('gpa');
            $absences = Attendance::where('student_id', $s->id)->where('status','Absent')->count();
            $totalAtt = Attendance::where('student_id', $s->id)->count();
            $absRate = $totalAtt > 0 ? round(($absences / $totalAtt) * 100) : 0;
            if (($avgGpa && $avgGpa > 2.5) || $absRate > 20) {
                $atRiskStudents[] = ['name' => $s->full_name, 'section' => $s->section, 'gpa' => round($avgGpa ?? 0, 2), 'abs_rate' => $absRate, 'id' => $s->student_id];
            }
        }

        // Grade distribution for pass/fail
        $passCount = $myStudents->filter(fn($s) => $s->academicRecords->count() && $s->academicRecords->avg('gpa') <= 3.0)->count();
        $failCount = $myStudents->filter(fn($s) => $s->academicRecords->count() && $s->academicRecords->avg('gpa') > 3.0)->count();

        $sectionFilter = $request->get('section');
        $subjectFilter = $request->get('subject');

        return view('reports.faculty', compact(
            'user','faculty','schedules','sections','sectionStudentCounts',
            'gradeAnalytics','attendanceSummary','assignmentSummary','atRiskStudents',
            'passCount','failCount','myStudents','subjectCodes',
            'sectionFilter','subjectFilter'
        ));
    }

    public function facultyDownload(Request $request)
    {
        $user = auth()->user();
        $faculty = Faculty::where('email', $user->email)->first();
        $type = $request->get('type', 'schedule');

        if ($type === 'grades') {
            $sections = Schedule::where('faculty_id', $faculty?->faculty_id)->pluck('section')->unique();
            $students = Student::whereIn('section', $sections)->with('academicRecords')->orderBy('section')->orderBy('full_name')->get();
            $csv = "Section,Student ID,Name,Program,GPA,Standing\n";
            foreach ($students as $s) {
                $rec = $s->academicRecords->first();
                $csv .= "\"{$s->section}\",\"{$s->student_id}\",\"{$s->full_name}\",\"{$s->academic_program}\",".($rec?->gpa ?? 'N/A').",\"".($rec?->standing ?? 'N/A')."\"\n";
            }
            $fn = 'student_grades_'.now()->format('Y-m-d').'.csv';
        } elseif ($type === 'attendance') {
            $codes = Schedule::where('faculty_id', $faculty?->faculty_id)->pluck('subject_code')->unique();
            $rows = Attendance::whereIn('subject_code', $codes)->orderBy('date','desc')->limit(3000)->get();
            $csv = "Date,Student ID,Subject,Section,Status\n";
            foreach ($rows as $a) { $csv .= "\"{$a->date->format('Y-m-d')}\",{$a->student_id},\"{$a->subject_code}\",\"{$a->section}\",\"{$a->status}\"\n"; }
            $fn = 'class_attendance_'.now()->format('Y-m-d').'.csv';
        } else {
            $schedules = $faculty ? Schedule::where('faculty_id', $faculty->faculty_id)->get() : collect();
            $csv = "Day,Time,Subject Code,Subject Name,Room,Section,Type\n";
            foreach ($schedules as $s) { $csv .= "\"{$s->day}\",\"{$s->start_time}-{$s->end_time}\",\"{$s->subject_code}\",\"{$s->subject_name}\",\"{$s->room}\",\"{$s->section}\",\"{$s->type}\"\n"; }
            $fn = 'teaching_schedule_'.now()->format('Y-m-d').'.csv';
        }
        return response($csv, 200, ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=\"{$fn}\""]);
    }

    // ══════════════════════ STUDENT ══════════════════════
    public function studentReports(Request $request)
    {
        $user = auth()->user();
        $student = Student::where('email', $user->email)
            ->with(['skills','achievements','organizations','violations','academicRecords.grades','internship','attendances','assignmentSubmissions.assignment','feedbacks'])
            ->first();

        $data = ['user' => $user, 'student' => $student];

        if ($student) {
            // Attendance summary
            $att = $student->attendances;
            $data['attendanceStats'] = [
                'present' => $att->where('status','Present')->count(),
                'late' => $att->where('status','Late')->count(),
                'absent' => $att->where('status','Absent')->count(),
                'total' => $att->count(),
                'rate' => $att->count() > 0 ? round(($att->where('status','Present')->count() / $att->count()) * 100, 1) : 0,
            ];

            // Attendance by subject
            $data['attendanceBySubject'] = $att->groupBy('subject_code')->map(function ($rows, $code) {
                return [
                    'subject' => $code,
                    'present' => $rows->where('status','Present')->count(),
                    'late' => $rows->where('status','Late')->count(),
                    'absent' => $rows->where('status','Absent')->count(),
                    'total' => $rows->count(),
                ];
            })->values();

            // Assignment scores
            $subs = $student->assignmentSubmissions;
            $data['assignmentStats'] = [
                'submitted' => $subs->where('status','Submitted')->count(),
                'late' => $subs->where('status','Late')->count(),
                'missing' => $subs->where('status','Missing')->count(),
                'total' => $subs->count(),
                'avgScore' => round($subs->where('status','!=','Missing')->avg('score') ?? 0, 1),
            ];

            // Assignment details
            $data['assignments'] = $subs->map(fn($s) => [
                'title' => $s->assignment?->title ?? 'Unknown',
                'type' => $s->assignment?->type ?? 'N/A',
                'subject' => $s->assignment?->subject_code ?? 'N/A',
                'score' => $s->score,
                'total' => $s->assignment?->total_points ?? 100,
                'pct' => $s->assignment?->total_points > 0 ? round(($s->score / $s->assignment->total_points) * 100) : 0,
                'status' => $s->status,
                'due' => $s->assignment?->due_date?->format('M d') ?? '',
            ])->sortByDesc('due')->values();

            // GPA trend
            $data['gpaTrend'] = $student->academicRecords->sortBy('id')->map(fn($r) => [
                'label' => $r->academic_year.' '.$r->semester,
                'gpa' => $r->gpa,
            ])->values();

            // Teacher feedback
            $data['feedbacks'] = $student->feedbacks->sortByDesc('created_at')->values();

            // Alerts
            $alerts = collect();
            $latestGpa = $student->academicRecords->first()?->gpa;
            if ($latestGpa && $latestGpa > 2.5) $alerts->push(['type'=>'warning','msg'=>"Your latest GPA ({$latestGpa}) is below 2.5. Consider seeking academic support."]);
            if ($data['attendanceStats']['total'] > 0 && $data['attendanceStats']['rate'] < 80) $alerts->push(['type'=>'danger','msg'=>"Your attendance rate ({$data['attendanceStats']['rate']}%) is below 80%. Excessive absences may affect your standing."]);
            if ($subs->where('status','Missing')->count() > 3) $alerts->push(['type'=>'warning','msg'=>"You have {$subs->where('status','Missing')->count()} missing submissions. Please submit pending work."]);
            if ($latestGpa && $latestGpa <= 1.5) $alerts->push(['type'=>'success','msg'=>"Congratulations! Your GPA of {$latestGpa} qualifies you for the Dean's List."]);
            $data['alerts'] = $alerts;
        }

        return view('reports.student', $data);
    }

    public function studentDownload(Request $request)
    {
        $user = auth()->user();
        $student = Student::where('email', $user->email)->with(['skills','achievements','academicRecords.grades','attendances','assignmentSubmissions.assignment'])->first();
        if (!$student) return redirect()->back()->with('error','No student record found.');

        $type = $request->get('type', 'full');

        if ($type === 'attendance') {
            $csv = "Date,Subject,Section,Status,Remarks\n";
            foreach ($student->attendances->sortByDesc('date') as $a) {
                $csv .= "\"{$a->date->format('Y-m-d')}\",\"{$a->subject_code}\",\"{$a->section}\",\"{$a->status}\",\"{$a->remarks}\"\n";
            }
            $fn = 'my_attendance_'.now()->format('Y-m-d').'.csv';
        } elseif ($type === 'grades') {
            $csv = "Academic Year,Semester,GPA,Standing,Code,Subject,Grade,Units\n";
            foreach ($student->academicRecords as $r) {
                foreach ($r->grades as $g) {
                    $csv .= "\"{$r->academic_year}\",\"{$r->semester}\",{$r->gpa},\"{$r->standing}\",\"{$g->code}\",\"{$g->name}\",{$g->grade},{$g->units}\n";
                }
            }
            $fn = 'my_grades_'.now()->format('Y-m-d').'.csv';
        } else {
            $csv = "ACADEMIC REPORT - {$student->full_name}\nID: {$student->student_id} | Program: {$student->academic_program}\nGenerated: ".now()->format('M d, Y h:i A')."\n\n";
            $csv .= "GRADES\nAY,Semester,GPA,Standing,Units Enrolled,Units Passed\n";
            foreach ($student->academicRecords as $r) { $csv .= "\"{$r->academic_year}\",\"{$r->semester}\",{$r->gpa},\"{$r->standing}\",{$r->units_enrolled},{$r->units_passed}\n"; }
            $csv .= "\nSKILLS\nName,Category,Proficiency\n";
            foreach ($student->skills as $s) { $csv .= "\"{$s->name}\",\"{$s->category}\",\"{$s->proficiency}\"\n"; }
            $csv .= "\nACHIEVEMENTS\nType,Level,Date\n";
            foreach ($student->achievements as $a) { $csv .= "\"{$a->type}\",\"{$a->level}\",\"{$a->date_received}\"\n"; }
            $fn = 'my_full_report_'.now()->format('Y-m-d').'.csv';
        }
        return response($csv, 200, ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=\"{$fn}\""]);
    }
}
