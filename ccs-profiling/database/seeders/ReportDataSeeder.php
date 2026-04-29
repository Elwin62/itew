<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\Faculty;

class ReportDataSeeder extends Seeder
{
    private $feedbackComments = [
        'Commendation' => [
            'Excellent performance in class activities. Keep up the great work!',
            'Outstanding participation during group discussions.',
            'Consistently demonstrates leadership and academic excellence.',
            'Shows remarkable improvement in understanding complex topics.',
            'One of the most dedicated students in this class.',
            'Impressive analytical skills demonstrated in recent project.',
            'Always prepared and contributes meaningfully to class discussions.',
        ],
        'Warning' => [
            'Attendance has been declining. Please ensure regular class participation.',
            'Missing several assignments. Please submit all pending work immediately.',
            'Academic performance is below expectations. Consider seeking tutoring.',
            'Frequent tardiness is affecting class participation. Please improve.',
            'Last exam score was significantly below passing. Extra effort needed.',
            'Please focus more during lectures and reduce distractions.',
        ],
        'General' => [
            'Good progress this semester. Continue working hard.',
            'Shows potential but needs to be more consistent with submissions.',
            'Active in class but needs improvement in written assessments.',
            'Adapting well to the course material. Keep it up!',
            'Consider joining study groups for better performance.',
            'Satisfactory performance. Room for improvement in lab work.',
            'Good teamwork skills observed during group projects.',
        ],
    ];

    private $assignmentTitles = [
        'Assignment' => [
            'Research Paper: {:subject}',
            'Case Study Analysis',
            'Problem Set #{:num}',
            'Written Report: {:subject} Concepts',
            'Group Project Proposal',
            'Literature Review',
            'Technical Documentation',
        ],
        'Activity' => [
            'Hands-on Lab Exercise #{:num}',
            'In-class Workshop',
            'Peer Review Activity',
            'Collaborative Problem Solving',
            'Practical Application Exercise',
            'Interactive Simulation',
        ],
        'Quiz' => [
            'Quiz #{:num}: {:subject}',
            'Pop Quiz - {:subject}',
            'Weekly Assessment #{:num}',
            'Chapter Review Quiz',
        ],
        'Exam' => [
            'Midterm Examination',
            'Final Examination',
            'Practical Exam',
            'Comprehensive Assessment',
        ],
    ];

    public function run(): void
    {
        $students  = Student::all();
        $schedules = Schedule::all();
        $faculty   = Faculty::all();

        if ($students->isEmpty() || $schedules->isEmpty()) {
            $this->command->warn('No students or schedules found. Run main seeder first.');
            return;
        }

        // Group schedules by section for lookup
        $schedulesBySection = $schedules->groupBy('section');

        $this->command->info('Seeding attendance records...');
        $this->seedAttendance($students, $schedulesBySection);

        $this->command->info('Seeding assignments & submissions...');
        $this->seedAssignments($students, $schedules, $schedulesBySection);

        $this->command->info('Seeding teacher feedbacks...');
        $this->seedFeedback($students, $schedules, $faculty);

        $this->command->info('Report data seeded successfully!');
    }

    private function seedAttendance($students, $schedulesBySection)
    {
        $attendanceData = [];
        $batchSize = 500;

        // Generate attendance for last 3 months (approx 60 school days)
        $schoolDays = [];
        $date = now()->subMonths(3);
        while ($date->lte(now())) {
            if ($date->isWeekday()) {
                $schoolDays[] = $date->format('Y-m-d');
            }
            $date->addDay();
        }

        // Sample ~30 school days for performance
        $sampledDays = collect($schoolDays);
        if ($sampledDays->count() > 30) {
            $sampledDays = $sampledDays->random(30)->sort()->values();
        }

        foreach ($students as $student) {
            // Get subjects for this student's section
            $sectionSchedules = $schedulesBySection[$student->section] ?? collect();
            $subjects = $sectionSchedules->pluck('subject_code')->unique()->take(3);

            foreach ($subjects as $subjectCode) {
                foreach ($sampledDays as $day) {
                    // 85% present, 8% late, 7% absent
                    $rand = rand(1, 100);
                    $status = $rand <= 85 ? 'Present' : ($rand <= 93 ? 'Late' : 'Absent');

                    $attendanceData[] = [
                        'student_id'   => $student->id,
                        'subject_code' => $subjectCode,
                        'section'      => $student->section,
                        'date'         => $day,
                        'status'       => $status,
                        'remarks'      => $status === 'Absent' ? collect(['Sick', 'Personal', 'Family emergency', 'No reason given', null])->random() : null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    if (count($attendanceData) >= $batchSize) {
                        DB::table('attendances')->insert($attendanceData);
                        $attendanceData = [];
                    }
                }
            }
        }

        if (!empty($attendanceData)) {
            DB::table('attendances')->insert($attendanceData);
        }
    }

    private function seedAssignments($students, $schedules, $schedulesBySection)
    {
        $assignments = [];
        $submissions = [];
        $batchSize = 500;
        $assignmentId = 1;

        // Create assignments per unique subject+section combo
        $subjectSections = $schedules->unique(fn($s) => $s->subject_code . '-' . $s->section);

        foreach ($subjectSections as $sched) {
            $types = ['Assignment', 'Assignment', 'Activity', 'Activity', 'Quiz', 'Quiz', 'Quiz', 'Exam'];

            foreach ($types as $i => $type) {
                $titles = $this->assignmentTitles[$type];
                $title = str_replace(['{:subject}', '{:num}'], [$sched->subject_name, $i + 1], $titles[array_rand($titles)]);

                $dueDate = now()->subDays(rand(1, 80));
                $totalPoints = match($type) {
                    'Quiz' => collect([20, 30, 50])->random(),
                    'Exam' => collect([100, 150, 200])->random(),
                    'Activity' => collect([20, 30, 40, 50])->random(),
                    default => collect([50, 80, 100])->random(),
                };

                $assignments[] = [
                    'id'           => $assignmentId,
                    'title'        => $title,
                    'description'  => "Complete this {$type} for {$sched->subject_name}.",
                    'subject_code' => $sched->subject_code,
                    'section'      => $sched->section,
                    'faculty_id'   => $sched->faculty_id,
                    'type'         => $type,
                    'due_date'     => $dueDate->format('Y-m-d'),
                    'total_points' => $totalPoints,
                    'created_at'   => $dueDate->subDays(7),
                    'updated_at'   => $dueDate->subDays(7),
                ];

                // Create submissions for students in this section
                $sectionStudents = $students->where('section', $sched->section);
                foreach ($sectionStudents as $student) {
                    $rand = rand(1, 100);
                    // 75% submitted on time, 12% late, 13% missing
                    if ($rand <= 75) {
                        $status = 'Submitted';
                        $submittedAt = $dueDate->copy()->subDays(rand(0, 3));
                        $score = round(($totalPoints * rand(55, 100)) / 100, 2);
                    } elseif ($rand <= 87) {
                        $status = 'Late';
                        $submittedAt = $dueDate->copy()->addDays(rand(1, 5));
                        $score = round(($totalPoints * rand(40, 85)) / 100, 2);
                    } else {
                        $status = 'Missing';
                        $submittedAt = null;
                        $score = 0;
                    }

                    $submissions[] = [
                        'assignment_id' => $assignmentId,
                        'student_id'    => $student->id,
                        'score'         => $score,
                        'submitted_at'  => $submittedAt,
                        'status'        => $status,
                        'remarks'       => null,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    if (count($submissions) >= $batchSize) {
                        DB::table('assignment_submissions')->insert($submissions);
                        $submissions = [];
                    }
                }

                $assignmentId++;
            }
        }

        if (!empty($assignments)) {
            DB::table('assignments')->insert($assignments);
        }
        if (!empty($submissions)) {
            DB::table('assignment_submissions')->insert($submissions);
        }
    }

    private function seedFeedback($students, $schedules, $faculty)
    {
        $feedbackData = [];
        $batchSize = 500;

        // Give ~30% of students at least one feedback
        $selectedStudents = $students->random(min((int)($students->count() * 0.3), $students->count()));

        foreach ($selectedStudents as $student) {
            $numFeedbacks = rand(1, 3);
            $sectionSchedules = $schedules->where('section', $student->section);

            for ($i = 0; $i < $numFeedbacks; $i++) {
                $sched = $sectionSchedules->isNotEmpty() ? $sectionSchedules->random() : null;
                $type = collect(['Commendation', 'Warning', 'General', 'General'])->random();
                $comments = $this->feedbackComments[$type];

                // Try to find the matching faculty
                $fac = $sched ? $faculty->where('faculty_id', $sched->faculty_id)->first() : null;

                $feedbackData[] = [
                    'student_id'   => $student->id,
                    'faculty_id'   => $sched?->faculty_id ?? 'FAC-000',
                    'faculty_name' => $fac?->full_name ?? 'Faculty Member',
                    'subject_code' => $sched?->subject_code ?? 'GEN101',
                    'type'         => $type,
                    'comment'      => $comments[array_rand($comments)],
                    'created_at'   => now()->subDays(rand(1, 60)),
                    'updated_at'   => now(),
                ];

                if (count($feedbackData) >= $batchSize) {
                    DB::table('teacher_feedbacks')->insert($feedbackData);
                    $feedbackData = [];
                }
            }
        }

        if (!empty($feedbackData)) {
            DB::table('teacher_feedbacks')->insert($feedbackData);
        }
    }
}
