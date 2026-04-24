<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('skills');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('student_id', 'like', "%{$request->search}%");
            });
        }
        if ($request->program && $request->program !== 'All') {
            $query->where('academic_program', $request->program);
        }
        if ($request->skill && $request->skill !== 'All') {
            $query->whereHas('skills', fn($q) => $q->where('name', $request->skill));
        }
        if ($request->status && $request->status !== 'All') {
            $query->where('enrollment_status', $request->status);
        }

        $students = $query->orderBy('full_name')->paginate(20)->withQueryString();
        $programs = Student::distinct()->pluck('academic_program')->sort()->values();
        $skills   = \App\Models\StudentSkill::distinct()->pluck('name')->sort()->values();

        return view('students.index', compact('students', 'programs', 'skills'));
    }

    public function show(Student $student)
    {
        $student->load(['skills', 'achievements', 'organizations', 'violations', 'academicRecords.grades', 'internship', 'guardian', 'medical']);
        return view('students.show', compact('student'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'       => 'required|unique:students,student_id',
            'full_name'        => 'required|string|max:255',
            'email'            => 'required|email|unique:students,email',
            'contact_number'   => 'required',
            'academic_program' => 'required',
            'year_level'       => 'required|integer|between:1,4',
            'section'          => 'required',
            'enrollment_status'=> 'required',
            'gender'           => 'required',
            'birthdate'        => 'required|date',
            'address'          => 'required',
        ]);

        $validated['date_enrolled']  = now()->toDateString();
        $validated['admission_type'] = $request->admission_type ?? 'Regular';
        $validated['profile_photo']  = 'https://ui-avatars.com/api/?name=' . urlencode($validated['full_name']) . '&background=f97316&color=fff&size=200';

        $student = Student::create($validated);

        // Add skills if provided
        if ($request->skills) {
            foreach ($request->skills as $skill) {
                if (!empty($skill['name'])) {
                    $student->skills()->create($skill);
                }
            }
        }

        ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Added new student', 'target' => $student->full_name, 'module' => 'Student Information']);

        return redirect()->route('students.show', $student)->with('success', 'Student record created successfully!');
    }

    public function edit(Student $student)
    {
        $student->load('skills');
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'full_name'        => 'required|string|max:255',
            'email'            => 'required|email|unique:students,email,' . $student->id,
            'contact_number'   => 'required',
            'academic_program' => 'required',
            'year_level'       => 'required|integer|between:1,4',
            'section'          => 'required',
            'enrollment_status'=> 'required',
            'gender'           => 'required',
            'birthdate'        => 'required|date',
            'address'          => 'required',
        ]);

        $student->update($validated);

        // Sync skills
        if ($request->has('skills')) {
            $student->skills()->delete();
            foreach ($request->skills as $skill) {
                if (!empty($skill['name'])) {
                    $student->skills()->create($skill);
                }
            }
        }

        ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Updated student profile', 'target' => $student->full_name, 'module' => 'Student Information']);

        return redirect()->route('students.show', $student)->with('success', 'Student record updated successfully!');
    }

    public function destroy(Student $student)
    {
        $name = $student->full_name;
        $student->delete();
        ActivityLog::create(['user_id' => auth()->id(), 'user_name' => auth()->user()->name, 'action' => 'Deleted student record', 'target' => $name, 'module' => 'Student Information']);
        return redirect()->route('students.index')->with('success', 'Student record deleted.');
    }

    // Student self-view
    public function myProfile()
    {
        $user    = auth()->user();
        $student = Student::where('email', $user->email)
            ->with(['skills', 'achievements', 'organizations', 'violations', 'academicRecords.grades', 'internship', 'guardian', 'medical'])
            ->first();
        return view('students.show', compact('student'));
    }
}
