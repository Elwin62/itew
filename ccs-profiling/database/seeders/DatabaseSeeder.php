<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\StudentAchievement;
use App\Models\StudentOrganization;
use App\Models\StudentViolation;
use App\Models\StudentAcademicRecord;
use App\Models\SubjectGrade;
use App\Models\StudentInternship;
use App\Models\StudentGuardian;
use App\Models\StudentMedical;
use App\Models\Faculty;
use App\Models\FacultySkill;
use App\Models\FacultyEducation;
use App\Models\FacultySubject;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\ActivityLog;

class DatabaseSeeder extends Seeder
{
    // ── Data pools ────────────────────────────────────────────────
    private $firstNames = ['Juan','Maria','Jose','Ana','Pedro','Elena','Miguel','Sofia','Carlos','Isabella',
        'Diego','Valentina','Luis','Camila','Alejandro','Gabriela','Daniel','Natalia','Francisco','Paula',
        'Marco','Bianca','Rafael','Monica','Adrian','Patricia','Kevin','Kristine','Mark','Angela'];
    private $lastNames = ['Dela Cruz','Santos','Reyes','Garcia','Mendoza','Torres','Castillo','Rivera',
        'Ortiz','Lopez','Martinez','Hernandez','Gonzales','Ramos','Cruz','Lim','Tan','Sy','Co','Wang',
        'Bautista','Aquino','Villanueva','Pascual','Domingo','Flores','Santiago','Aguilar','Navarro','Abad'];
    private $programs = ['BS Computer Science','BS Information Technology','BS Electrical Engineering',
        'BS Mechanical Engineering','BS Civil Engineering','BS Accountancy','BS Nursing','BS Education'];
    private $sections = ['A','B','C'];
    private $brgy = ['Sala','Niugan','Pulo','Butong','Mamatay','San Isidro','Pittland','Marinig'];
    private $bloodTypes = ['O+','A+','B+','AB+','O-','A-','B-','AB-'];
    private $allSkills = ['React.js','Python','Java','C++','Networking','Design','Leadership',
        'Public Speaking','Photography','Music','Writing','Mathematics','Data Analysis','UI/UX'];
    private $orgNames = ['PnC IT Society','Google Developer Club','Varsity Team','Debate Club','Red Cross Youth'];
    private $positions = ['President','VP','Secretary','Treasurer','Member','PRO'];
    private $facultyFirstNames = ['Dr. Ricardo','Ms. Sarah','Engr. Michael','Prof. Anna','Mr. David',
        'Dra. Liza','Engr. John','Prof. Elena','Ms. Carla','Dr. Mark','Engr. Leo','Prof. Grace'];
    private $facultyLastNames = ['Gomez','Concepcion','Ross','Lim','Tan','Sy','Wang','Lee','Chen','Park',
        'Santos','Reyes','Garcia','Torres','Rivera'];
    private $departments = ['College of Computing and Engineering','College of Arts and Sciences',
        'College of Business Administration','College of Education','College of Nursing'];
    private $facultySkills = ['Artificial Intelligence','Machine Learning','Data Science','Web Development',
        'Networking','Literature','Mathematics','Accounting','Nursing','Education','Civil Engineering','Mechanical Engineering'];
    private $degrees = ['PhD in Computer Science - UP Diliman','MS Computer Science - DLSU',
        'MA Literature - UST','BS Electrical Engineering - PnC','BS Nursing - PnC','BS Accountancy - PnC',
        'PhD in Education - PUP','MS Data Science - ADMU'];
    private $ranks = ['Instructor I','Instructor II','Assistant Professor I','Assistant Professor II',
        'Associate Professor I','Professor I','Professor II','Professor III'];
    private $subjectNames = ['Data Structures','Algorithms','Programming','Database','Networking',
        'Calculus','Physics','Chemistry','Literature','Economics','Accounting','Biology'];

    private function rand_from(array $arr) { return $arr[array_rand($arr)]; }

    private function rand_date(string $from, string $to): string {
        return date('Y-m-d', rand(strtotime($from), strtotime($to)));
    }

    private function rand_id(): string {
        return '202' . rand(1,4) . '-' . str_pad(rand(1000,9999), 5, '0', STR_PAD_LEFT);
    }

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StudentSeeder::class,
            FacultySeeder::class,
            EventSeeder::class,
            ScheduleSeeder::class,
        ]);
    }
}


class UserSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $users = [
            ['name' => 'System Administrator', 'email' => 'admin@pnc.edu.ph', 'role' => 'Admin',
             'profile_photo' => 'https://ui-avatars.com/api/?name=Admin&background=f97316&color=fff&size=200'],
            ['name' => 'Juan Dela Cruz', 'email' => 'student@pnc.edu.ph', 'role' => 'Student',
             'student_id' => '2021-00123', 'contact_number' => '09123456789',
             'address' => 'Brgy. Sala, Cabuyao City, Laguna', 'gender' => 'Male',
             'birthdate' => '2002-05-15', 'civil_status' => 'Single', 'nationality' => 'Filipino',
             'academic_program' => 'BS Computer Science', 'year_level' => 3,
             'profile_photo' => 'https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=3b82f6&color=fff&size=200'],
            ['name' => 'Dr. Ricardo Gomez', 'email' => 'faculty@pnc.edu.ph', 'role' => 'Faculty',
             'faculty_id' => 'PROF-2010-001', 'contact_number' => '09556667777',
             'address' => 'Cabuyao City, Laguna', 'gender' => 'Male',
             'birthdate' => '1975-11-10', 'civil_status' => 'Married', 'nationality' => 'Filipino',
             'profile_photo' => 'https://ui-avatars.com/api/?name=Ricardo+Gomez&background=8b5cf6&color=fff&size=200'],
        ];

        foreach ($users as $u) {
            User::create(array_merge($u, ['password' => Hash::make('password123'), 'email_verified_at' => now()]));
        }
    }
}


class StudentSeeder extends Seeder
{
    private $firstNames = ['Juan','Maria','Jose','Ana','Pedro','Elena','Miguel','Sofia','Carlos','Isabella',
        'Diego','Valentina','Luis','Camila','Alejandro','Gabriela','Daniel','Natalia','Francisco','Paula',
        'Marco','Bianca','Rafael','Monica','Adrian','Patricia','Kevin','Kristine','Mark','Angela'];
    private $lastNames = ['Dela Cruz','Santos','Reyes','Garcia','Mendoza','Torres','Castillo','Rivera',
        'Ortiz','Lopez','Martinez','Hernandez','Gonzales','Ramos','Cruz','Lim','Tan','Sy','Co','Wang',
        'Bautista','Aquino','Villanueva','Pascual','Domingo','Flores','Santiago','Aguilar','Navarro','Abad'];
    private $programs = ['BS Computer Science','BS Information Technology','BS Electrical Engineering',
        'BS Mechanical Engineering','BS Civil Engineering','BS Accountancy','BS Nursing','BS Education'];
    private $brgy = ['Sala','Niugan','Pulo','Butong','Mamatay','San Isidro','Pittland','Marinig'];
    private $bloodTypes = ['O+','A+','B+','AB+','O-','A-','B-','AB-'];
    private $allSkills = ['React.js','Python','Java','C++','Networking','Design','Leadership',
        'Public Speaking','Photography','Music','Writing','Mathematics','Data Analysis','UI/UX'];
    private $orgNames = ['PnC IT Society','Google Developer Club','Varsity Team','Debate Club','Red Cross Youth'];
    private $positions = ['President','VP','Secretary','Treasurer','Member','PRO'];
    private $sections = ['A','B','C'];
    private $subjectNames = ['Data Structures','Algorithms','Programming','Database','Networking',
        'Calculus','Physics','Chemistry','Literature','Economics','Accounting','Biology'];

    private function r(array $arr) { return $arr[array_rand($arr)]; }
    private function rd(string $from, string $to): string {
        return date('Y-m-d', rand(strtotime($from), strtotime($to)));
    }

    public function run(): void
    {
        Student::query()->delete();

        $total = 800;
        $basketballTarget = 250;
        $programmingTarget = 300;
        $basketballCount = 0;
        $programmingCount = 0;
        $usedEmails = [];

        for ($i = 0; $i < $total; $i++) {
            $firstName = $this->r($this->firstNames);
            $lastName = $this->r($this->lastNames);
            $fullName = "$firstName $lastName";
            $yearLevel = rand(1, 4);
            $emailBase = strtolower(str_replace(' ', '.', $fullName));
            $email = $emailBase . rand(100,999) . '@pnc.edu.ph';
            while (in_array($email, $usedEmails)) {
                $email = $emailBase . rand(100,999) . '@pnc.edu.ph';
            }
            $usedEmails[] = $email;

            $studentId = '202' . rand(1,4) . '-' . str_pad($i + 1001, 5, '0', STR_PAD_LEFT);

            $student = Student::create([
                'student_id'        => $studentId,
                'full_name'         => $fullName,
                'gender'            => rand(0,1) ? 'Male' : 'Female',
                'birthdate'         => $this->rd('2000-01-01', '2005-12-31'),
                'civil_status'      => rand(0,9) > 1 ? 'Single' : 'Married',
                'nationality'       => 'Filipino',
                'email'             => $email,
                'contact_number'    => '09' . rand(100000000, 999999999),
                'address'           => 'Brgy. ' . $this->r($this->brgy) . ', Cabuyao City, Laguna',
                'profile_photo'     => 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=' . dechex(rand(0x100000, 0xFFFFFF)) . '&color=fff&size=200',
                'academic_program'  => $this->r($this->programs),
                'enrollment_status' => $this->r(['Enrolled','Enrolled','Enrolled','Not Enrolled','Graduated']),
                'admission_type'    => $this->r(['Regular','Regular','Regular','Transferee','Irregular']),
                'academic_year'     => '2023-2024',
                'semester'          => rand(0,1) ? '1st Semester' : '2nd Semester',
                'date_enrolled'     => $this->rd('2024-01-01', '2024-03-01'),
                'year_level'        => $yearLevel,
                'section'           => rand(1,10) . $this->r($this->sections),
                'is_scholar'        => rand(0,1),
            ]);

            // Skills — ensure Basketball and Programming quotas
            $skills = [];
            $needBasketball = $basketballCount < $basketballTarget && rand(0,3) < 2;
            $needProgramming = $programmingCount < $programmingTarget && rand(0,3) < 2;

            if ($needBasketball) {
                $skills[] = ['name' => 'Basketball', 'category' => 'Sports', 'proficiency' => $this->r(['Beginner','Intermediate','Advanced'])];
                $basketballCount++;
            }
            if ($needProgramming) {
                $skills[] = ['name' => 'Programming', 'category' => 'Programming', 'proficiency' => $this->r(['Beginner','Intermediate','Advanced'])];
                $programmingCount++;
            }
            // If near end and haven't met quota, force-add
            if ($i >= $total - ($basketballTarget - $basketballCount) - 5 && $basketballCount < $basketballTarget) {
                if (!collect($skills)->where('name','Basketball')->count()) {
                    $skills[] = ['name' => 'Basketball', 'category' => 'Sports', 'proficiency' => 'Intermediate'];
                    $basketballCount++;
                }
            }
            if ($i >= $total - ($programmingTarget - $programmingCount) - 5 && $programmingCount < $programmingTarget) {
                if (!collect($skills)->where('name','Programming')->count()) {
                    $skills[] = ['name' => 'Programming', 'category' => 'Programming', 'proficiency' => 'Intermediate'];
                    $programmingCount++;
                }
            }

            // 2-4 extra random skills
            $extraCount = rand(2, 4);
            $usedNames = array_column($skills, 'name');
            $available = array_values(array_diff($this->allSkills, $usedNames));
            shuffle($available);
            foreach (array_slice($available, 0, $extraCount) as $skillName) {
                $skills[] = ['name' => $skillName, 'category' => $this->r(['Programming','Networking','Design','Soft Skill']), 'proficiency' => $this->r(['Beginner','Intermediate','Advanced'])];
            }

            foreach ($skills as $s) {
                StudentSkill::create(array_merge(['student_id' => $student->id], $s));
            }

            // Achievement (30% chance)
            if (rand(0,9) < 3) {
                StudentAchievement::create([
                    'student_id'    => $student->id,
                    'type'          => $this->r(["Dean's Lister", 'Academic Award', 'Best in Thesis', 'Sports Award']),
                    'level'         => $this->r(['School', 'Regional', 'National']),
                    'date_received' => $this->rd('2023-01-01', '2024-03-01'),
                ]);
            }

            // Organization (20% chance)
            if (rand(0,9) < 2) {
                StudentOrganization::create([
                    'student_id'    => $student->id,
                    'name'          => $this->r($this->orgNames),
                    'position'      => $this->r($this->positions),
                    'academic_year' => '2023-2024',
                    'start_date'    => $this->rd('2023-06-01', '2023-12-01'),
                    'status'        => 'Active',
                ]);
            }

            // Violation (5% chance)
            if (rand(0,19) === 0) {
                StudentViolation::create([
                    'student_id'    => $student->id,
                    'category'      => 'Minor',
                    'sanction'      => 'Warning',
                    'status'        => 'Resolved',
                    'reported_by'   => 'Security',
                    'date_reported' => $this->rd('2023-09-01', '2024-02-01'),
                ]);
            }

            // Academic Record
            $record = StudentAcademicRecord::create([
                'student_id'      => $student->id,
                'academic_year'   => '2023-2024',
                'semester'        => '1st Semester',
                'gpa'             => round(1.0 + (rand(0,200)/100), 2),
                'standing'        => $this->r(['Excellent','Very Good','Good']),
                'units_enrolled'  => 18 + rand(0,9),
                'units_passed'    => 18 + rand(0,9),
            ]);

            $subjectCount = rand(3, 6);
            $usedCodes = [];
            for ($j = 0; $j < $subjectCount; $j++) {
                $code = 'CS' . rand(100, 499);
                while (in_array($code, $usedCodes)) { $code = 'CS' . rand(100, 499); }
                $usedCodes[] = $code;
                SubjectGrade::create([
                    'academic_record_id' => $record->id,
                    'code'   => $code,
                    'name'   => $this->r($this->subjectNames),
                    'grade'  => round(1.0 + (rand(0,200)/100), 2),
                    'units'  => 3,
                ]);
            }

            // Internship (15% chance)
            if (rand(0,6) === 0) {
                StudentInternship::create([
                    'student_id'          => $student->id,
                    'company_name'        => $this->r(['TechCorp Inc.','NetSolutions','CodeWorks PH','DataForge','SoftLabs']),
                    'company_address'     => $this->r(['Makati','Taguig','Pasig','Alabang']),
                    'supervisor_name'     => $this->r($this->firstNames) . ' Supervisor',
                    'supervisor_contact'  => '09' . rand(100000000, 999999999),
                    'role'                => $this->r(['IT Intern','Web Developer Intern','Data Analyst Intern']),
                    'duration'            => rand(200,500) . ' Hours',
                    'status'              => $this->r(['Ongoing','Completed']),
                ]);
            }

            // Guardian
            StudentGuardian::create([
                'student_id'     => $student->id,
                'name'           => $this->r($this->firstNames) . ' ' . $lastName,
                'relationship'   => $this->r(['Mother','Father','Guardian']),
                'contact_number' => '09' . rand(100000000, 999999999),
                'address'        => 'Brgy. ' . $this->r($this->brgy) . ', Cabuyao City, Laguna',
            ]);

            // Medical
            StudentMedical::create([
                'student_id'       => $student->id,
                'blood_type'       => $this->r($this->bloodTypes),
                'allergies'        => rand(0,2) ? ['None'] : [$this->r(['Peanuts','Shellfish','Dust','Pollen'])],
                'conditions'       => rand(0,4) ? ['None'] : [$this->r(['Asthma','Allergy'])],
                'emergency_contact'=> $this->r($this->firstNames) . ' ' . $lastName,
                'emergency_number' => '09' . rand(100000000, 999999999),
            ]);
        }

        // Ensure Basketball/Programming quotas are exactly met
        $this->fillSkillQuota('Basketball', 'Sports', $basketballTarget);
        $this->fillSkillQuota('Programming', 'Programming', $programmingTarget);
    }

    private function fillSkillQuota(string $skill, string $category, int $target): void
    {
        $current = StudentSkill::where('name', $skill)->count();
        if ($current >= $target) return;

        $need = $target - $current;
        $studentsWithout = Student::whereDoesntHave('skills', fn($q) => $q->where('name', $skill))
            ->inRandomOrder()->limit($need)->get();

        foreach ($studentsWithout as $student) {
            StudentSkill::create([
                'student_id'  => $student->id,
                'name'        => $skill,
                'category'    => $category,
                'proficiency' => ['Beginner','Intermediate','Advanced'][array_rand(['Beginner','Intermediate','Advanced'])],
            ]);
        }
    }
}


class FacultySeeder extends Seeder
{
    private $firstNames = ['Dr. Ricardo','Ms. Sarah','Engr. Michael','Prof. Anna','Mr. David',
        'Dra. Liza','Engr. John','Prof. Elena','Ms. Carla','Dr. Mark','Engr. Leo','Prof. Grace',
        'Mr. Ryan','Ms. Jane','Dr. Robert','Prof. Mary'];
    private $lastNames = ['Gomez','Concepcion','Ross','Lim','Tan','Sy','Wang','Lee','Chen','Park',
        'Santos','Reyes','Garcia','Torres','Rivera','Cruz'];
    private $departments = ['College of Computing and Engineering','College of Arts and Sciences',
        'College of Business Administration','College of Education','College of Nursing'];
    private $skills = ['Artificial Intelligence','Machine Learning','Data Science','Web Development',
        'Networking','Literature','Mathematics','Accounting','Nursing','Education','Civil Engineering'];
    private $degrees = ['PhD in Computer Science - UP Diliman','MS Computer Science - DLSU',
        'MA Literature - UST','BS Electrical Engineering - PnC','BS Nursing - PnC','BS Accountancy - PnC'];
    private $ranks = ['Instructor I','Instructor II','Assistant Professor I','Associate Professor I','Professor I','Professor III'];
    private $brgy = ['Sala','Niugan','Cabuyao City','Santa Rosa','Biñan'];

    private function r(array $arr) { return $arr[array_rand($arr)]; }
    private function rd(string $from, string $to): string {
        return date('Y-m-d', rand(strtotime($from), strtotime($to)));
    }

    public function run(): void
    {
        Faculty::query()->delete();

        $usedEmails = [];
        for ($i = 0; $i < 200; $i++) {
            $firstName = $this->r($this->firstNames);
            $lastName = $this->r($this->lastNames);
            $fullName = "$firstName $lastName";
            $emailBase = strtolower(str_replace([' ', '.'], ['.', ''], $fullName));
            $email = $emailBase . ($i + 1) . '@pnc.edu.ph';
            while (in_array($email, $usedEmails)) {
                $email = $emailBase . rand(1,999) . '@pnc.edu.ph';
            }
            $usedEmails[] = $email;

            $faculty = Faculty::create([
                'faculty_id'        => 'PROF-' . $this->rd('2005-01-01','2020-12-31') . '-' . str_pad($i+1, 3, '0', STR_PAD_LEFT),
                'full_name'         => $fullName,
                'gender'            => rand(0,1) ? 'Male' : 'Female',
                'birthdate'         => $this->rd('1970-01-01', '1990-12-31'),
                'civil_status'      => rand(0,1) ? 'Married' : 'Single',
                'nationality'       => 'Filipino',
                'email'             => $email,
                'contact_number'    => '09' . rand(100000000, 999999999),
                'address'           => $this->r($this->brgy) . ', Laguna',
                'department'        => $this->r($this->departments),
                'profile_photo'     => 'https://ui-avatars.com/api/?name=' . urlencode($fullName) . '&background=' . dechex(rand(0x100000, 0xFFFFFF)) . '&color=fff&size=200',
                'years_experience'  => rand(5, 30),
                'employment_status' => rand(0,2) === 0 ? 'Part-time' : 'Full-time',
                'academic_rank'     => $this->r($this->ranks),
                'date_hired'        => $this->rd('2010-01-01', '2023-12-31'),
            ]);

            // Skills (2-4)
            $shuffled = $this->skills;
            shuffle($shuffled);
            foreach (array_slice($shuffled, 0, rand(2, 4)) as $skill) {
                FacultySkill::create(['faculty_id' => $faculty->id, 'skill' => $skill]);
            }

            // Education (1-2)
            $eduShuffled = $this->degrees;
            shuffle($eduShuffled);
            foreach (array_slice($eduShuffled, 0, rand(1, 2)) as $deg) {
                FacultyEducation::create(['faculty_id' => $faculty->id, 'degree' => $deg]);
            }

            // Subjects (2-4)
            $usedCodes = [];
            for ($j = 0; $j < rand(2, 4); $j++) {
                $code = 'CS' . rand(100, 499);
                while (in_array($code, $usedCodes)) { $code = 'CS' . rand(100, 499); }
                $usedCodes[] = $code;
                FacultySubject::create(['faculty_id' => $faculty->id, 'subject_code' => $code]);
            }
        }
    }
}


class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::query()->delete();
        $events = [
            ['title' => 'CCS Tech Summit 2024', 'description' => 'Annual technology summit for computing students.', 'date' => '2024-03-18', 'start_time' => '07:00', 'end_time' => '17:00', 'location' => 'CCS Auditorium', 'organizer' => 'Academic Affairs', 'category' => 'Academic', 'status' => 'Completed'],
            ['title' => 'Intramurals 2024', 'description' => 'Annual sports fest for all colleges.', 'date' => '2024-04-10', 'start_time' => '06:00', 'end_time' => '18:00', 'location' => 'PnC Sports Complex', 'organizer' => 'Student Affairs', 'category' => 'Sports', 'status' => 'Completed'],
            ['title' => 'Web Dev Workshop', 'description' => 'Hands-on workshop on modern web development.', 'date' => '2024-05-15', 'start_time' => '08:00', 'end_time' => '17:00', 'location' => 'Computer Lab 1', 'organizer' => 'IT Society', 'category' => 'Workshop', 'status' => 'Upcoming'],
            ['title' => 'Acquaintance Party', 'description' => 'Welcome party for new CCS students.', 'date' => '2024-06-01', 'start_time' => '16:00', 'end_time' => '21:00', 'location' => 'CCS Grounds', 'organizer' => 'Student Council', 'category' => 'Social', 'status' => 'Upcoming'],
            ['title' => 'Foundation Day', 'description' => 'PnC Foundation Day Celebration.', 'date' => '2024-09-21', 'start_time' => '08:00', 'end_time' => '18:00', 'location' => 'Main Campus', 'organizer' => 'Administration', 'category' => 'Academic', 'status' => 'Upcoming'],
        ];
        foreach ($events as $e) { Event::create($e); }
    }
}


class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        Schedule::query()->delete();
        $schedules = [
            ['subject_code' => 'CS311', 'subject_name' => 'Data Structures', 'room' => 'CL1', 'day' => 'Monday', 'start_time' => '08:00', 'end_time' => '11:00', 'faculty_id' => 'PROF-2010-001', 'section' => 'CS3A', 'type' => 'Regular'],
            ['subject_code' => 'CS312', 'subject_name' => 'Algorithms', 'room' => 'CL2', 'day' => 'Wednesday', 'start_time' => '13:00', 'end_time' => '16:00', 'faculty_id' => 'PROF-2010-001', 'section' => 'CS3A', 'type' => 'Laboratory'],
            ['subject_code' => 'IT201', 'subject_name' => 'Web Development', 'room' => 'CL3', 'day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '12:00', 'faculty_id' => 'PROF-2010-002', 'section' => 'IT2B', 'type' => 'Regular'],
            ['subject_code' => 'CS401', 'subject_name' => 'Capstone Project', 'room' => 'CL4', 'day' => 'Friday', 'start_time' => '13:00', 'end_time' => '17:00', 'faculty_id' => 'PROF-2010-001', 'section' => 'CS4A', 'type' => 'Regular'],
        ];
        foreach ($schedules as $s) { Schedule::create($s); }
    }
}
